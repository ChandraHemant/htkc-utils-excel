<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Illuminate\Support\Collection;

interface FromCollection
{
    /**
     * @return Collection
     */
    public function collection();
}
