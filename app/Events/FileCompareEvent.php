<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/7/17, 8:57 PM
 */

namespace App\Events;


class FileCompareEvent
{
    private $fileNames;
    private $taskName;
    private $option;

    public function __construct(array $fileNames, string $option, string $taskName)
    {
        $this->fileNames    = $fileNames;
        $this->option       = $option;
        $this->taskName     = $taskName;
    }

    /**
     * @return array
     */
    public function getFileNames(): array
    {
        return $this->fileNames;
    }

    /**
     * @return string
     */
    public function getOption(): string
    {
        return $this->option;
    }

    /**
     * @return string
     */
    public function getTaskName(): string
    {
        return $this->taskName;
    }
}