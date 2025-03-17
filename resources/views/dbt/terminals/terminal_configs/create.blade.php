@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminal_configs.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/terminal_configs.create.title'): {{$terminal->name}}
            @endslot
            @slot('body')
                {!! Form::open(['route' => ['admin::dbt.terminals.configs.store', paramsWithBackTo($terminal->id, 'admin::dbt.terminals.show',[$terminal->id,'#'.nav_fragment('DBT/terminal_configs.title')])],'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) !!}
                <div class="form-group">
                    <label for="published" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, null, [ 'id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('DBT/terminal_configs.attributes.published'), 'data-off' => trans('DBT/terminal_configs.attributes.not_published'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>

                <div class="form-group required">
                    <label for="ota_id"
                           class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.ota')</label>
                    <div class="col-md-6">
                        {!! Form::select('ota_id', [], null, ['id' => 'ota_id', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="document_id"
                           class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.document')</label>
                    <div class="col-md-6">
                        {!! Form::select('document_id', [], null, ['id' => 'document_id', 'class' => 'form-control']) !!}
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>

                    <a href="{{ backToSource('admin::dbt.terminals.show', $terminal->id) }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection

@push('scripts')
    @component('components.select2_script', ['name' => 'ota_id', 'route' => route('admin::dbt.terminals.configs.select2Otas', $terminal->id)])
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
    @component('components.select2_script', ['name' => 'document_id', 'route' => route('admin::dbt.terminals.configs.select2Documents', $terminal->id)])
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
