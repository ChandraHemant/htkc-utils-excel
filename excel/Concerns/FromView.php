<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use Illuminate\Contracts\View\View;

interface FromView
{
    /**
     * @return View
     */
    public function view(): View;
}
