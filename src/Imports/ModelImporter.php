<?php

namespace ChandraHemant\HtkcUtilsExcel\Imports;

use ChandraHemant\HtkcUtilsExcel\Concerns\SkipsEmptyRows;
use ChandraHemant\HtkcUtilsExcel\Concerns\ToModel;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithBatchInserts;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithCalculatedFormulas;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithColumnLimit;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithEvents;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithFormatData;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithMapping;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithProgressBar;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithValidation;
use ChandraHemant\HtkcUtilsExcel\Events\AfterBatch;
use ChandraHemant\HtkcUtilsExcel\HasEventBus;
use ChandraHemant\HtkcUtilsExcel\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ModelImporter
{
    use HasEventBus;

    /**
     * @var ModelManager
     */
    private $manager;

    /**
     * @param  ModelManager  $manager
     */
    public function __construct(ModelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  Worksheet  $worksheet
     * @param  ToModel  $import
     * @param  int|null  $startRow
     * @param  string|null  $endColumn
     *
     * @throws \ChandraHemant\HtkcUtilsExcel\Validators\ValidationException
     */
    public function import(Worksheet $worksheet, ToModel $import, int $startRow = 1)
    {
        if ($startRow > $worksheet->getHighestRow()) {
            return;
        }
        if ($import instanceof WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        $headingRow       = HeadingRowExtractor::extract($worksheet, $import);
        $headerIsGrouped  = HeadingRowExtractor::extractGrouping($headingRow, $import);
        $batchSize        = $import instanceof WithBatchInserts ? $import->batchSize() : 1;
        $endRow           = EndRowFinder::find($import, $startRow, $worksheet->getHighestRow());
        $progessBar       = $import instanceof WithProgressBar;
        $withMapping      = $import instanceof WithMapping;
        $withCalcFormulas = $import instanceof WithCalculatedFormulas;
        $formatData       = $import instanceof WithFormatData;
        $withValidation   = $import instanceof WithValidation && method_exists($import, 'prepareForValidation');
        $endColumn        = $import instanceof WithColumnLimit ? $import->endColumn() : null;

        $this->manager->setRemembersRowNumber(method_exists($import, 'rememberRowNumber'));

        $i             = 0;
        $batchStartRow = $startRow;
        foreach ($worksheet->getRowIterator($startRow, $endRow) as $spreadSheetRow) {
            $i++;

            $row = new Row($spreadSheetRow, $headingRow, $headerIsGrouped);
            if (!$import instanceof SkipsEmptyRows || !$row->isEmpty($withCalcFormulas)) {
                $rowArray = $row->toArray(null, $withCalcFormulas, $formatData, $endColumn);

                if ($import instanceof SkipsEmptyRows && method_exists($import, 'isEmptyWhen') && $import->isEmptyWhen($rowArray)) {
                    continue;
                }

                if ($withValidation) {
                    $rowArray = $import->prepareForValidation($rowArray, $row->getIndex());
                }

                if ($withMapping) {
                    $rowArray = $import->map($rowArray);
                }

                $this->manager->add(
                    $row->getIndex(),
                    $rowArray
                );

                // Flush each batch.
                if (($i % $batchSize) === 0) {
                    $this->flush($import, $batchSize, $batchStartRow);
                    $batchStartRow += $i;
                    $i = 0;

                    if ($progessBar) {
                        $import->getConsoleOutput()->progressAdvance($batchSize);
                    }
                }
            }
        }

        if ($i > 0) {
            // Flush left-overs.
            $this->flush($import, $batchSize, $batchStartRow);
        }
    }

    private function flush(ToModel $import, int $batchSize, int $startRow)
    {
        $this->manager->flush($import, $batchSize > 1);
        $this->raise(new AfterBatch($this->manager, $import, $batchSize, $startRow));
    }
}
