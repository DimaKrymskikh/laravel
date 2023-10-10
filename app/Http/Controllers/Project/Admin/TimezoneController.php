<?php

namespace App\Http\Controllers\Project\Admin;

use App\Models\Thesaurus\Timezone;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimezoneController extends Controller
{
    public function index(Request $request): string
    {
        return (string) Timezone::select('id', 'name')->where('name', 'ilike', "%$request->name%")->get();
    }
}
