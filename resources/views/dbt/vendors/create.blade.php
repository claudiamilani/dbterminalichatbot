@extends('layouts.adminlte.template',['page_title' => trans('DBT/vendors.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/vendors.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.vendors.store','class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group">
                    <label for="published" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, null, [ 'id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('DBT/vendors.attributes.published'), 'data-off' => trans('DBT/vendors.attributes.not_published'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="name" class="col-md-3 control-label"> @lang('DBT/vendors.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="col-md-3 control-label"> @lang('DBT/vendors.attributes.description')</label>
                    <div class="col-md-9">
                        {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control vertical noResize height-md']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right">
                        <i class="fas fa-save fa-fw"></i> @lang('common.form.save')
                    </button>

                    <a href="{{ route('admin::dbt.vendors.index') }}" class="btn btn-md btn-warning pull-right">
                        <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                    </a>
                </div>
                {!! Form::close() !!}
            @endslot
        @endcomponent
    </div>
@endsection
