<?php

namespace App\Imports;

use App\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Mail;
use App\Mail\CsvFailed;

class ModuleUpload implements ToCollection, WithChunkReading, ShouldQueue, WithHeadingRow
{
    public $file_name;

    function __construct($fileName)
    {
        $this->file_name = $fileName;
    }

    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), [
            '*.module_code' => 'required',
            '*.module_name' => 'required',
            '*.module_term' => 'required'
        ]);

        if(!$validator->fails())
        {
            foreach ($rows as $row) {
                Module::create([
                    'module_code' => $row['module_code'],
                    'module_name' => $row['module_name'],
                    'module_term' => $row['module_term'],
                ]);
            }
        }
        else
        {
            $messages = $validator->errors();
            Mail::to('tpsalmanulfaris1002@gmail.com')->send(new CsvFailed($messages, $this->file_name));
        }
    }


    public function chunkSize(): int
    {
        return config('csv.rows');
    }

    public function headingRow(): int
    {
        return 1;
    }
}
