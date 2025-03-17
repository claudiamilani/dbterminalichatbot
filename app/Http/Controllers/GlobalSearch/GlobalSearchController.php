<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\GlobalSearch;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;

class GlobalSearchController extends Controller
{
    //Indicates Models that implement GloballySearchable trait to search
    private array $searchable = [];

    /**
     * The view of search results
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Redirector|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        if(!$request->get('search')){
            return redirect()->route('admin::dashboard')->with(['alerts' => [
                ['message' => trans('common.min_required_chars',['charNum' => 3]), 'type' => 'error']
            ]]);
        }
        $results = [];
        foreach($this->searchable as $class){
            if(!method_exists($class,'searchFilter')) continue;
            $query = $class::search($request->all(),true);
            if(count(($paginated_results = $query->paginate(null, ['*'], $class::globalSearchName().'_page'))->items())){
                $results[] = $paginated_results;
            }
        }
        if(count($results) == 1 && count(($lucky_set = Arr::first($results))->items()) == 1){
            //TODO: permessi?
            return redirect($lucky_set->first()->globalSearchItemUrl(false));
        }

        return view('global_search.index',compact('results'));
    }
}
