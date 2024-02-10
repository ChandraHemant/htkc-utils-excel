<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface WithBatchInserts
{
    /**
     * @return int
     */
    public function batchSize(): int;
}
