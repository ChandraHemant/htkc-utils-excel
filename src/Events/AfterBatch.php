<?php

namespace ChandraHemant\HtkcUtilsExcel\Events;

use ChandraHemant\HtkcUtilsExcel\Imports\ModelManager;

class AfterBatch extends Event
{
    /**
     * @var ModelManager
     */
    public $manager;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var int
     */
    private $startRow;

    /**
     * @param  ModelManager  $manager
     * @param  object  $importable
     * @param  int  $batchSize
     * @param  int  $startRow
     */
    public function __construct(ModelManager $manager, $importable, int $batchSize, int $startRow)
    {
        $this->manager   = $manager;
        $this->batchSize = $batchSize;
        $this->startRow  = $startRow;
        parent::__construct($importable);
    }

    public function getManager(): ModelManager
    {
        return $this->manager;
    }

    /**
     * @return mixed
     */
    public function getDelegate()
    {
        return $this->manager;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function getStartRow(): int
    {
        return $this->startRow;
    }
}
