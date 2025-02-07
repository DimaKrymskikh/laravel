<?php

namespace App\Http\Controllers\Project\Admin;

use App\Http\Controllers\Controller;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Http\Request;

class TimezoneController extends Controller
{
    public function __construct(
            private TimezoneService $timezoneService,
    ) {
    }
    
    /**
     * Возвращает список временных зон при редактировании города
     * 
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        return (string) $this->timezoneService->getAllTimezonesList($request->name);
    }
}
