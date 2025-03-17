<?php

namespace App\Rules;

use App\DBT\Models\DbtAttribute;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ValidateLegacyAttributeValue implements ValidationRule
{

    protected $dbtAttribute;

    public function __construct($dbtAttribute)
    {
        $this->dbtAttribute = $dbtAttribute;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dbt_attribute = $this->dbtAttribute;
        //Presenza attributo
        if (!$dbt_attribute) {
            // Se non esiste, chiama $fail e interrompi la validazione
            $fail('Attributo con ID ' . $attribute . ' non trovato a sistema.');

        } else {
            $configured_options = $dbt_attribute->getOptions();

            //Valido le opzioni in arrivo

            if ($dbt_attribute->getMultipleOption()) {
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
             /*                   if($value != '' && Str::contains('|',$value)){
                                    $formatted = array_values(explode('|',$value));
                                    foreach($formatted as $item){
                                        if (!in_array($item, $configured_options) && !empty($configured_options)) {
                                            $fail('Opzione ' . $item . ' non valida per l\'attributo ' . $dbt_attribute->name);
                                        }
                                    }
                                }
                                if (!in_array($value, $configured_options)) {
                                    $fail('Opzione ' . $value . ' non valida per l\'attributo ' . $dbt_attribute->name);
                                }*/
                                break;
                        }
                        break;
                }

            } else {
            }
        }
    }
}
