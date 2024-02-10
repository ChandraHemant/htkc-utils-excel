<?php

namespace ChandraHemant\HtkcUtilsExcel\Mixins;

use Illuminate\Database\Eloquent\Builder;
use ChandraHemant\HtkcUtilsExcel\Concerns\Exportable;
use ChandraHemant\HtkcUtilsExcel\Concerns\FromQuery;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithHeadings;
use ChandraHemant\HtkcUtilsExcel\Sheet;

class DownloadQueryMacro
{
    public function __invoke()
    {
        return function (string $fileName, string $writerType = null, $withHeadings = false) {
            $export = new class($this, $withHeadings) implements FromQuery, WithHeadings
            {
                use Exportable;

                /**
                 * @var bool
                 */
                private $withHeadings;

                /**
                 * @var Builder
                 */
                private $query;

                /**
                 * @param  $query
                 * @param  bool  $withHeadings
                 */
                public function __construct($query, bool $withHeadings = false)
                {
                    $this->query        = $query;
                    $this->withHeadings = $withHeadings;
                }

                /**
                 * @return Builder
                 */
                public function query()
                {
                    return $this->query;
                }

                /**
                 * @return array
                 */
                public function headings(): array
                {
                    if (!$this->withHeadings) {
                        return [];
                    }

                    $firstRow = (clone $this->query)->first();

                    if ($firstRow) {
                        return array_keys(Sheet::mapArraybleRow($firstRow));
                    }

                    return [];
                }
            };

            return $export->download($fileName, $writerType);
        };
    }
}
