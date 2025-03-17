<?php

namespace App\Rules;

use App\DBT\Models\DbtAttribute;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ValidateAttributeValue implements ValidationRule
{


    public function __construct()
    {

    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dbt_attribute = DbtAttribute::find(array_keys($value)[0]);
        //Presenza attributo
        if (!$dbt_attribute) {
            // Se non esiste, chiama $fail e interrompi la validazione
            $fail('Attributo con ID ' . array_keys($value)[0] . ' non trovato a sistema.');

        } else {

            if((empty($value[array_keys($value)[0]])||$value[array_keys($value)[0]] == ''||$value[array_keys($value)[0]]) && $dbt_attribute->getRequiredOption()){
                $fail('Attributo ' . $dbt_attribute->name . ' richiesto.');
            }else if(is_array($value[array_keys($value)[0]]) && Arr::first($value[array_keys($value)[0]]) == '["-"]'){
                $fail('Attributo ' . $dbt_attribute->name . ' richiesto.');
            }
            //Valido le opzioni in arrivo
            $configured_options = [''=>''] + $dbt_attribute->getOptions();
            switch ($dbt_attribute->type) {
                //Validazione Stringa
                case DbtAttribute::TYPE_VARCHAR:
                case DbtAttribute::TYPE_INT:
                case DbtAttribute::TYPE_DECIMAL:
                    switch ($dbt_attribute->getInputTypeOption()) {
                        //se è una stringa con input type checkbox verifichiamo che il
                        //value in arrivo sia presente tra le opzioni disponibili
                        case 'CHECKBOX':
                        case 'SELECT':
                            if (is_array($value)) {
                                foreach ($value as $option => $inc_value) {
                                    if(is_array($inc_value)){
                                        foreach ($inc_value as $selected_option) {
                                            if (!in_array($selected_option, $configured_options) && !empty($configured_options)) {
                                                $fail('Opzione ' . print_r($selected_option, true) . ' non valida per l\'attributo ' . $dbt_attribute->name);
                                                Log::debug('Multiple Options Failed validation - Value: ' .json_encode($selected_option) . ' Options: ' . json_encode($configured_options) );
                                            }
                                        }
                                    }else{
                                        if (!in_array($inc_value, $configured_options) && !empty($configured_options)) {
                                            $fail('Opzione ' . print_r($inc_value, true) . ' non valida per l\'attributo ' . $dbt_attribute->name);
                                            Log::debug('2nd case Failed validation - Value: ' .$inc_value . ' Options: ' . json_encode($configured_options) );
                                        }
                                    }
                                }
                            } else {
                                if (!in_array($value, $configured_options) && !empty($configured_options)) {
                                    $fail('Opzione ' . $value . ' non valida per l\'attributo ' . $dbt_attribute->name);
                                    Log::debug('Not Array Failed validation - Value: ' .json_encode($value) . ' Options: ' . json_encode($configured_options) );

                                }
                            }

                            break;
                    }
                    break;
            }

        }
    }
}
