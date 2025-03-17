@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminal_pictures.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/terminal_pictures.create.title'): {{$terminal->name}}
            @endslot
            @slot('body')
                {!! Form::open(['route' => ['admin::dbt.terminals.pictures.store', paramsWithBackTo($terminal->id, 'admin::dbt.terminals.show',[$terminal->id])],'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) !!}
                <div class="form-group">
                    <label for="title" class="col-md-3 control-label"> @lang('DBT/terminal_pictures.create.image_title')</label>
                    <div class="col-md-6">
                        {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group required">
                    <label for="file_path"
                           class="col-md-3 control-label"> @lang('DBT/terminal_pictures.attributes.file_path')</label>
                    <div class="col-md-6">
                        {!! Form::file('file_path', ['id' => 'file_path', 'class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="col-md-12 col-md-offset-3">
                        <small><i class="fa fa-info-circle text-info"></i> @lang('DBT/terminal_pictures.placeholder_hints.file_types')
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/terminal_pictures.create.display_order')</label>
                    <div class="col-md-6">
                        {!! Form::number('display_order', 0, ['class' => 'form-control','required', 'min'=>0]) !!}
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
