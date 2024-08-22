<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Generator;

interface FromGenerator
{
    /**
     * @return Generator
     */
    public function generator(): Generator;
}
