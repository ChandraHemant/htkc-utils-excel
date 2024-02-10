<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface SkipsUnknownSheets
{
    /**
     * @param  string|int  $sheetName
     */
    public function onUnknownSheet($sheetName);
}
