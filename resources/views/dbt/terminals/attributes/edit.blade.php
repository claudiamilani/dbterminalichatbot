@component('components.widget', [
    'size' => 12,
    'collapsible' => true,
    'withAnchor' => str_slug(trans('DBT/attributes.title')),
    ])
    @slot('extraToolbar')
        <div class="btn-toolbar">
            <div class="input-group input-group-sm" style="max-width:250px;">
                {!! Form::select('attr_category_id', $categories_filter, null, ['id' => 'attr_category_id', 'class' => 'form-control pull-right', 'placeholder' => trans('DBT/terminals.attributes.attr_category_id') . ':']) !!}
            </div>
            <div class="input-group input-group-sm" style="max-width:250px;">
                {!! Form::select('dbt_attribute_id', [], null, ['id' => 'dbt_attribute_id', 'class' => 'form-control pull-right']) !!}
            </div>
        </div>
    @endslot
    @slot('title')
        @lang('DBT/attributes.title')
    @endslot
    @slot('body')
        @foreach($categories as $category)
            @component('components.widget', ['size'=>12,'collapsible'=>true])
                @slot('title')
                    <div id="{{'attr_cat_'.$category->id}}">
                        {{$category->description ?? $category->name}}
                    </div>
                @endslot
                @slot('body')
                    @component('components.table-list')
                        @slot('head')
                        @endslot
                        @slot('body')
                            @foreach($attributes->where('attr_category_id',$category->id) as $attribute)
                                <tr>
                                    {{--<td class="col-md-1">
                                        @can('update', \App\DBT\Models\AttributeValue::class)
                                            <a id="save_attribute_{{$attribute->id}}"
                                               href="{{ route('admin::dbt.terminals.updateAttributeModal', ['terminal_id'=>$terminal->id, 'attribute_id'=>$attribute->id]) }}"
                                               title="@lang('Aggiorna')"
                                               class="btn btn-sm btn-primary "
                                               data-toggle="modal" data-remote="false"
                                               data-target="#myModal">
                                                <i class="fas fa-pen fa-fw"></i>
                                            </a>
                                        @endcan
                                        @can('forceDelete',\App\DBT\Models\AttributeValue::class)
                                            <a id="save_attribute_{{$attribute->id}}"
                                               href="{{ route('admin::dbt.terminals.forceDeleteAttributeModal', ['terminal_id'=>$terminal->id, 'attribute_id'=>$attribute->id]) }}"
                                               title="@lang('Rimuovi')"
                                               class="btn btn-sm btn-danger "
                                               data-toggle="modal" data-remote="false"
                                               data-target="#myModal">
                                                <i class="fas fa-trash fa-fw"></i>

                                        @endcan
                                    </td>--}}
                                    <td class="">
                                        <div class="form-group {{$attribute->getRequiredOption()}}"
                                             style="word-break: break-all; margin-left:0" id="{{'attr_'.$attribute->id}}">
                                            <label><b>{{ $attribute->description ?? $attribute->name }}</b></label>
                                        </div>
                                    </td>
                                    <td class="col-md-3">
                                        <div class="">
                                            <b>Wind</b>
                                        </div>
                                        @switch($attribute->getInputTypeOption())
                                            @case('TEXT')
                                                <div class="">
                                                    {!! Form::text($attribute->id, $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ? $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value : null, ['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id, ]) !!}
                                                </div>
                                                @break
                                            @case('TEXTAREA')
                                                <div class="">
                                                    {!! Form::textarea($attribute->id, $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ? $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value : null, ['class' => 'form-control noResize height-md', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id, ]) !!}
                                                </div>
                                                @break
                                            @case('CHECKBOX')
                                                <div class="">
                                                    @if($attribute->getMultipleOption())
                                                        @foreach($attribute->getOptions() as $option)
                                                            <div style="display:flex;align-items:  center; margin-top:10px">
                                                                {!! Form::checkbox($attribute->id .'[]', $option ,($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() && in_array($option,json_decode($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value,true)) ) ? 1 : 0, [ $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id , 'class'=>'mdm_checkbox checkbox_'.$attribute->id , 'multiple']) !!}
                                                                <label style="margin-left:5px"
                                                                       for="{{$attribute->id .'[]'}}">
                                                                    {{$option}}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @elseif($attribute->type === 'BOOLEAN')
                                                        <input name="{{$attribute->id}}" type="hidden" value="0">
                                                        {!! Form::checkbox($attribute->id, 1 ,$attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ? ($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value == '1' ? 1  : 0) : 0, ['id'=>'attribute_'.$attribute->id, 'class'=>'mdm_checkbox checkbox_'.$attribute->id ]) !!}
                                                    @else
                                                        <input name="{{$attribute->id}}" type="hidden" value="{{null}}">
                                                        {!! Form::checkbox($attribute->id, Arr::first($attribute->getOptions()) ,$attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ? $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value : null, ['id'=>'attribute_'.$attribute->id, 'class'=>'mdm_checkbox checkbox_'.$attribute->id ]) !!}
                                                    @endif
                                                </div>
                                                @break
                                            @case('SELECT')
                                                @if($attribute->getMultipleOption())
                                                    {!! Form::select($attribute->id .'[]',$attribute->getSelectOptions($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()),$attribute->getSelectedOptions($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()),['class' => 'form-control', $attribute->getRequiredOption(), 'multiple', 'id'=>'attribute_'.$attribute->id]) !!}
                                                @else
                                                    {!! Form::select($attribute->id,$attribute->getSelectOptions($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()),$attribute->getSelectedOptions($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()),['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id]) !!}
                                                @endif
                                                @break
                                            @case('NUMBER')
                                                <div class="">
                                                    {!! Form::number($attribute->id, $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ?$attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value:  null,['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id, 'step'=>$attribute->getDecimals()] ) !!}
                                                </div>
                                                @break
                                            @default
                                                <div class="">
                                                    {!! Form::text($attribute->id, $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first() ? $attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first()->value :  null, ['class' => 'form-control']) !!}
                                                </div>
                                                @break
                                        @endswitch
                                    </td>
                                    @foreach($ingestion_sources->where('id','!=',1) as $ingestion_source)
                                        <td class="col-md-3">
                                            <div class="">
                                                <b>{{$ingestion_source->name}}</b>
                                            </div>
                                            @switch($attribute->getInputTypeOption())
                                                @case('SELECT')
                                                    {!! Form::select($attribute->id .'_'. $ingestion_source->name .'[]',$attribute->getSelectedOptions($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first()),$attribute->getSelectedOptions($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first()),['class' => 'form-control', $attribute->getRequiredOption(), 'multiple', 'id'=>'attribute_'.$attribute->id, 'disabled']) !!}
                                                    @break
                                                @case('CHECKBOX')
                                                    @if($attribute->getMultipleOption())
                                                        @foreach($attribute->getOptions() as $option)
                                                            <div style="display:flex;align-items:  center; margin-top:10px">
                                                                {!! Form::checkbox($attribute->id .'_'. $ingestion_source->name .'[]', $option ,($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first() && in_array($option,json_decode($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first()->value,true)) ) ? 1 : 0, ['id'=>'attribute_'.$attribute->id , 'class'=>'mdm_checkbox checkbox_'.$attribute->id , 'multiple', 'disabled']) !!}
                                                                <label style="margin-left:5px"
                                                                       for="{{$attribute->id .'_'. $ingestion_source->name .'[]'}}">{{$option}}</label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        {!! Form::checkbox($attribute->id .'_'. $ingestion_source->name, '1' ,$attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first() ? ($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first()->value == '1' ? 1  : 0) :  [], ['id'=>'attribute_'.$attribute->id, 'class'=>'mdm_checkbox checkbox_'.$attribute->id,'disabled' ]) !!}
                                                    @endif
                                                    @break
                                                @default
                                                    <div class="">
                                                        {!! Form::text($attribute->id .'_'. $ingestion_source->name,$attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first() ? $attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first()->value : null, ['disabled', 'class'=>'form-control']) !!}
                                                    </div>
                                                    @break;
                                            @endswitch
                                        </td>
                                    @endforeach
                                    @if($attribute->getSearchableOption())
                                        @push('scripts')
                                            @component('components.select2_script', ['name'=>$attribute->id . ($attribute->getMultipleOption() ? '[]' : ''),'route'=>route('admin::dbt.attributes.select2SearchableAttributes')])
                                                @slot('dataParams')
                                                    attribute_id: '{{$attribute->id}}'
                                                @endslot
                                                @slot('format_selection')
                                                    if(output.id !== '') {
                                                    output = output.text
                                                    return output;
                                                    }
                                                    return '';
                                                @endslot
                                                @slot('format_output')
                                                    if(!output.loading) {
                                                    output = output.text
                                                    return output;
                                                    }
                                                    return output.text;
                                                @endslot
                                            @endcomponent
                                        @endpush
                                    @endif
                                </tr>

                            @endforeach
                        @endslot
                    @endcomponent
                @endslot
            @endcomponent
        @endforeach
    @endslot
    @slot('footer')
        <div class="btn-toolbar">
            <button type="submit" class="btn btn-md btn-primary pull-right"><i
                        class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
            <a href="{{ backToSource('admin::dbt.terminals.index') }}"
               class="btn btn-md btn-warning pull-right"><i
                        class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
        </div>
    @endslot
@endcomponent
@push('scripts')
    @component('components.select2_script',['name' => 'dbt_attribute_id', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.attributes.select2'), 'linkedClear'=>'', 'callback'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/terminals.attributes.dbt_attribute_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
    <script>
        $('#attr_category_id').on('change', function (e) {
            $('html, body').animate({scrollTop: $('#attr_cat_' +this.value).offset().top}, "slow");
        });
        $('#dbt_attribute_id').on('change', function (e) {
            $('html, body').animate({scrollTop: $('#attr_' +this.value).offset().top}, "slow");
        });
    </script>
    <script>
        $('.mdm_checkbox').iCheck({
            checkboxClass: 'icheckbox_square-orange',
            radioClass: 'iradio_square-orange',
            increaseArea: '40%' // optional
        });
    </script>
@endpush