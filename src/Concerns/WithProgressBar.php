<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Illuminate\Console\OutputStyle;

interface WithProgressBar
{
    /**
     * @return OutputStyle
     */
    public function getConsoleOutput(): OutputStyle;
}
