@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminal_pictures.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/terminal_pictures.edit.title'): {{$terminal->name}}
            @endslot
            @slot('body')
                {!! Form::model($picture, ['method' => 'patch', 'route' => ['admin::dbt.terminals.pictures.update', paramsWithBackTo(['terminal_id'=>$terminal->id, 'picture_id'=>$picture->id], 'admin::dbt.terminals.show',[$terminal->id, '#'.nav_fragment('DBT/terminal_pictures.title')])], 'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group">
                    <label for="title"
                           class="col-md-3 control-label"> @lang('DBT/terminal_pictures.create.image_title')</label>
                    <div class="col-md-6">
                        {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/terminal_pictures.attributes.preview')</label>
                    <div class="col-md-9">
                        <img style="max-height:300px"
                             src="{{(Storage::disk('terminal-pictures')->url($picture->file_path))}}"
                             alt="{{ $picture->fileName }}" title="{{ $picture->fileName }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/terminal_pictures.attributes.display_order')</label>
                    <div class="col-md-6">
                        {!! Form::number('display_order',null, ['id' => 'display_order', 'class' => 'form-control', 'min'=>0]) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>

                    <a href="{{ backToSource('admin::dbt.terminals.show', $picture->terminal->id) }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
