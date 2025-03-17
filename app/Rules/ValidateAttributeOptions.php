<?php

namespace App\Rules;

use App\DBT\Models\DbtAttribute;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class ValidateAttributeOptions implements ValidationRule
{

    protected $config;
    protected $data;

    public function __construct($data)
    {
        $this->config = DbtAttribute::INPUT_TYPES_CONFIGURATION;
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = $value;
        $data = $this->data;
        $type_config = $this->config[$value];

        switch ($type) {
            default:
                if (isset($data['default_value'])) {
                    //Validazione presenza dei valori di default tra le opzioni disponibili
                    /*foreach ($data['default_value'] as $key => $item) {
                        if (!in_array($item, $data['options'])) {
                            $fail(trans('DBT/attributes.validation.options_not_found', ['option' => $item]));
                        }
                    }*/
                    if (count((array)$data['default_value']) >= 1 && ($data['type'] == DbtAttribute::TYPE_DECIMAL || $data['type'] == DbtAttribute::TYPE_INT)) {
                        foreach ($data['default_value'] as $option_to_verify) {
                            if (!is_numeric($option_to_verify)) {
                                $fail(trans('Valori default non numerici'));
                            }
                        }
                    }
                    if (!$data['multiple'] && count((array)$data['default_value']) > 1) {
                        $fail(trans('DBT/attributes.validation.multiple_default'));
                    }
                }
                if(isset($data['options'])){
                    if (count((array)$data['options']) > 1  &&  ($data['type'] == DbtAttribute::TYPE_DECIMAL || $data['type'] == DbtAttribute::TYPE_INT)) {
                        foreach($data['options'] as $option_to_verify) {
                            if(!is_numeric($option_to_verify)) {
                                $fail(trans('DBT/attributes.validation.not_numeric',['option'=>$option_to_verify]));
                            }
                        }
                    }
                }
                if ($type_config['options']['searchable'] === 0 && $data['searchable']) {
                    $fail(trans('DBT/attributes.validation.not_searchable'));
                }
                if ($type_config['options']['multiple'] === 0 && $data['multiple']) {
                    $fail(trans('DBT/attributes.validation.not_multiple'));
                }
                if ($data['multiple'] && empty($data['options'])) {
                    $fail(trans('DBT/attributes.validation.multiple_no_options'));
                }

        }
    }
}
