<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface WithUpserts
{
    /**
     * @return string|array
     */
    public function uniqueBy();
}
