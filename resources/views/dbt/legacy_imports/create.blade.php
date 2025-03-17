@extends('layouts.adminlte.template',['page_title' => trans('DBT/legacy_imports.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 12])
            @slot('title')
                @lang('DBT/legacy_imports.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.legacy_imports.store','class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group required">
                    <label for="type"
                           class="col-md-3 control-label">@lang('DBT/legacy_imports.attributes.type')</label>
                    <div class="col-md-4">
                        {!! Form::select('type[]', $types, null, [ 'id' => 'type','class' => 'form-control','style' => 'width: 100%', 'required','multiple']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="update_existing"
                           class="col-md-3 control-label">@lang('DBT/legacy_imports.attributes.update_existing')</label>
                    <div class="col-md-2">
                        {!! Form::select('update_existing', [0 => trans('common.no'),1 => trans('common.yes')], 1, [ 'id' => 'update_existing','class' => 'form-control','style' => 'width: 100%', 'required']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::dbt.legacy_imports.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class="btn btn-md btn-primary">
                        <i class="fas fa-save fa-fw"></i>@lang('common.form.save')
                    </button>
                </div>
                {!! Form::close() !!}
            @endslot
        @endcomponent
    </div>
@endsection