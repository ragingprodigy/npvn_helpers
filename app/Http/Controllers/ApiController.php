<?php
declare(strict_types = 1);

/**
 * @author Oladapo Omonayajo <oladapo.omonayajo@lazada.com.ph>
 * Created on 7/6/2017, 01:44
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ApiController extends Controller
{
    /**
     * @return mixed
     */
    public function compareSample()
    {
        return Excel::create('sample_file', function ($writer) {
            $writer->sheet('NAMES', function ($sheet) {
                $data = collect();
                $data->push([
                    'SN' => '1',
                    'FIRST NAME' => '-',
                    'MIDDLE NAME' => '-',
                    'LAST NAME' => '-',
                ]);

                $data->push([
                    'SN' => '2',
                    'FIRST NAME' => '-',
                    'MIDDLE NAME' => '-',
                    'LAST NAME' => '-',
                ]);

                $sheet->with($this->useInSheet($data->all()));
            });
        })->download('xlsx');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function mergeSheets(Request $request)
    {
        $fileContents = $this->fetchFilesFromRequest($request);

        $setOne = collect($fileContents[0])->keyBy('bvn');
        $setTwo = collect($fileContents[1])->keyBy('bvn');

        $option = $request->get('option', 'A');
        switch($option) {
            case 'A':
                // Get records only in set One
                $newData = collect($setOne->filter(function ($row) use ($setTwo) {
                    return $setTwo->get($row->bvn) === null;
                }));
                break;
            case 'B':
                // Keep set One constant and get records unique to set Two
                $newData = collect($setTwo->filter(function ($row) use ($setOne) {
                    return $setOne->get($row->bvn) === null;
                }));
                break;
            case 'C':
                // Fetch A n B
                $newData = $setOne->intersectKey($setTwo);
                break;
            case 'D':
            default:
                // Fetch A u B
                $newData = $setTwo->union($setOne)->unique('bvn');
                break;
        }

        return Excel::create('merged_data', function (LaravelExcelWriter $excel) use ($newData) {
            $excel->sheet('DATA', function(LaravelExcelWorksheet $sheet) use ($newData) {
                $sheet->with($this->useInSheet($newData->all()));
            });
        })->download('xlsx');
    }

    private function useInSheet($data)
    {
        return json_decode(json_encode($data), true);
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function processUpload(Request $request)
    {
        $path = 'storage/app/' . $request->file('theFile')->store('bvn');

        Excel::load($path, function (LaravelExcelReader $reader) {
            $results = $reader->get();
            $data = [];
            foreach ($results as $key => $row) {
                $fName = $row['firstname'] === null ? '' : strtolower(trim($row['firstname']));
                $mName = $row['middlename'] === null ? '' : strtolower(trim($row['middlename']));
                $lName = $row['lastname'] === null ? '' : strtolower(trim($row['lastname']));

                $rNames = [
                    strtolower(trim($row['resolved_firstname'])),
                    strtolower(trim($row['resolved_lastname']))
                ];

                if (in_array($fName, $rNames, false) || in_array($lName, $rNames, false)) {
                    $row['classification'] = 'B';
                } elseif (!in_array($fName, $rNames, false)
                    && !in_array($lName, $rNames, false)
                    && !in_array($mName, $rNames, false)
                ) {
                    $row['classification'] = 'A';
                } elseif (in_array($mName, $rNames, false)) {
                    $row['classification'] = 'C';
                }

                $data[$key] = $row;
            }

            Excel::create('processed', function ($excel) use ($data) {
                $excel->sheet('PROCESSED_DATA', function ($sheet) use ($data) {
                    $sheet->with($this->useInSheet($data));
                });
            })->download('xlsx');
        });
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function compareSheets(Request $request)
    {
        $fileContents = $this->fetchFilesFromRequest($request);

        $setOne = collect($fileContents[0])->keyBy('bvn');
        $setTwo = collect($fileContents[1])->keyBy('bvn');
        $newData = collect([]);

        $setOne->each(function ($volunteer, $key) use (&$newData, $setTwo) {
            \Log::info(print_r($volunteer->first_name, true));

            $coRecord = $setTwo->get($volunteer->bvn);
            $names1 = [
                $volunteer->first_name === null ? '' : strtoupper($volunteer->first_name),
                $volunteer->middle_name === null ? '' : strtoupper($volunteer->middle_name),
                $volunteer->last_name === null ? '' : strtoupper($volunteer->last_name)
            ];

            $names2 = [
                $coRecord->first_name === null ? '' : strtoupper($coRecord->first_name),
                $coRecord->middle_name === null ? '' : strtoupper($coRecord->middle_name),
                $coRecord->last_name === null ? '' : strtoupper($coRecord->last_name)
            ];

            $matches = 0;

            foreach ($names1 as $name) {
                if (in_array($name, $names2)) {
                    $matches += 1;
                }
            }

            $newData->push([
                'BVN'   => $key,
                'FIRST NAME' => $volunteer->first_name,
                'MIDDLE NAME' => $volunteer->middle_name,
                'LAST NAME' => $volunteer->last_name,
                'MATCHED NAMES' => $matches
            ]);
        });

        return Excel::create('resolved_names', function (LaravelExcelWriter $excel) use ($newData) {
            $excel->sheet('Resolved Name Issues', function ($sheet) use ($newData) {
                $sheet->with($newData->all());
            });
        })->download('xlsx');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fetchFilesFromRequest(Request $request)
    {
        /** @var UploadedFile[] $files */
        $files = $request->allFiles();
        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $file->store('destination');
        }

        $fileContents = [];

        foreach ($fileNames as $name) {
            Excel::load('storage/app/' . $name, function (LaravelExcelReader $reader) use (&$fileContents) {
                $fileContents[] = $reader->get();
            });
        }

        unlink(base_path('storage/app/' . $fileNames[0]));
        unlink(base_path('storage/app/' . $fileNames[1]));

        return $fileContents;
    }
}
