<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Iterator;

interface FromIterator
{
    /**
     * @return Iterator
     */
    public function iterator(): Iterator;
}
