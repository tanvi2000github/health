<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Queue\Worker;
use Queue as IlluminateQueue;
use Illuminate\Queue\WorkerOptions;

class Queue extends Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        IlluminateQueue::pushOn($this->resource['name'], instantiate($this->resource['test_job']));

        $worker = instantiate(Worker::class);

        $connection = $this->resource['connection'] ?: app('config')['queue.default'];

        $queue = app('config')->get(
            "queue.connections.{$connection}.queue", 'default'
        );

        $worker->setCache(instantiate($this->resource['cache_instance'])->driver());

        $worker->runNextJob(
            $connection, $queue, $this->gatherWorkerOptions()
        );

        return $this->makeResult(true);
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        return new WorkerOptions(
            0, 0,
            0, 0,
            0, false
        );
    }
}
