@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminals.title'), 'fa_icon_class' => ''])
@section('page_nav_default')
    @component('components.page_navigation')@endcomponent
@endsection
@push('styles')
@endpush
@section('content')
    {!! Form::model($terminal, ['method' => 'patch', 'route' => ['admin::dbt.terminals.update', $terminal->id],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/terminals.edit.title')
            @endslot
            @slot('body')
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, $terminal->published, [ 'id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.published'), 'data-off' => trans('common.unpublished'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="certified" value="0">
                        {!! Form::checkbox('certified', 1, null, [ 'id' => 'certified','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.certified'), 'data-off' => trans('common.uncertified'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="name" class="col-md-3 control-label"> @lang('DBT/terminals.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="vendor_id"
                           class="col-md-3 control-label"> @lang('DBT/terminals.attributes.vendor_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('vendor_id', $vendor_id, $vendor_id, ['id' => 'vendor_id', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="ota_vendor"
                           class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ota_vendor')</label>
                    <div class="col-md-9">
                        {!! Form::text('ota_vendor', null, ['id' => 'ota_vendor', 'class' => 'form-control','disabled']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="ota_model"
                           class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ota_model')</label>
                    <div class="col-md-9">
                        {!! Form::text('ota_model', null, ['id' => 'ota_model', 'class' => 'form-control','disabled']) !!}
                    </div>
                </div>
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
    </div>
    <div class="row">
        @include('dbt.terminals.attributes.edit')
        {{Form::close()}}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'vendor_id', 'route' => route('admin::dbt.terminals.select2Vendors')])
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