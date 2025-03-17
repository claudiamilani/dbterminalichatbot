@extends('layouts.adminlte.template',['page_title' => trans('DBT/attributes.title')])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8])
            @slot('title')
                @lang('DBT/attributes.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.attributes.store','class' => 'form-horizontal']) !!}
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, 1, [ 'id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.published'), 'data-off' => trans('common.unpublished'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="attr_category_id"
                           class="col-md-3 control-label">@lang('DBT/attributes.attributes.attr_category_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('attr_category_id',[] ,null, [ 'id' => 'attr_category_id','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="name"
                           class="col-md-3 control-label">@lang('DBT/attributes.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, [ 'id' => 'name','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/attributes.attributes.display_order')</label>
                    <div class="col-md-9">
                        {!! Form::number('display_order',0, ['id' => 'display_order', 'class' => 'form-control', 'min'=>0]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="col-md-3 control-label">@lang('DBT/attributes.attributes.description')</label>
                    <div class="col-md-9">
                        {!! Form::textarea('description',null, ['id' => 'description', 'class' => 'form-control height-md noResize ']) !!}
                    </div>
                </div>

                <fieldset>
                    <legend>@lang('DBT/attributes.attributes.type_options')</legend>
                    <div class="form-group required">
                        <label for="type"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.type')</label>
                        <div class="col-md-9">
                            {!! Form::select('type', $types, null,[ 'id' => 'type','class' => 'form-control','required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input_type"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.input_type')</label>
                        <div class="col-md-9">
                            {!! Form::select('input_type', $input_types, null, [ 'id' => 'input_type','class' => 'form-control']) !!}
                        </div>
                        <label for="input_type"
                               class="col-md-3 control-label">@lang('')</label>
                        <div class="col-md-9">
                            <small><i class="fa fa-info-circle text-info"></i> <b>@lang('DBT/attributes.placeholder_hints.available_options')</b>
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="required"
                               class="col-md-3 control-label">@lang('DBT/attributes.type_options.required')</label>
                        <div class="col-md-1">
                            <input type="hidden" name="required" value="0">
                            {!! Form::checkbox('required', 1, 0, ['id' => 'required', 'class' => 'form-control type_option_check']) !!}
                        </div>
                        <label for="multiple"
                               class="col-md-3 control-label">@lang('DBT/attributes.type_options.multiple')</label>
                        <div class="col-md-1">
                            <input type="hidden" name="multiple" value="0">
                            {!! Form::checkbox('multiple', 1, 0, ['id' => 'multiple', 'class' => 'form-control type_option_check']) !!}
                        </div>
                        <label for="searchable"
                               class="col-md-3 control-label">@lang('DBT/attributes.type_options.searchable')</label>
                        <div class="col-md-1">
                            <input type="hidden" name="searchable" value="0">
                            {!! Form::checkbox('searchable', 1, 0, ['id' => 'searchable', 'class' => 'form-control type_option_check']) !!}
                        </div>

                    </div>
                    <div class="form-group " id="options_div">
                        <label for="type_options"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.options')</label>
                        <div class="col-md-9">
                            {!! Form::select('options[]', [], null , [ 'id' => 'options','class' => 'form-control', 'multiple']) !!}
                        </div>

                    </div>

                    <div class="form-group" id="default_value_div">
                        <label for="default_value"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.default_value')</label>
                        <div class="col-md-9">
                            {!! Form::select('default_value[]',[],null, ['id' => 'default_value_select', 'class' => 'form-control', 'multiple']) !!}
                        </div>
                    </div>

                    <div class="form-group" id="default_bool_div">
                        <label for="default_value_bool"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.default_value')</label>
                        <div class="col-md-9">
                            {!! Form::select('default_bool_value',[0=>'False',1=>'True'],0, ['id' => 'default_bool_value', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                </fieldset>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::dbt.attributes.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class="btn btn-md btn-primary">
                        <i class="fas fa-save fa-fw"></i>@lang('common.form.save')
                    </button>
                </div>
            @endslot
            {!! Form::close() !!}
        @endcomponent
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'attr_category_id', 'route' => route('admin::dbt.attributes.select2Category')])
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
    @component('components.select2_script', ['name' => 'ingestion_id', 'route' => route('admin::dbt.attributes.select2Ingestion')])
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
    @component('components.select2_script', ['name' => 'ingestion_source_id', 'route' => route('admin::dbt.attributes.select2ingestionSource')])
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
    @component('components.select2_script', ['name' => 'options[]', 'route' => route('admin::dbt.attributes.select2TypeOptions'), 'tags'=>true, 'minChars'=>1,'inputTooShort'=>trans('DBT/attributes.input_too_short')])
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
    @component('components.select2_script', ['name' => 'default_value[]', 'route' => route('admin::dbt.attributes.select2TypeOptions'), 'tags'=>true, 'minChars'=>1, 'inputTooShort'=>trans('DBT/attributes.empty_default_value')])
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
    <script>
        $('.type_option_check').iCheck({
            checkboxClass: 'icheckbox_square-orange pull-left',
            increaseArea: '20%' // optional
        });
    </script>
    <script>

        let input_type = $('#input_type');
        let type = $('#type');

        $(document).ready(function () {
            loadInputTypeOptions()
        });
        input_type.on('change', function () {
            loadInputTypeOptions()
        })
        type.on('change', function () {
            loadOptions()
        })
        function loadInputTypeOptions(){
            var selectedType = $('#input_type').val();
            $.ajax({
                url: '{{ route('admin::dbt.attributes.loadInputTypeOptions') }}',
                type: 'POST',
                data: {'input_type':selectedType},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    $.each(result.input_types_config.options, function(index, value) {
                        if (value === 1) {
                            $('#' + index).iCheck('check').iCheck('enable');
                        } else if (value === 0) {
                            $('#' + index).iCheck('uncheck').iCheck('disable');
                        } else if (value === null) {
                            $('#' + index).iCheck('enable');
                        }
                    });
                    if(type.val() === '{{\App\DBT\Models\DbtAttribute::TYPE_BOOLEAN}}'){
                        $('#multiple').iCheck('disable');
                        $('#options_div').hide()
                        $('#options').attr('disabled')
                        $('#default_value_div').hide()
                        $('#default_value').attr('disabled')
                        $('#default_bool_div').show()
                        $('#default_bool_value').attr('enabled')
                    } else{
                        $('#options').attr('disabled',false)
                        $('#options_div').show()
                        $('#default_bool_div').hide()
                        $('#default_bool_value').attr('disabled')
                        $('#default_value').attr('enabled')
                        $('#default_value_div').show()
                    }
                    if( ((input_type.val() !== "SELECT") && (input_type.val() !== "CHECKBOX"))){
                        $('#options_div').hide()
                        $('#options').attr('disabled')
                        $('#default_bool_div').hide()
                        $('#default_bool_value').attr('disabled')
                        $('#default_value').attr('enabled')
                        $('#default_value_div').show()
                    }


                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });

        }

        function loadOptions(){
            let selected_type = $('#type').val()
            let selected_option = $("#input_type").val()
            $.ajax({
                url: '{{ route('admin::dbt.attributes.loadOptions') }}',
                type: 'POST',
                data: {'type':selected_type, 'input_type':selected_option},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    $("#input_type").empty()
                    $.each(result.type_config, function(index, value) {
                        $('#input_type').append($('<option>', {
                            value: index,
                            text: value
                        }));
                    });
                    loadInputTypeOptions()

                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });

        }
    </script>
@endpush