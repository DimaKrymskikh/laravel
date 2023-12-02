<?php

namespace App\Http\Controllers\Project\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    use Url;
    
    /**
     * Аутентифицированный пользователь становится админом
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        User::where('id', $request->user()->id)
            ->update(['is_admin' => true]);
        
        return redirect($this->getUrl('/userfilms', [
            'page' => $request->page,
            'number' => $request->number,
            'title' => $request->title,
            'description' => $request->description
        ]));
    }
    
    /**
     * Админ отказывается от прав
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        User::where('id', $request->user()->id)
            ->update(['is_admin' => false]);
        
        return redirect($this->getUrl('/userfilms', [
            'page' => $request->page,
            'number' => $request->number,
            'title' => $request->title,
            'description' => $request->description
        ]));
    }
}
