<?php

namespace ChandraHemant\HtkcUtilsExcel;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Collection;
use ChandraHemant\HtkcUtilsExcel\Concerns\ShouldQueueWithoutChain;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithChunkReading;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithEvents;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithLimit;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithProgressBar;
use ChandraHemant\HtkcUtilsExcel\Files\TemporaryFile;
use ChandraHemant\HtkcUtilsExcel\Imports\HeadingRowExtractor;
use ChandraHemant\HtkcUtilsExcel\Jobs\AfterImportJob;
use ChandraHemant\HtkcUtilsExcel\Jobs\QueueImport;
use ChandraHemant\HtkcUtilsExcel\Jobs\ReadChunk;
use Throwable;

class ChunkReader
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  WithChunkReading  $import
     * @param  Reader  $reader
     * @param  TemporaryFile  $temporaryFile
     * @return PendingDispatch|Collection|null
     */
    public function read(WithChunkReading $import, Reader $reader, TemporaryFile $temporaryFile)
    {
        if ($import instanceof WithEvents) {
            $reader->beforeImport($import);
        }

        $chunkSize    = $import->chunkSize();
        $totalRows    = $reader->getTotalRows();
        $worksheets   = $reader->getWorksheets($import);
        $queue        = property_exists($import, 'queue') ? $import->queue : null;
        $delayCleanup = property_exists($import, 'cleanupInterval') ? $import->cleanupInterval : 60;

        if ($import instanceof WithProgressBar) {
            $import->getConsoleOutput()->progressStart(array_sum($totalRows));
        }

        $jobs = new Collection();
        foreach ($worksheets as $name => $sheetImport) {
            $startRow = HeadingRowExtractor::determineStartRow($sheetImport);

            if ($sheetImport instanceof WithLimit) {
                $limit = $sheetImport->limit();

                if ($limit <= $totalRows[$name]) {
                    $totalRows[$name] = $sheetImport->limit();
                }
            }

            for ($currentRow = $startRow; $currentRow <= $totalRows[$name]; $currentRow += $chunkSize) {
                $jobs->push(new ReadChunk(
                    $import,
                    $reader->getPhpSpreadsheetReader(),
                    $temporaryFile,
                    $name,
                    $sheetImport,
                    $currentRow,
                    $chunkSize
                ));
            }
        }

        $afterImportJob = new AfterImportJob($import, $reader);

        if ($import instanceof ShouldQueueWithoutChain) {
            $afterImportJob->setInterval($delayCleanup);
            $afterImportJob->setDependencies($jobs);
            $jobs->push($afterImportJob->delay($delayCleanup));

            return $jobs->each(function ($job) use ($queue) {
                dispatch($job->onQueue($queue));
            });
        }

        $jobs->push($afterImportJob);

        if ($import instanceof ShouldQueue) {
            return new PendingDispatch(
                (new QueueImport($import))->chain($jobs->toArray())
            );
        }

        $jobs->each(function ($job) {
            try {
                function_exists('dispatch_now')
                    ? dispatch_now($job)
                    : $this->dispatchNow($job);
            } catch (Throwable $e) {
                if (method_exists($job, 'failed')) {
                    $job->failed($e);
                }
                throw $e;
            }
        });

        if ($import instanceof WithProgressBar) {
            $import->getConsoleOutput()->progressFinish();
        }

        unset($jobs);

        return null;
    }

    /**
     * Dispatch a command to its appropriate handler in the current process without using the synchronous queue.
     *
     * @param  object  $command
     * @param  mixed  $handler
     * @return mixed
     */
    protected function dispatchNow($command, $handler = null)
    {
        $uses = class_uses_recursive($command);

        if (in_array(InteractsWithQueue::class, $uses) &&
            in_array(Queueable::class, $uses) && !$command->job
        ) {
            $command->setJob(new SyncJob($this->container, json_encode([]), 'sync', 'sync'));
        }

        $method = method_exists($command, 'handle') ? 'handle' : '__invoke';

        return $this->container->call([$command, $method]);
    }
}
