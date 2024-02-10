<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use ChandraHemant\HtkcUtilsExcel\Row;

interface OnEachRow
{
    /**
     * @param  Row  $row
     */
    public function onRow(Row $row);
}
