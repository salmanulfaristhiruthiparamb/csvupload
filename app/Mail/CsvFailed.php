<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\MessageBag;

class CsvFailed extends Mailable
{
    use Queueable, SerializesModels;

    protected $errors;
    protected $file_name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MessageBag $array,$file_name)
    {
        $this->errors = $array;
        $this->file_name = $file_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('File upload Failed')->markdown('emails.csv.failed')->with([
            'errors' => $this->exportMessages(),
            'file' => $this->file_name
        ]);
    }

    public function exportMessages()
    {
        $new_arr = array();
        foreach ($this->errors->all() as $val) {
            $key = preg_replace('/[^0-9]/', '', $val);
            $value = preg_replace('/[^a-zA-Z _]/', '', $val);
            $new_arr[$value][] = $key;
        }
        return $new_arr;
    }
}
