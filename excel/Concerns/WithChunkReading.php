<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

interface WithChunkReading
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
