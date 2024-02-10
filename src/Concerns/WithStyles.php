<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface WithStyles
{
    public function styles(Worksheet $sheet);
}
