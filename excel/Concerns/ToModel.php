<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Illuminate\Database\Eloquent\Model;

interface ToModel
{
    /**
     * @param  array  $row
     * @return Model|Model[]|null
     */
    public function model(array $row);
}
