<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thesaurus\Filters\LanguageFilterRequest;
use App\Http\Requests\Thesaurus\LanguageRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Thesaurus\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    public function __construct(
        private LanguageService $languageService,
    ) {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * В админской части отрисовывает таблицу языков
     * 
     * @return Response
     */
    public function index(LanguageFilterRequest $request): Response
    {
        return Inertia::render('Admin/Languages', [
            'languages' => $this->languageService->getAllLanguagesList($request->getLanguageFilterDto())
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
        $this->languageService->create($request->name);
        
        return redirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }

    /**
     * Изменяет название языка
     * 
     * @param LanguageRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(LanguageRequest $request, int $id): RedirectResponse
    {
        $this->languageService->update($request->name, $id);
        
        return redirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }

    /**
     * Удаляет язык из таблицы 'thesaurus.languages'
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->languageService->delete($id);
        
        return redirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }
    
    /**
     * Возвращает список языков в формате json
     * 
     * @param Request $request
     * @return string
     */
    public function getJson(LanguageFilterRequest $request): string
    {
        return (string) $this->languageService->getAllLanguagesList($request->getLanguageFilterDto());
    }
}
