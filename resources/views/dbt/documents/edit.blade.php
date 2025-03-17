@extends('layouts.adminlte.template',['page_title' => trans('DBT/documents.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/documents.edit.title')
            @endslot
            @slot('body')
                {!! Form::model($document, ['method' => 'patch', 'route' => ['admin::dbt.documents.update', $document->id],'class' => 'form-horizontal', 'files' => true]) !!}
                <div class="form-group">
                    <label for="title" class="col-md-3 control-label">@lang('DBT/documents.attributes.title')</label>
                    <div class="col-md-9">
                        {!! Form::text('title', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="file_path"
                           class="col-md-3 control-label"> @lang('DBT/documents.attributes.file_path_uploaded')</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{{$document->file_path}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="file_path" class="col-md-3 control-label"> @lang('DBT/documents.attributes.file_path')</label>
                    <div class="col-md-9">
                        {!! Form::file('file_path', ['id' => 'file_path', 'class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-12 col-md-offset-3">
                        <small><i class="fa fa-info-circle text-info"></i> @lang('DBT/documents.placeholder_hints.file_types')
                        </small>
                    </div>
                </div>

                <div class="form-group required">
                    <label for="document_type_id"
                           class="col-md-3 control-label"> @lang('DBT/documents.attributes.document_type_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('document_type_id', $document_type_id, $document_type_id, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                @slot('footer')
                    <div class="btn-toolbar">
                        <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                        <a href="{{ backToSource('admin::dbt.documents.index') }}"
                           class="btn btn-md btn-warning pull-right"><i
                                    class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                    </div>
                @endslot
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'document_type_id', 'route' => route('admin::dbt.document_types.select2')])
        @slot('format_selection')
            if(output.id !== '') {
            output = output.text
            return output;
            }
            return '';
        @endslot
        @slot('format_output')
            if(!output.loading) {
            output = output.text + ' - ' + '<b>{{trans('DBT/document_types.attributes.channel_id')}}</b>' + ': ' + output.channel
            return output;
            }
            return output.text;
        @endslot
    @endcomponent
@endpush