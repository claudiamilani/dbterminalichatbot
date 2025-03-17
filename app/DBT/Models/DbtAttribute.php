<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\DBT\Traits\LegacyImportable;
use App\Rules\ValidateAttributeOptions;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DbtAttribute extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    const string TYPE_BOOLEAN = 'BOOLEAN';
    const string TYPE_VARCHAR = 'VARCHAR';
    const string TYPE_TEXT = 'TEXT';
    const string TYPE_INT = 'INT';
    const string TYPE_DECIMAL = 'DECIMAL';
    const array INPUT_TYPES_CONFIGURATION = [
        'TEXT' => [
            'options' => [
                'searchable' => 0,
                'multiple' => 0,
                'required' => null
            ]
        ],
        'NUMBER' => [
            'options' => [
                'searchable' => 0,
                'multiple' => 0,
                'required' => null
            ]
        ],
        'TEXTAREA' => [
            'options' => [
                'searchable' => 0,
                'multiple' => 0,
                'required' => null
            ]
        ],
        'SELECT' => [
            'options' => [
                'searchable' => null,
                'multiple' => null,
                'required' => null
            ]
        ],
        'CHECKBOX' => [
            'options' => [
                'searchable' => 0,
                'multiple' => null,
                'required' => 0
            ]
        ]
    ];
    const array TYPES_CONFIGURATION = [
        DbtAttribute::TYPE_TEXT => [
            'input_types' => [
                'TEXTAREA'
            ]
        ],
        DbtAttribute::TYPE_INT => [
            'input_types' => [
                'SELECT', 'NUMBER', 'TEXT'
            ]
        ],
        DbtAttribute::TYPE_DECIMAL => [
            'input_types' => [
                'SELECT', 'NUMBER', 'TEXT'
            ]
        ],
        DbtAttribute::TYPE_BOOLEAN => [
            'input_types' => [
                'CHECKBOX'
            ]
        ],
        DbtAttribute::TYPE_VARCHAR => [
            'input_types' => [
                'SELECT', 'TEXT', 'CHECKBOX'
            ]
        ]
    ];

    protected $guarded = ['id', 'updated_by_id', 'created_by_id', 'attr_category_id'];

    protected $casts = ['default_value' => 'array', 'type_options' => 'array'];

    /**
     * Filters query results for name or id
     * @param $query
     * @param $search
     * @return Builder
     */
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'ILIKE', "%$search%")
                ->orWhere('id', 'ILIKE', (int)$search)
                ->orWhere('description', 'ILIKE', "%$search%");
        });
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'category_id':
                    if ($value !== '-') {
                        $query->whereHas('category', function ($query) use ($value) {
                            $query->where('id', (int)$value);
                        });
                    }
                    break;
                case 'attribute_type':
                    if ($value !== '-') {
                        $query->where('type', $value);
                    }
                    break;
                case 'published':
                    if ($value !== '-') {
                        $query->where('published', $value);
                    }
                    break;
            }
        }
        return $query;
    }

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AttrCategory::class, 'attr_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function ingestionSource(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class, 'ingestion_source_id');
    }

    public function ingestion(): BelongsTo
    {
        return $this->belongsTo(Ingestion::class, 'ingestion_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    public function getAttributeTypeLabelAttribute(): string
    {
        return trans('DBT/attributes.types.' . $this->type);
    }

    public function getCreatedAtInfoAttribute(): string
    {
        $result = $this->created_at ? $this->created_at->format('d/m/Y H:i') : '';
        if ($this->created_by_id && $result) {
            $result = $result . ' ' . trans('common.from') . ' ' . $this->createdBy->fullName;
        }
        return $result;
    }

    public function getUpdatedAtInfoAttribute(): string
    {
        $result = $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '';
        if ($this->updated_by_id && $result) {
            $result = $result . ' ' . trans('common.from') . ' ' . $this->updatedBy->fullName;
        }
        return $result;
    }

    public function getRequiredOption()
    {
        return !empty($this->type_options['required']) ? 'required' : '';
    }

    public function getSearchableOption()
    {
        return !empty($this->type_options['searchable']) ? 'searchable' : 0;
    }

    public function getMultipleOption()
    {
        return !empty($this->type_options['multiple']) ? 'multiple' : 0;
    }

    public function getInputTypeOption()
    {
        return isset($this->type_options['input_type']) ? $this->type_options['input_type'] : null;
    }

    public function getInputTypeLabelAttribute(): string
    {
        return (isset($this->type_options['input_type'])) ? trans('DBT/attributes.type_options.input_types.' . $this->type_options['input_type']) : 'N/A';
    }

    public function getDefaultValue()
    {
        if (!empty($this->default_value) && $this->getMultipleOption()) {
            return array_combine($this->default_value, $this->default_value);
        } else if (!empty($this->default_value) && !$this->getMultipleOption()) {
            return $this->default_value[0];
        } else if (empty($this->default_value) && $this->type == DbtAttribute::TYPE_BOOLEAN) {
            return 0;
        }
        return null;
    }


    public function getDefaultValueOptions()
    {
        return array_combine($this->default_value, $this->default_value);
    }

    public function getOptions()
    {
        return array_combine($this->type_options['options'], $this->type_options['options']) ;
    }

    public function getDecimals()
    {
        switch ($this->type) {
            case DbtAttribute::TYPE_DECIMAL:
                return '0.01';
            case DbtAttribute::TYPE_INT:
                return '1';
            default:
                return '';
        }
    }

    public function getStep()
    {
        switch ($this->type) {
            case DbtAttribute::TYPE_DECIMAL:
                return '0.1';
            case DbtAttribute::TYPE_INT:
                return '1';
            default:
                return '';
        }
    }

    protected static function legacyTable(): string
    {
        return 'attribute';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_attribute';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->join('category', function ($join) {
            $join->on('category.id_category', '=', 'attribute.id_category')->whereNotIn('category.id_category', [1, 100, 1000])->whereNull('category.deleted');
        })->where('id_attribute', '!=',147);

    }

    protected static function createFromLegacy(object $row): void
    {
        //TODO Handle legacy fields and map them into actual type_options
        $type_map = [
            'url' => DbtAttribute::TYPE_TEXT, //safe
            'BOOLEAN' => DbtAttribute::TYPE_BOOLEAN, //safe
            'VARCHAR' => DbtAttribute::TYPE_VARCHAR, //safe
            'select' => DbtAttribute::TYPE_VARCHAR, //not safe
            'text' => DbtAttribute::TYPE_TEXT,
            'checkbox' => DbtAttribute::TYPE_BOOLEAN,
            'textarea' => DbtAttribute::TYPE_TEXT,
            'double' => DbtAttribute::TYPE_DECIMAL,
            'MULTI_VALUED_ENUM' => DbtAttribute::TYPE_VARCHAR,
            'TEXT' => DbtAttribute::TYPE_TEXT,
            'ENUM' => DbtAttribute::TYPE_VARCHAR,
            'BIGINT' => DbtAttribute::TYPE_INT,
            'DOUBLE' => DbtAttribute::TYPE_DECIMAL,
        ];
        $attribute_category = AttrCategory::imported($row->{AttrCategory::legacyPrimaryKey()})->firstOrFail();
        $row->id_category = $attribute_category->id;

        throw_unless(array_key_exists($row->type, $type_map), \Exception::class, 'Invalid category type: ' . $row->type);
        $row->type = $type_map[$row->type];
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'name' => 'required|string|max:255',
            'id_category' => 'required',
            'description' => 'string|max:65535|nullable',
            'display_order' => 'nullable|integer|min:0|max:100',
            'required' => 'integer|min:0|max:1'
        ])->validate();

        $type_options = self::createOptionsFromLegacy($row);

        DbtAttribute::unguard();
        DbtAttribute::create(['name' => $row->name, 'description' => $row->description, 'type' => $row->type, 'attr_category_id' => $row->id_category, 'published' => $row->required, 'display_order' => $row->display_order, 'type_options' => $type_options['config'], 'default_value' => $type_options['default_value'], 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        DbtAttribute::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        $type_map = [
            'url' => DbtAttribute::TYPE_TEXT, //safe
            'BOOLEAN' => DbtAttribute::TYPE_BOOLEAN, //safe
            'VARCHAR' => DbtAttribute::TYPE_VARCHAR, //safe
            'select' => DbtAttribute::TYPE_VARCHAR, //not safe
            'text' => DbtAttribute::TYPE_TEXT,
            'checkbox' => DbtAttribute::TYPE_BOOLEAN,
            'textarea' => DbtAttribute::TYPE_TEXT,
            'double' => DbtAttribute::TYPE_DECIMAL,
            'MULTI_VALUED_ENUM' => DbtAttribute::TYPE_VARCHAR,
            'TEXT' => DbtAttribute::TYPE_TEXT,
            'ENUM' => DbtAttribute::TYPE_VARCHAR,
            'BIGINT' => DbtAttribute::TYPE_INT,
            'DOUBLE' => DbtAttribute::TYPE_DECIMAL,
        ];
        $attribute_category = AttrCategory::imported($row->{AttrCategory::legacyPrimaryKey()})->firstOrFail();
        $row->id_category = $attribute_category->id;
        throw_unless(array_key_exists($row->type, $type_map), \Exception::class, 'Invalid category type: ' . $row->type);
        $row->type = $type_map[$row->type];
        Validator::make((array)$row, [
            'name' => 'required|string|max:255',
            'id_category' => 'required',
            'description' => 'string|max:65535|nullable',
            'display_order' => 'nullable|integer|min:0|max:100',
            'required' => 'integer|min:0|max:1'
        ])->validate();
        //TODO Should validate the incoming array $row.
        $type_options = self::createOptionsFromLegacy($row);
        $this->update(['name' => $row->name, 'description' => $row->description, 'type' => $row->type, 'attr_category_id' => $row->id_category, 'type_options' => $type_options['config'], 'default_value' => $type_options['default_value'], 'published' => $row->required, 'display_order' => $row->display_order,]);
    }

    public static function createOptions($data)
    {
        $mapping = [
            'input_type' => $data['input_type'],
            'searchable' => $data['searchable'],
            'multiple' => $data['multiple'],
            'required' => $data['required'],
            'default_value' => $data['default_value'] ? array_values($data['default_value']) : null,
            'options' => $data['options'] ?? [],
            'type' => $data['type']
        ];
        Validator::make($mapping, [
            'input_type' => new ValidateAttributeOptions($mapping)
        ])->validate();


        $defaults = $mapping['default_value'] ?? [];
        $options = $mapping['options'] ?? [];


        //se arriva un valore di default e non ho opzioni creo un opzione di default
        if (!empty($defaults) && ($mapping['input_type'] === 'SELECT' || $mapping['input_type'] === 'CHECKBOX' && $mapping['type'] !== 'BOOLEAN')) {
            foreach ($defaults as $key => $item) {
                if (!in_array($item, $options)) {
                    //se non è in array l'opzione di default che si sta salvando la aggiungo
                    //alle opzioni disponibili
                    array_push($options, $item);
                }
            }
        }
        return [
            'config' => [
                'multiple' => $mapping['multiple'],
                'searchable' => $mapping['searchable'],
                'required' => $mapping['required'],
                'input_type' => $mapping['input_type'],
                'options' => $options,
            ],
            'default_value' => $defaults
        ];

    }

    public static function createOptionsFromLegacy($data)
    {
        try {
            $default_value = null;
            $options = [];
            $input_type = 'TEXT';

            //Rimuovo le parentesi tonde all'inizio e alla fine del enum values
            if (isset($data->enum_values)) {
                $options = $data->enum_values;
                if (substr(trim($options), 0, 1) === '(') {
                    $options = substr($data->enum_values, 1);
                }
                if (substr(trim($options), -1)) {
                    $options = substr(trim($options), 0, -1);
                }
                //Se è presente il separatore | ho multiple opzioni
                if (Str::contains($options, '|')) {
                    $options = explode('|', $options);
                } else {
                    $options = [];
                }
            }
            //Verifico la presenza di opzioni multiple nei default, se presenti le esplodo con il carattere |

            if (isset($data->defaults) && Str::contains($data->defaults, '|')) {
                $default_value = explode('|', $data->defaults);

                //abbiamo multipli default possiamo essere solo una checkbox o una select
                $input_type = ($data->type == DbtAttribute::TYPE_BOOLEAN ? 'CHECKBOX' : 'SELECT');
                //se a questo punto non è flaggato come attributo multiplo svuoto i default value e li setto come opzioni
                $options = $default_value;
                $default_value = [];
                //non ho multipli default ma ho un default
            }

            //Non esistono Checkbox multiple in ambiente legacy di produzione
            if (($data->type == DbtAttribute::TYPE_BOOLEAN)) {
                if (count($options) > 1) {
                    $input_type = 'SELECT';
                } else {
                    $input_type = 'CHECKBOX';
                }
            }


            if ($data->type == DbtAttribute::TYPE_VARCHAR) {
                if (count($options) > 1) {
                    $input_type = 'SELECT';
                }
            }

            if (($data->type == DbtAttribute::TYPE_INT || $data->type == DbtAttribute::TYPE_DECIMAL)) {
                $input_type = 'TEXT';
                $options = [];
            }


            //Gli unici multipli che abbiamo in ambiente Legacy di produzione sono per le select
            $multiple = 0;
            if ($input_type == 'SELECT') {
                $multiple = 1;
            }

            $type_options = [
                'input_type' => $input_type,
                'searchable' => ($data->searchable && (self::INPUT_TYPES_CONFIGURATION[$input_type]['options']['searchable'] !== 0) ? 1 : 0),
                'multiple' => $multiple,
                'required' => false,
                'default_value' => $default_value,
                'options' => $options,
                'type' => $data->type
            ];
            return self::createOptions($type_options);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function updateAttributeValue(AttributeValue $attribute_value, $value = null, $ingestion_id = null)
    {
        Log::channel('admin_gui')->debug('Updating Attribute Value ID: ' . $attribute_value->id . 'Incoming value: ' . print_r($value, true));
        $terminal_id = $attribute_value->terminal_id;
        $attribute = $attribute_value->dbtAttribute;
        $attribute_id = $attribute->id;
        $formatted_value = $value;
        if (!is_array($value) && $value !== null) {
            if ($attribute->getMultipleOption()) {
                if (!empty($value) && Str::contains($value, '|')) {
                    \Log::debug('Attempting to parse symbol separated values');
                    $formatted_value = array_values(explode('|', $value));
                } else {
                    \Log::debug('Casting to array multiple value attribute');
                    $formatted_value = (array)$value;
                }
            }
        }
        /*$validator = Validator::make(['attribute' => [$attribute_id => $value]], [
            'attribute' => new ValidateAttributeValue()
        ]);
        $validator->validate();*/

        if(is_array($value)){
            $value = implode(',', $value);
        }

        Log::channel('admin_gui')->debug('Terminal_ID: ' . $terminal_id . ' Incoming attribute: ' . print_r($value, true));
        $formatted_value = is_array($formatted_value) ? json_encode(array_values(array_filter($formatted_value))) : (DbtAttribute::find($attribute_id)->getMultipleOption() ? json_encode((array)$formatted_value) : $formatted_value);
        Log::channel('admin_gui')->debug('Formatted Value: ' . print_r($formatted_value, true));


        $attribute_value->update([
            'value' => $formatted_value,
            'raw_value' => $value,
            'ingestion_id' => $ingestion_id
        ]);
        return $attribute_value;
    }

    public static function createAttributeValue($attribute_id, $terminal_id, $ingestion_source_id, $value = null, $ingestion_id = null)
    {
        $attribute = DbtAttribute::find($attribute_id);
        $formatted_value = $value;
        if (!is_array($value)) {
            if ($attribute->getMultipleOption()) {
                if (!empty($value) && Str::contains($value, '|')) {
                    \Log::info('Attempting to parse symbol separated values');
                    $formatted_value = array_values(explode('|', $value));
                } else {
                    \Log::info('Casting to array multiple value attribute');
                    $formatted_value = (array)$value;
                }
            }
        }

        if ($attribute->type == DbtAttribute::TYPE_BOOLEAN && !$attribute->getMultipleOption()) {
            switch (strtolower(trim($value))) {
                case 'true':
                case '1':
                    $formatted_value = '1';
                    break;
                case 'false':
                case '0':
                case '':
                    $formatted_value = '0';
                    break;
                default:
                    Log::channel('legacy_import')->warning('Attributo di tipo booleano ma valore diverso da 0/1/true/false - casto a boolean il valore:' . print_r($value, true));
                    $formatted_value = (bool)$value;
                    break;
            }

        }

        if(is_array($value)){
            $value = implode(',', $value);
        }

        /*$validator = Validator::make(['attribute' => [$attribute_id => $formatted_value]], [
            'attribute' => new ValidateAttributeValue()
        ]);
        $validator->validate();*/
        Log::channel('admin_gui')->debug('Terminal_ID: ' . $terminal_id . ' Creating attribute value: ' . print_r($formatted_value, true));
        $formatted_value = is_array($formatted_value) ? json_encode(array_values(array_filter($formatted_value))) : ($attribute->getMultipleOption() ? json_encode((array)$formatted_value) : $formatted_value);
        AttributeValue::unguard();
        $attribute_value = AttributeValue::make([
            'dbt_attribute_id' => $attribute_id,
            'ingestion_source_id' => $ingestion_source_id,
            'value' => $formatted_value,
            'raw_value' => $value,
            'ingestion_id' => $ingestion_id,
            'terminal_id' => $terminal_id,
        ]);
        Log::channel('admin_gui')->debug(json_encode($attribute_value));
        $attribute_value->save();
        //TODO: return unsaved instance of AttributeValue. Object should be saved by caller

        AttributeValue::reguard();
        return $attribute_value;
    }

    public function getPublicValue($terminal_id, ?array $sources = null)
    {
        $prioritySource = AttributeValue::selectRaw('min(ingestion_source_id) source')->where('dbt_attribute_id', $this->id)->where('terminal_id', $terminal_id);
        return AttributeValue::where('terminal_id', $terminal_id)->where('dbt_attribute_id',$this->id)
            ->joinSub($prioritySource, 'prioritySource', function ($join)use($terminal_id) {
                $join->on('attribute_values.ingestion_source_id', '=', 'prioritySource.source')->where('terminal_id',$terminal_id)->where('dbt_attribute_id',$this->id);
            })->first();
    }

    public function getSelectOptions($attribute_value = null)
    {
        $options = [''=>''] + $this->getOptions();
        if($attribute_value && !empty($attribute_value->value) && is_array(json_decode($attribute_value->value))) {
            foreach(json_decode($attribute_value->value,true) as $saved_option) {
                $options[$saved_option] = $saved_option;
            }
        }
        return $options;
    }

    public function getSelectedOptions($attribute_value=null)
    {
        if($attribute_value){
            return $attribute_value->getFormattedValue();
        }else{
            return [];
        }
    }
}
