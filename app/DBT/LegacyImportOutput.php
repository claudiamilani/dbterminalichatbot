<?php

namespace App\DBT;

class LegacyImportOutput
{
    public string $status;
    public ?string $message;
    public function __construct($status,$message = null)
    {
        $this->status = $status;
        $this->message = $message;
    }

}