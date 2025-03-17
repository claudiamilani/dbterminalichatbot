<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Tac;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiTerminalController extends Controller
{
    /**
     * Display resources (getTerminalWind).
     * Retrieves the terminal information based on the IMEI from the request and returns a string.
     *
     * @param  Request  $request
     * @return string
     *
     */
    public function getTerminalWind($imei)
    {
        try {
            $separator = ';';

            if (!empty($imei)) {
                $tac = substr($imei, 0, 8);

                /* Call getTerminalData() -> returns success or error */
                $data = $this->getTerminalData($tac);

                /* Define status messages */
                $results = [];

                if ($data !== false) { /* SUCCESS */
                    $results = [
                        'ESITO' => trans('DBT/configuration.terminal_wind.success.result_type'),
                        'VENDOR' => $data['vendor']->name,
                        'MODEL' => $data['model']->name
                    ];

                } else { /* ERROR */
                    $results = [
                        'ESITO' => trans('DBT/configuration.terminal_wind.error.result_type'),
                        'VENDOR' => trans('DBT/configuration.terminal_wind.error.not_found'),
                        'MODEL' => trans('DBT/configuration.terminal_wind.error.not_found')
                    ];
                }

                if (request('format') == 'xml') { /* If xml is requested */
                    $tag = '<?xml version="1.0" encoding="ISO-8859-1" ?>';

                    $results = (object) $results;

                    $xml = view('dbt.configuration.xml_files.getTerminalWind_xml', compact(
                        'results'
                    ))->render();

                    $xml = $tag.$xml;

                    return response($xml, 200)
                        ->header('Content-Type', 'application/xml');

                } elseif (request('format') === 'json') { /* If json is requested */
                    $jsonResponse = $results;
                    return response()->json($jsonResponse);

                } else { /* If cvs or default is requested */
                    unset($results['ESITO']);
                    return implode($separator, $results);
                }
            }

        } catch (Exception|Throwable $e) {
            Log::channel('admin_gui')->error($e->getMessage());
        }

        exit;
    }

    /**
     * Retrieve terminal & vendor information by specified TAC passed as $param.
     *
     * @param  string  $param
     * @return array|false
     */
    private function getTerminalData(string $param)
    {
        try {
            $tac = Tac::where('value', $param)->firstOrFail();

            /* If TAC exists, find vendor & terminal */
            $terminal = $tac->terminal;
            $vendor = $terminal->vendor;

            /* If vendor doesn't exist */
            if (!$vendor) {
                Log::channel('admin_gui')->error(trans('DBT/configuration.terminal_wind.errors.vendor_not_found').$param);

                return false;

            } /* If vendor is empty */
            elseif (empty($vendor->name)) {
                Log::channel('admin_gui')->error(trans('DBT/configuration.terminal_wind.errors.vendor_empty').$param);

                return false;

            } /* If terminal doesn't exist */
            elseif (!$terminal) {
                Log::channel('admin_gui')->error(trans('DBT/configuration.terminal_wind.errors.terminal_not_found').$param);

                return false;

            } /* All is ok, return value's array */
            else {
                return [
                    'status' => 'success',
                    'vendor' => $vendor,
                    'model' => $terminal
                ];
            }

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());

            return false;
        }
    }
}
