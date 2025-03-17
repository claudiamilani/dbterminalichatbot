<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ValidateDateInterval implements ValidationRule
{
    private mixed $end_attribute;
    private mixed $end_attribute_value;
    private string $date_format;
    private Application|Translator|string|array|null|\Illuminate\Foundation\Application $message;
    private string $translated_date_format;

    /**
     * Create a new rule instance.
     *
     * @param $end_attribute
     * @param string $date_format
     * @param string $translated_date_format
     */
    public function __construct($end_attribute, string $date_format = 'd/m/Y H:i', string $translated_date_format = 'gg/mm/AAAA HH:MM')
    {
        if(is_array($end_attribute) && Arr::isAssoc($end_attribute)){
            $this->end_attribute = Arr::first(array_keys($end_attribute));
            $this->end_attribute_value = Arr::first($end_attribute);
        }else{
            $this->end_attribute = $end_attribute;
            $this->end_attribute_value = request($this->end_attribute);
        }
        $this->date_format = $date_format;
        $this->message = trans('validation.date_interval',['']);
        $this->translated_date_format = $translated_date_format;
    }

    public function isValidDate($date): bool|Carbon
    {
        if (!empty($date)) {
            try {
                $carbon_date = Carbon::createFromFormat($this->date_format, $date);
                if ($carbon_date->format($this->date_format) !== $date) {
                    $this->message = trans('validation.date_interval_format', ['date' => $date ,'format' => $this->translated_date_format]);
                    return false;
                }
            } catch (Exception $e) {
                Log::channel('admin_gui')->error($e->getMessage());
                Log::channel('admin_gui')->error($e->getTraceAsString());
                $this->message = trans('validation.date_interval_format', ['date' => $date ,'format' => $this->translated_date_format]);
                return false;
            }
            return $carbon_date;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return \Illuminate\Foundation\Application|array|string|Translator|Application|null
     */
    public function message(): \Illuminate\Foundation\Application|array|string|Translator|Application|null
    {
        return $this->message;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((!$from = $this->isValidDate($value)) && (!$to = $this->isValidDate($this->end_attribute_value)) && $from > $to) {
            $fail($this->message());
        }
    }
}
