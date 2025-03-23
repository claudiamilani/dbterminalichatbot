@component('components.widget', ['size' => 12])
    @slot('title')
        @lang('DBT/attributes.title')
    @endslot
    @slot('body')
        @foreach($categories as $category)
            @component('components.widget', ['size'=>12,'collapsible'=>true])
                @slot('title')
                    {{$category->description ?? $category->name}}
                @endslot
                @slot('body')
                    @component('components.table-list')
                        @slot('body')
                            @foreach($attributes->where('attr_category_id',$category->id) as $attribute)
                                <tr>
                                    <td class="col-md-6">
                                        <div class="form-group {{$attribute->getRequiredOption()}}"
                                             style="word-break: break-all; margin-left:0">
                                            <label><b>{{$attribute->description ??$attribute->name }}</b></label>
                                        </div>
                                    </td>
                                    <td class="col-md-6">
                                        <div class="">
                                            <b>Utente</b>
                                        </div>
                                        @switch($attribute->getInputTypeOption())
                                            @case('TEXT')
                                                <div class="">
                                                    {!! Form::text($attribute->id,  $attribute->getDefaultValue(), ['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id, ]) !!}
                                                </div>
                                                @break
                                            @case('TEXTAREA')
                                                <div class="">
                                                    {!! Form::textarea($attribute->id,  $attribute->getDefaultValue(), ['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id, ]) !!}
                                                </div>
                                                @break
                                            @case('CHECKBOX')
                                                <div class="">
                                                    @if($attribute->getMultipleOption())
                                                        @foreach($attribute->getOptions() as $option)
                                                            <div style="display:flex;align-items:  center; margin-top:10px">
                                                                {!! Form::checkbox($attribute->id .'[]', $option, in_array($option,$attribute->getDefaultValue() ?? [] ) ? 1 : 0,[ $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id , 'class'=>'mdm_checkbox checkbox_'.$attribute->id , 'multiple']) !!}
                                                                <label style="margin-left:5px"
                                                                       for="{{$attribute->id .'[]'}}">
                                                                    {{$option}}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @elseif($attribute->type === 'BOOLEAN')
                                                        <input name="{{$attribute->id}}" type="hidden" value="0">
                                                        {!! Form::checkbox($attribute->id, 1 ,null, ['id'=>'attribute_'.$attribute->id, 'class'=>'mdm_checkbox checkbox_'.$attribute->id ]) !!}
                                                    @else
                                                        <input name="{{$attribute->id}}" type="hidden" value="{{null}}">
                                                        {!! Form::checkbox($attribute->id, Arr::first($attribute->getOptions()), null, ['id'=>'attribute_'.$attribute->id, 'class'=>'mdm_checkbox checkbox_'.$attribute->id ]) !!}
                                                    @endif
                                                </div>
                                                @break
                                            @case('SELECT')
                                                @if($attribute->getMultipleOption())
                                                    {!! Form::select($attribute->id .'[]',$attribute->getSelectOptions(),$attribute->getDefaultValue(),['class' => 'form-control', $attribute->getRequiredOption(), 'multiple', 'id'=>'attribute_'.$attribute->id]) !!}
                                                @else
                                                    {!! Form::select($attribute->id,$attribute->getSelectOptions(),$attribute->getDefaultValue(),['class' => 'form-control', $attribute->getRequiredOption(), 'id'=>'attribute_'.$attribute->id]) !!}
                                                @endif
                                                @break
                                            @case('NUMBER')
                                                <div class="">
                                                    {!! Form::number($attribute->id, $attribute->getDefaultValue(),['class' => 'form-control', $attribute->getRequiredOption(), $attribute->getDecimals(), 'id'=>'attribute_'.$attribute->id ] ) !!}
                                                </div>
                                                @break
                                            @default
                                                <div class="">
                                                    {!! Form::text($attribute->id,   $attribute->getDefaultValue(), ['class' => 'form-control']) !!}
                                                </div>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
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
        {{Form::close()}}
    @endslot
@endcomponent
@push('scripts')
    <script>
        $('.mdm_checkbox').iCheck({
            checkboxClass: 'icheckbox_square-orange',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    </script>
@endpush
