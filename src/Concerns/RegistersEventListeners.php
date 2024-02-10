<?php

namespace ChandraHemant\HtkcUtilsExcel\Concerns;

use ChandraHemant\HtkcUtilsExcel\Events\AfterBatch;
use ChandraHemant\HtkcUtilsExcel\Events\AfterChunk;
use ChandraHemant\HtkcUtilsExcel\Events\AfterImport;
use ChandraHemant\HtkcUtilsExcel\Events\AfterSheet;
use ChandraHemant\HtkcUtilsExcel\Events\BeforeExport;
use ChandraHemant\HtkcUtilsExcel\Events\BeforeImport;
use ChandraHemant\HtkcUtilsExcel\Events\BeforeSheet;
use ChandraHemant\HtkcUtilsExcel\Events\BeforeWriting;
use ChandraHemant\HtkcUtilsExcel\Events\ImportFailed;

trait RegistersEventListeners
{
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $listenersClasses = [
            BeforeExport::class  => 'beforeExport',
            BeforeWriting::class => 'beforeWriting',
            BeforeImport::class  => 'beforeImport',
            AfterImport::class   => 'afterImport',
            AfterBatch::class    => 'afterBatch',
            AfterChunk::class    => 'afterChunk',
            ImportFailed::class  => 'importFailed',
            BeforeSheet::class   => 'beforeSheet',
            AfterSheet::class    => 'afterSheet',
        ];
        $listeners = [];

        foreach ($listenersClasses as $class => $name) {
            // Method names are case insensitive in php
            if (method_exists($this, $name)) {
                // Allow methods to not be static
                $listeners[$class] = [$this, $name];
            }
        }

        return $listeners;
    }
}
