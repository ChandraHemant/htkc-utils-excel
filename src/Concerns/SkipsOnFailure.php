<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use ChandraHemant\HtkcUtilsExcel\Validators\Failure;

interface SkipsOnFailure
{
    /**
     * @param  Failure[]  $failures
     */
    public function onFailure(Failure ...$failures);
}
