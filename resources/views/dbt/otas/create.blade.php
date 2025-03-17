@extends('layouts.adminlte.template',['page_title' => trans('DBT/otas.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/otas.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.otas.store','class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group">
                    <label for="published" class="col-md-3 control-label"></label>
                </div>
                <div class="form-group required">
                    <label for="name" class="col-md-3 control-label"> @lang('DBT/otas.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="type" class="col-md-3 control-label"> @lang('DBT/otas.attributes.type')</label>
                    <div class="col-md-9">
                        {!! Form::text('type', null, ['id' => 'type', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="sub_type" class="col-md-3 control-label"> @lang('DBT/otas.attributes.sub_type')</label>
                    <div class="col-md-9">
                        {!! Form::text('sub_type', null, ['id' => 'sub_type', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="ext_0" class="col-md-3 control-label"> @lang('DBT/otas.attributes.ext_0')</label>
                    <div class="col-md-9">
                        {!! Form::text('ext_0', null, ['id' => 'ext_0', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="ext_number" class="col-md-3 control-label"> @lang('DBT/otas.attributes.ext_number')</label>
                    <div class="col-md-9">
                        {!! Form::text('ext_number', null, ['id' => 'ext_number', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right">
                        <i class="fas fa-save fa-fw"></i> @lang('common.form.save')
                    </button>

                    <a href="{{ route('admin::dbt.otas.index') }}" class="btn btn-md btn-warning pull-right">
                        <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                    </a>
                </div>
                {!! Form::close() !!}
            @endslot
        @endcomponent
    </div>
@endsection
