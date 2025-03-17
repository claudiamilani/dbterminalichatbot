@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/attribute_header_mappings.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/attribute_header_mappings.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.ingestion_sources.attribute_header_mappings.destroy', paramsWithBackTo($mapping->id)], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/attribute_header_mappings.attributes.header_name'): </b> {{$mapping->header_name}}</p>
            <p><b>@lang('DBT/attribute_header_mappings.attributes.dbt_attribute_id'): </b> {{$mapping->dbtAttribute->name}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
