<?php

namespace App\Exceptions;

use Illuminate\Http\RedirectResponse;

final class RuleException extends \Exception
{
    public function __construct(
            private string $attribute,
            private string $exceptionMessage
    ) {
        parent::__construct($this->exceptionMessage);
    }
    
    public function render(): RedirectResponse
    {
        return redirect()->back()
               ->withInput()
               ->withErrors([$this->attribute => $this->exceptionMessage]);
    }
}
