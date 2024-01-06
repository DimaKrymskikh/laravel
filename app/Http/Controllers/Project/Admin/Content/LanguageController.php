<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thesaurus\LanguageRequest;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * В админской части отрисовывает таблицу языков
     * 
     * @return Response
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
     * В таблицу 'thesaurus.languages' добавляется новый язык
     * 
     * @param LanguageRequest $request
     * @return RedirectResponse
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
     * Изменяет название языка
     * 
     * @param LanguageRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(LanguageRequest $request, string $id): RedirectResponse
    {
        $language = Language::find($id);
        $language->name = $request->name;
        $language->save();
        
        return redirect('admin/languages');
    }

    /**
     * Удаляет язык из таблицы 'thesaurus.languages'
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        Language::find($id)->delete();
        
        return redirect('admin/languages');
    }
    
    /**
     * Возвращает список языков в формате json
     * 
     * @param Request $request
     * @return string
     */
    public function getJson(Request $request): string
    {
        return (string) Language::select('id', 'name')
                            ->when($request->name, function (Builder $query, string $name) {
                                $query->where('name', 'ILIKE', "%$name%");
                            })
                            ->orderBy('name')
                            ->get();
    }
}
