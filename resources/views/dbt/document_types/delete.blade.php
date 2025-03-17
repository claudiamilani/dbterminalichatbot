@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/document_types.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/document_types.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.document_types.destroy', $documentType->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/document_types.attributes.name'): </b> {{$documentType->name}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
