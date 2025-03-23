@component('components.widget', [
    'size' => 12,
    'collapsible' => true,
    'hide_required_legend' => true,
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
                                    <td class="">
                                        <div class="form-group"
                                             style="word-break: break-all" id="{{'attr_'.$attribute->id}}">
                                            <label><b>{{$attribute->description ?? $attribute->name}}</b></label>
                                        </div>
                                    </td>
                                    <td class="col-md-3">
                                        <div class="">
                                            <b>Utente</b>
                                        </div>
                                        <p> {{optional($attribute->attributeValues->where('ingestion_source_id',\App\DBT\Models\IngestionSource::SRC_ADMIN)->first())->getReadableValue()}}</p>
                                    </td>
                                    @foreach($ingestion_sources->where('id','!=',1) as $ingestion_source)
                                        <td class="col-md-3">
                                            <div class="">
                                                <b>{{$ingestion_source->name}}</b>
                                            </div>
                                            <p> {{optional($attribute->attributeValues->where('ingestion_source_id',$ingestion_source->id)->first())->getReadableValue()}}</p>
                                        </td>
                                    @endforeach
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
            <a href="{{ backToSource('admin::dbt.terminals.index') }}"
               class="btn btn-md btn-warning pull-right"><i
                        class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
        </div>
        {!! Form::close() !!}
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
@endpush
