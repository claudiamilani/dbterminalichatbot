@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/documents.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/documents.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.documents.destroy', $document->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/documents.attributes.file_path'): </b> {{$document->file_path}}</p>
            <p><b>@lang('DBT/documents.attributes.file_mime_type'): </b> {{optional($document)->file_mime_type}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
