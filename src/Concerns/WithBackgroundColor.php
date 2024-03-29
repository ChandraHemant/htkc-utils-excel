<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use PhpOffice\PhpSpreadsheet\Style\Color;

interface WithBackgroundColor
{
    /**
     * @return string|array|Color
     */
    public function backgroundColor();
}
