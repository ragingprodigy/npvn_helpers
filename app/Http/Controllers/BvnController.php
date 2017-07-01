<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

class BvnController extends Controller
{
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
                    $sheet->with(json_decode(json_encode($data), true));
                });
            })->download('xlsx');
        });
    }
}
