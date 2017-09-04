<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/7/17, 8:43 PM
 */

namespace App\Jobs;


use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class FileCompareJob extends AbstractJob
{
    /**
     * @var array $fileNames
     */
    private $fileNames;

    /** @var  string $compareOption */
    private $compareOption;

    /** @var int $tries */
    public $tries = 3;

    /**
     * FileCompareJob constructor.
     * @param array $arguments
     * @param int $taskId
     */
    public function __construct(array $arguments, $taskId = -1)
    {
        parent::__construct($taskId);

        $this->fileNames        = $arguments[0];
        $this->compareOption    = $arguments[1];
    }

    public function handle(): void
    {
        $this->starting();

        // Read File Contents
        $fileContents = [];

        \Log::info('Reading files...' . print_r($this->fileNames, true));
        foreach ($this->fileNames as $k => $name) {
            Excel::load('storage/app/' . $name, function (LaravelExcelReader $reader) use (&$fileContents) {
                $fileContents[] = $reader->get();
            });
            \Log::info('File ' . $k . ' read...');
        }

        $setOne = collect($fileContents[0])->keyBy('bvn');
        $setTwo = collect($fileContents[1])->keyBy('bvn');

        $opName = '';

        switch($this->compareOption) {
            case 'A':
                \Log::info('Performing A\'');
                $opName = 'A\' ';
                // Get records only in set One
                $newData = collect($setOne->filter(function ($row) use ($setTwo) {
                    return $setTwo->get($row->bvn) === null;
                }));
                \Log::info(sprintf('Retrieved %d records', $newData->count()));
                break;
            case 'B':
                // Keep set One constant and get records unique to set Two
                \Log::info('Performing B\'');
                $opName = 'B\' ';
                $newData = collect($setTwo->filter(function ($row) use ($setOne) {
                    return $setOne->get($row->bvn) === null;
                }));
                \Log::info(sprintf('Retrieved %d records', $newData->count()));
                break;
            case 'C':
                // Fetch A n B
                $opName = 'A n B ';
                \Log::info('Performing A n B');
                $newData = $setOne->intersectKey($setTwo);
                \Log::info(sprintf('Retrieved %d records', $newData->count()));
                break;
            case 'D':
            default:
                // Fetch A u B
                $opName = 'A u B ';
                \Log::info('Performing A U B');
                $newData = $setTwo->union($setOne)->unique('bvn');
                \Log::info(sprintf('Retrieved %d records', $newData->count()));
                break;
        }

        // Free-Up Vars
        $setOne = null;
        $setTwo = null;
        $fileContents = [];

        $exportFileName = 'merged_data_' . $opName . microtime();
        $outDir = base_path('storage/exports');

        Excel::create($exportFileName, function (LaravelExcelWriter $excel) use ($newData, $outDir, $exportFileName) {
            $excel->sheet('DATA', function(LaravelExcelWorksheet $sheet) use ($newData) {
                $sheet->with($this->useInSheet($newData->all()));
            });

            $this->finished($outDir . '/' . $exportFileName . '.xlsx');
        })->store('xlsx');

        // Delete files
        unlink(base_path('storage/app/' . $this->fileNames[0]));
        unlink(base_path('storage/app/' . $this->fileNames[1]));
    }

    private function useInSheet($data)
    {
        return json_decode(json_encode($data), true);
    }
}