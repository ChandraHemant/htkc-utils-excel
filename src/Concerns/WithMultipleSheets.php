<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array;
}
