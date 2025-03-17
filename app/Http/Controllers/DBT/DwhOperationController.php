<?php

namespace App\Http\Controllers\DBT;

use App\DBT\DwhOperations;
use App\DBT\Models\TransposeConfig;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\TranslatedValidation;

class DwhOperationController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/dwh_operations';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list_dwh_operations', TransposeConfig::class);
            $dwh_operations = DwhOperations::getType();
            return view('dbt.dwh_operations.index', compact('dwh_operations'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Show the page for creating a new Dwh View.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create_views', TransposeConfig::class);
            $type = request('type');
            return view('dbt.dwh_operations.create', compact('type'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.forbidden_message')]);
        }
    }


    /**
     * Create the requested DWH View
     *
     * @param Request $request
     * @return void
     */
    public function executeCreate(Request $request)
    {
        $this->authorize('create_views', TransposeConfig::class);
        try {
            $dwh_operations = new DwhOperations();
            $type = $request->type;
            switch ($type) {
                case 'DWH_MARCA':
                    $dwh_operations->createDwhMarcaView();
                    break;
                case 'DWH_TERMINALE':
                    $dwh_operations->createDwhTerminaleView();
                    break;
                case 'DWH_TAC':
                    $dwh_operations->createDwhTacView();
                    break;
                case 'DWH_ATTRIBUTI':
                    $dwh_operations->createDwhAttributiView();
                    break;
                case 'DWH_TRASPOSTA':
                    $dwh_operations->createDwhTraspostaView();
                    break;
                default:
                    return redirect()->back()->withAlerts([
                        ['message' => trans('DBT/dwh_operations.create.error'), 'type' => 'error']
                    ]);
            }
            return redirect()->route('admin::dbt.dwh_operations.index')->withAlerts([
                ['message' => trans('DBT/dwh_operations.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (QueryException  $e) {
            if($e->getCode() == '42P01'){
                return redirect()->back()->withAlerts([
                    ['message' => trans('DBT/dwh_operations.create.error_42P01'), 'type' => 'error']
                ])->withInput();
            }
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/dwh_operations.create.error'), 'type' => 'error']
            ])->withInput();
        } catch (Exception $e) {
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/dwh_operations.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }
}