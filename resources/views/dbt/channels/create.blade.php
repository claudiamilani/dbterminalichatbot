@extends('layouts.adminlte.template',['page_title' => trans('DBT/channels.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/channels.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.channels.store','class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group required">
                    <label for="name" class="col-md-3 control-label"> @lang('DBT/channels.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="col-md-3 control-label"> @lang('DBT/channels.attributes.description')</label>
                    <div class="col-md-9">
                        {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control noResize height-md']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                    <a href="{{ backToSource('admin::dbt.channels.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')

@endpush