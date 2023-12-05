<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thesaurus\LanguageRequest;
use App\Models\Thesaurus\Language;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.password')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Languages', [
            'languages' => Language::select('id', 'name')
                ->orderBy('name')
                ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.languages'
        $language = new Language();
        $language->name = $request->name;
        $language->save();
        
        return redirect('admin/languages');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, string $id): RedirectResponse
    {
        $language = Language::find($id);
        $language->name = $request->name;
        $language->save();
        
        return redirect('admin/languages');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Language::find($id)->delete();
        
        return redirect('admin/languages');
    }
}
