<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface WithCustomChunkSize
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
