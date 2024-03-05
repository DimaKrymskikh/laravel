<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class OpenWeatherException extends \Exception
{
    public function __construct(
        private string $exceptionMessage
    )
    {
        parent::__construct($this->exceptionMessage);
    }
    
    public function report(): void
    {
        Log::channel('openweather')->info($this->exceptionMessage);
    }
    
    public function render(): string
    {
        return (string) collect(['message' => $this->exceptionMessage]);
    }
}
