<?php

namespace ChandraHemant\HtkcUtilsExcel;

use Illuminate\Support\Collection;
use ChandraHemant\HtkcUtilsExcel\Concerns\ToArray;
use ChandraHemant\HtkcUtilsExcel\Concerns\ToCollection;
use ChandraHemant\HtkcUtilsExcel\Concerns\ToModel;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithCalculatedFormulas;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithFormatData;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithMappedCells;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MappedReader
{
    /**
     * @param  WithMappedCells  $import
     * @param  Worksheet  $worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function map(WithMappedCells $import, Worksheet $worksheet)
    {
        $mapped = $import->mapping();
        array_walk_recursive($mapped, function (&$coordinate) use ($import, $worksheet) {
            $cell = Cell::make($worksheet, $coordinate);

            $coordinate = $cell->getValue(
                null,
                $import instanceof WithCalculatedFormulas,
                $import instanceof WithFormatData
            );
        });

        if ($import instanceof ToModel) {
            $model = $import->model($mapped);

            if ($model) {
                $model->saveOrFail();
            }
        }

        if ($import instanceof ToCollection) {
            $import->collection(new Collection($mapped));
        }

        if ($import instanceof ToArray) {
            $import->array($mapped);
        }
    }
}
