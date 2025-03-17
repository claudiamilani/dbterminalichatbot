@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/attribute_header_mappings.import.title')
    @endslot
    @slot('content')
        <p> @lang('DBT/attribute_header_mappings.import.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.ingestion_sources.attribute_header_mappings.import', $id], 'method' => 'POST', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="btn-toolbar">
            <button type="button" class="btn btn-sm btn-warning pull-right" data-dismiss="modal">
                <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
            </button>
            <button type="submit" class="btn btn-sm btn-primary pull-right">
                <i class="fas fa-fw fa-save"></i> @lang('common.form.confirm')
            </button>
        </div>

        {!! Form::close() !!}
    @endslot
@endcomponent
