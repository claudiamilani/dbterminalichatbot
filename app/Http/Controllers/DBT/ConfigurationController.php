<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttrCategory;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Ota;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use App\Http\Controllers\Controller;
use App\Mail\SendConfigurationMail;
use App\Services\RtmpService;
use App\Traits\ControllerPathfinder;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class ConfigurationController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/configuration';
    }

    /**
     * Display resources (form VAS).
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|View
     */
    public function showFormVas(Request $request)
    {
        try {
            $vendors = Vendor::whereHas('terminals', function ($query) {
                $query->where('published', 1);
            })
                ->where('published', 1)
                ->sortable(['name' => 'asc'])
                ->get();

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }

        return view('dbt.configuration.vas.vas_form', compact('vendors'));
    }

    /**
     * Display the specified resource (Tech Sheet VAS).
     *
     * @param  Request  $request
     * @return Application|Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|View|JsonResponse
     */
    public function showTechSheetVas(Request $request)
    {
        try {
            $terminal = $request->input('model');

            $terminalDetails = Terminal::where('id', $terminal)
                ->with([
                    'configs' => function ($query) {
                        $query->where('published', 1);
                    },
                    'pictures',
                    'ota',
                    'configs.document'
                ])
                ->where('published', 1)
                ->firstOrFail();

            $attributeDetails = DbtAttribute::where('published', 1)
                ->whereHas('category', function ($query) use ($terminal) {
                    $query->where('published', 1);
                })

                ->whereHas('attributeValues', function ($query) use ($terminal) {
                    $query->where('terminal_id', $terminal);
                })

                ->get();

            $attributeDetails = $attributeDetails
                ->sortBy('display_order')
                ->sortBy('category.display_order');

            $sources = IngestionSource::orderBy('priority')
                ->get()
                ->pluck('id')
                ->toArray();

            return view('dbt.configuration.vas.vas_techsheet', compact(
                'terminalDetails',
                'attributeDetails',
                'sources',
                'terminal'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return response()->json([$e->getMessage()], 500);
        }
    }

    /**
     * Display modal to send mail (VAS).
     *
     * @param  Request  $request
     * @return Application|Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|View
     */
    public function ShowSendMailModalVas(Request $request)
    {
        try {
            $file_path = $request->input('file_path');
            return view('dbt.configuration.vas.vas_send_mail', compact(
                'file_path'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }
    }

    /**
     * Send configuration to provided mail (VAS).
     *
     * @return JsonResponse
     */
    public function sendMailVas(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $attributes = [
                'email' => 'required|email',
                'file_path' => 'required|string'
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'title' => trans('DBT/configuration.send.mail_title_error'),
                    'message' => trans('DBT/configuration.send.mail_message_error')
                ]);
            }

            $email_address = $request->input('email');
            $attachment = $request->input('file_path');
            $email_content = trans('DBT/configuration.send.mail.emailContent');

            try {
                Mail::to($email_address)
                    ->send(new SendConfigurationMail($email_content, $attachment));

                return response()->json([
                    'success' => true,
                    'title' => trans('DBT/configuration.send.mail_title_success'),
                    'message' => trans('DBT/configuration.send.mail_message_success')
                ]);

            } catch (Exception $e) {
                Log::error(trans('DBT/configuration.send.mail.mail_error').$e->getMessage());

                return response()->json([
                    'success' => false,
                    'title' => trans('DBT/configuration.send.mail_title_error'),
                    'message' => trans('DBT/configuration.send.mail_message_error')
                ]);
            }

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());

            return response()->json([
                'success' => false,
                'title' => trans('DBT/configuration.send.mail_title_error'),
                'message' => trans('DBT/configuration.send.mail_message_error')
            ]);
        }
    }

    /**
     * Display send OTA form (VAS).
     *
     * @param  Request  $request
     * @return Application|Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|View
     */
    public function ShowSendOtaModalVas(Request $request)
    {
        try {
            $param_1 = $request->input('terminal_id');
            $param_2 = $request->input('ota_id');

            return view('dbt.configuration.vas.vas_send_ota', compact(
                'param_1',
                'param_2'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }
    }

    /**
     * Display resources (form WINDTRE).
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|View
     */
    public function showFormWindtre(Request $request)
    {
        try {
            $vendors = Vendor::whereHas('terminals', function ($query) {
                $query->where('published', 1);
            })
                ->where('published', 1)
                ->sortable(['name' => 'asc'])
                ->get();

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }

        return view('dbt.configuration.windtre.windtre_form', compact(
            'vendors'
        ));
    }

    /**
     * Display the specified resource (Tech Sheet WINDTRE).
     *
     * @param  Request  $request
     * @return Application|\Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function showTechSheetWindtre(Request $request)
    {
        try {
            $terminal = $request->input('model');

            $terminalDetails = Terminal::where('id', $terminal)
                ->with([
                    'configs' => function ($query) {
                        $query->where('published', 1);
                    },
                    'pictures',
                    'ota',
                    'configs.document'
                ])
                ->where('published', 1)
                ->firstOrFail();

            return view('dbt.configuration.windtre.windtre_techsheet', compact(
                'terminalDetails'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }
    }

    /**
     * Display the specified resource modal (WINDTRE).
     *
     * @param  int  $terminal
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|JsonResponse|View
     */
    public function showTechSheetModalWindtre(int $terminal)
    {
        try {
            $terminalDetails = Terminal::where('id', $terminal)
                ->with([
                    'configs' => function ($query) {
                        $query->where('published', 1);
                    },
                    'pictures',
                    'ota',
                    'configs.document'
                ])
                ->where('published', 1)
                ->firstOrFail();

            $sources = IngestionSource::orderBy('priority')
                ->get()
                ->pluck('id')
                ->toArray();

            $groupedAttributes = AttrCategory::where('published', 1)
                ->whereHas('dbtAttributes', function ($query) use ($terminal) {
                    $query->where('published', 1)
                        ->whereHas('attributeValues', function ($query) use ($terminal) {
                            $query->where('terminal_id', $terminal);
                        });
                })
                ->orderBy('display_order')
                ->with([
                    'dbtAttributes' => function ($query) use ($terminal) {
                        $query->whereHas('attributeValues', function ($query) use ($terminal) {
                            $query->where('terminal_id', $terminal)
                                ->where('published', 1);
                        })
                            ->orderBy('display_order')
                            ->orderBy('name');
                    }
                ])->get();

            return view('dbt.configuration.windtre.windtre_modal_techsheet', compact(
                'terminalDetails',
                'groupedAttributes',
                'sources',
                'terminal'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Display modal send OTA (WINDTRE).
     *
     * @param  Request  $request
     * @return Application|Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|View
     */
    public function ShowSendOtaModalWindtre(Request $request)
    {
        try {
            $param_1 = $request->input('terminal_id');
            $param_2 = $request->input('ota_id');

            return view('dbt.configuration.windtre.windtre_send_ota', compact(
                'param_1',
                'param_2'
            ));

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            abort(500, trans('common.http_err.500'));
        }
    }

    /**
     * Retrieve the list of models (terminals) for the specified vendor.
     *
     * @param  int  $vendorId
     * @return JsonResponse
     */
    public function getModels(int $vendorId)
    {
        try {
            $terminals = Terminal::whereHas('vendor', function ($query) use ($vendorId) {
                $query->where('id', $vendorId)
                    ->where('published', 1);
            })->where('published', 1)
                ->sortable(['name' => 'asc'])
                ->get();

            return response()->json($terminals);

        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return response()->json(['message' => trans('DBT/configuration.errors.request_error')], 500);
        }
    }

    /**
     * Send OTA configuration to provided phone number.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function sendOta(Request $request)
    {
        $tag = '<?xml version="1.0" encoding="UTF-8" ?>';

        try {
            $validator = Validator::make($request->all(), $attributes = [
                'terminal_id' => 'required|integer',
                'ota_id' => 'required|integer',
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'title' => trans('DBT/configuration.send.ota_title_error'),
                    'message' => trans('DBT/configuration.send.ota_message_error')
                ]);
            }

            /* Retrieve data from request */
            $terminal_id = $request->input('terminal_id');
            $ota_id = $request->input('ota_id');
            $phone = $request->input('phone');

            /* Retrieve data from DB */
            $terminal = Terminal::where('id', $terminal_id)
                ->where('published', 1)
                ->firstOrFail();

            $ota = Ota::where('id', $ota_id)->firstOrFail();

            /* Connection & OTA_EXT_0 parameters for the first call */
            $session_url = config('dbt.configuration_rtmp.session_url');
            $session_user = config('dbt.configuration_rtmp.session_user');
            $session_password = config('dbt.configuration_rtmp.session_password');
            $ota_ext_0 = $ota->ext_0;

            $sessionData = (object) [
                'user' => $session_user,
                'password' => $session_password,
                'ota_ext_0' => $ota_ext_0
            ];

            /* Generate XML for the first call & pass values to view */
            $session_xml = view('dbt.configuration.xml_files.session_xml', compact('sessionData'))->render();

            $session_xml = $tag.$session_xml;

            /* Call rtmpService for sending the first XML request */
            $rtmp_service = new RtmpService();
            $session_xml_response = $rtmp_service->RtmpClient($session_xml, $session_url);

            /* If the first XML call succeeds */
            if ($session_xml_response['success']) {

                /* Connection parameters for the second call */
                $request_url = config('dbt.configuration_rtmp.request_url');
                $request_user = config('dbt.configuration_rtmp.request_user');
                $request_password = config('dbt.configuration_rtmp.request_password');

                /* Retrieve data to send */
                $ota_type = $ota->type;
                $ota_sub_type = $ota->sub_type;
                $ota_vendor = $terminal->ota_vendor;
                $ota_model = $terminal->ota_model;

                /* Load & parse the first response XML */
                $response_xml_body = $session_xml_response['response_xml'];
                $xml = simplexml_load_string($response_xml_body);

                /* Extract parameters from the first response */
                $parameters = [];
                foreach ($xml->xpath('/otamanager/result/parameters/*') as $obj) {
                    $key = (string) $obj->attributes()->key;
                    $value = (string) $obj->attributes()->value;
                    $parameters[$key] = $value;
                }

                /* Generate a unique trans ID */
                $transId = $this->getSessionId();

                /* Generate XML for the second call & pass values to view */
                $requestData = (object) [
                    'user' => $request_user,
                    'password' => $request_password,
                    'transId' => $transId,
                    'phone' => $phone,
                    'ota_type' => $ota_type,
                    'ota_vendor' => $ota_vendor,
                    'ota_model' => $ota_model,
                    'ota_sub_type' => $ota_sub_type,
                    'parameters' => $parameters
                ];

                $requestXml = view('dbt.configuration.xml_files.request_xml', compact('requestData'))->render();

                /* Send the second XML request */
                $request_xml_response = $rtmp_service->RtmpClient($requestXml, $request_url);

                /* If the second XML call succeeds */
                if ($request_xml_response['success']) {
                    $request_response_body = $request_xml_response['response_xml'];

                    /* Check if the second response contains the <ok/> tag */
                    if (str_contains($request_response_body, '<ok/>')) {
                        return response()->json([
                            'success' => true,
                            'title' => trans('DBT/configuration.send.ota_title_success'),
                            'message' => trans('DBT/configuration.send.ota_message_success')
                        ]);
                    } else {
                        /* If the second XML call hasn't <ok/> tag */
                        return response()->json([
                            'success' => false,
                            'title' => trans('DBT/configuration.send.ota_title_error'),
                            'message' => trans('DBT/configuration.send.ota_message_error')
                        ]);
                    }
                } else {
                    /* If the second XML call fails */
                    return response()->json([
                        'success' => false,
                        'title' => trans('DBT/configuration.send.ota_title_error'),
                        'message' => trans('DBT/configuration.send.ota_message_error')
                    ]);
                }
            } else {
                /* If the first XML call fails */
                return response()->json([
                    'success' => false,
                    'title' => trans('DBT/configuration.send.ota_title_error'),
                    'message' => trans('DBT/configuration.send.ota_message_error')
                ]);
            }

        } catch (Exception|Throwable $e) {
            Log::channel('admin_gui')->error($e->getMessage());

            return response()->json([
                'success' => false,
                'title' => trans('DBT/configuration.send.ota_title_error'),
                'message' => trans('DBT/configuration.send.ota_message_error')
            ]);
        }
    }

    /**
     * Generates a unique session ID based on the current microsecond timestamp.
     *
     * @return float
     */
    protected function getSessionId(): float
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}