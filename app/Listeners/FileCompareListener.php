<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/7/17, 8:56 PM
 */

namespace App\Listeners;


use App\Events\FileCompareEvent;
use App\Jobs\FileCompareJob;
use App\Jobs\SaveUserTaskToDatabaseJob;
use Carbon\Carbon;

class FileCompareListener
{
    /**
     * @param FileCompareEvent $compareEvent
     */
    public function handle(FileCompareEvent $compareEvent): void
    {
        $taskName =  implode(
            ' ',
            [
                'Data export for',
                $compareEvent->getTaskName(),
                'Requested On',
                Carbon::now()->toDateTimeString()
            ]
        );

        $params = [
            $compareEvent->getFileNames(),
            $compareEvent->getOption()
        ];

        // Dispatch Job to Create User Task
        $job = new SaveUserTaskToDatabaseJob($taskName, FileCompareJob::class, $params);

        dispatch($job);
    }
}