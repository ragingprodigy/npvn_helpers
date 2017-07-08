<?php
declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: dapo
 * Date: 7/7/17
 * Time: 8:36 PM
 */

namespace App\Jobs;


use App\Models\Task;
use Illuminate\Contracts\Bus\Dispatcher;

class SaveUserTaskToDatabaseJob extends AbstractJob
{
    public $tries = 3;

    /** @var string $taskName */
    private $taskName;

    /** @var string $jobClassName */
    private $jobClassName;

    /** @var array $jobArguments */
    private $jobArguments;

    /** @var  string $taskQueue */
    private $taskQueue;

    /**
     * SaveUserTaskToDatabase constructor.
     *
     * @param string $taskName
     * @param string $jobClassName
     * @param array  $jobArgs
     * @param string $queue
     */
    public function __construct(
        string $taskName,
        string $jobClassName,
        array $jobArgs = [],
        $queue = 'default'
    ) {
        parent::__construct();

        $this->taskName     = $taskName;
        $this->jobClassName = $jobClassName;
        $this->jobArguments = $jobArgs;
        $this->taskQueue    = $queue;
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function handle(Dispatcher $dispatcher): void
    {
        /** @var Task $task */
        $task = new Task([
            'name'      => $this->taskName,
            'status'    => Task::CREATED
        ]);

        if (!$task->save()) {
            $this->fail(
                new \Exception('Unable to Create new user task')
            );
        }

        // Dispatch The Job with the ID
        /** @var AbstractJob $job */
        $job = new $this->jobClassName($this->jobArguments, $task->id);
        $job->onQueue($this->taskQueue);

        $dispatcher->dispatch($job);
    }
}