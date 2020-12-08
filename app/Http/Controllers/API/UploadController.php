<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CsvUploadRequest;
use App\Imports\ModuleUpload;
use Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UploadController extends Controller
{
    public function upload(CsvUploadRequest $request)
    {
        $fileName = $request->file('file')->getClientOriginalName();
        $file = $request->file('file');
        
        $data['csv'] = $this->readCSV($file,array('delimiter' => ','));

        $headers = $data['csv'][0];
        $data['headers'] = $headers;
        unset($data['csv'][0]);
        array_pop($data['csv']);

        $validator = Validator::make($data,[
            'csv' => 'required|array|size:'.config('csv.rows'),
            'csv.*' => 'required|array|size:'.config('csv.columns'),
            'headers.*' => 'required|in:'.implode(',', config('csv.column_names')),
        ],[
            'csv.required' => 'File must have data',
            'csv.array' => 'Must have data',
            'csv.size' => 'CSV must have length :size',
        ]);

        if($validator->fails())
        {
            return response()->json(['status' => 0, 'errors' => $validator->errors()]);
        }

        Excel::import(new ModuleUpload($fileName), $file);

        return response()->json(['status' => 1, 'response' => 'uploaded']);
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        $i = 0;
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
