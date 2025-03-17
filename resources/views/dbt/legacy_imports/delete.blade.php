@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/legacy_imports.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/legacy_imports.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.legacy_imports.destroy', $import->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/legacy_imports.attributes.id'): </b> {{$import->id}}</p>
            <p><b>@lang('DBT/legacy_imports.attributes.type'): </b> {{ trans('DBT/legacy_imports.types.'.$import->type)}}</p>
            <p><b>@lang('DBT/legacy_imports.attributes.update_existing')
                    : </b> {{$import->update_existing ? trans('common.yes') : trans('common.no')}}</p>
            <p><b>@lang('DBT/legacy_imports.attributes.requested_by_id')
                    : </b> {{optional($import->requestedBy)->fullname}}</p>
            <p><b>@lang('DBT/legacy_imports.attributes.created_at'): </b> {{ optional($import->created_at)->format('d/m/Y H:i:s')}}</p>
            <p><b>@lang('DBT/legacy_imports.attributes.updated_at'): </b> {{ optional($import->updated_at)->format('d/m/Y H:i:s')}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                        class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
