@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminal_configs.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/terminal_configs.edit.title'): {{$terminal_config->terminal->name}}
            @endslot
            @slot('body')
                {!! Form::model($terminal_config, ['method' => 'patch', 'route' => ['admin::dbt.terminals.configs.update', $terminal_id, $config_id], 'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) !!}
                <div class="form-group">
                    <label for="published" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, null, ['id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('DBT/terminal_configs.attributes.published'), 'data-off' => trans('DBT/terminal_configs.attributes.not_published'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>

                <div class="form-group required">
                    <label for="ota_id"
                           class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.ota')</label>
                    <div class="col-md-6">
                        {!! Form::select('ota_id', [$terminal_config->ota_id => $terminal_config->ota->name], $terminal_config->ota_id, ['id' => 'ota_id', 'class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="document_id" class="col-md-3 control-label">
                        @lang('DBT/terminal_configs.attributes.document')
                    </label>
                    <div class="col-md-6">
                        {!! Form::select('document_id', optional($terminal_config->document)->id ? [optional($terminal_config->document)->id => optional($terminal_config->document)->title] : [], optional($terminal_config->document)->id, ['id' => 'document_id', 'class' => 'form-control select2']) !!}
                    </div>
                </div>

            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>

                    <a href="{{ backToSource('admin::dbt.terminals.show', $terminal_id) }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'ota_id', 'route' => route('admin::dbt.terminals.configs.select2Otas', $config_id)])
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
    @component('components.select2_script', ['name' => 'document_id', 'route' => route('admin::dbt.terminals.configs.select2Documents', $config_id)])
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
