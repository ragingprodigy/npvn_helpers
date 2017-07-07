<?php
/**
 * Created by PhpStorm.
 * User: dapo
 * Date: 7/7/17
 * Time: 8:34 PM
 */

namespace App\Jobs;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class AbstractJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $taskId = -1;

    public function __construct($taskId = -1)
    {
        $this->taskId = $taskId;
    }

    /**
     * Custom LifeCycle Event
     */
    protected function starting(): void
    {
        \Log::info('Starting Job...');

        if ($this->taskId !== -1) {
            Task::whereId($this->taskId)->update([ 'status' => Task::PROCESSING ]);
        }
    }

    /**
     * @param string $outFile
     */
    protected function finished(string $outFile = ''): void
    {
        \Log::info('Finished Job...');

        if ($this->taskId !== -1) {
            Task::whereId($this->taskId)->update([
                'status'       => Task::FINISHED,
                'finished_at'  => Carbon::now(),
                'output_file'  => $outFile
            ]);
        }
    }
}