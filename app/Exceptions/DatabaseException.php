<?php

namespace App\Exceptions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class DatabaseException extends \Exception
{
    public function __construct(
        private string $exceptionMessage
    ) {
        parent::__construct($this->exceptionMessage);
    }
    
    public function report(): void
    {
        Log::channel('database')->notice($this->exceptionMessage);
    }
    
    public function render(Request $request): string|RedirectResponse
    {
        if($request->header('X-Inertia', false)) {
            return redirect()->back()
                   ->withInput()
                   ->withErrors(['message' => $this->exceptionMessage]);
        } else {
            return (string) collect(['message' => $this->exceptionMessage]);
        }
    }
}
