<?php

namespace ChandraHemant\HtkcUtilsExcel\Events;

use ChandraHemant\HtkcUtilsExcel\Writer;

class BeforeWriting extends Event
{
    /**
     * @var Writer
     */
    public $writer;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param  Writer  $writer
     * @param  object  $exportable
     */
    public function __construct(Writer $writer, $exportable)
    {
        $this->writer     = $writer;
        parent::__construct($exportable);
    }

    /**
     * @return Writer
     */
    public function getWriter(): Writer
    {
        return $this->writer;
    }

    /**
     * @return mixed
     */
    public function getDelegate()
    {
        return $this->writer;
    }
}
