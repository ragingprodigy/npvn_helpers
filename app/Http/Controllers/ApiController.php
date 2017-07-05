<?php
declare(strict_types = 1);

/**
 * @author Oladapo Omonayajo <oladapo.omonayajo@lazada.com.ph>
 * Created on 7/6/2017, 01:44
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ApiController extends Controller
{
    public function compareSheets(Request $request)
    {
        /** @var UploadedFile[] $files */
        $files = $request->allFiles();
        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $file->store('destination');
        }

        $fileContents = [];

        foreach ($fileNames as $name) {
            Excel::load('storage/app/' . $name, function ($reader) use (&$fileContents) {
                $fileContents[] = $reader->get();
            });
        }

        \Log::info(
            sprintf(
                'Data length in file 1: %d, Data length in file 2: %d',
                count($fileContents[0]),
                count($fileContents[1])
            )
        );
    }
}
