@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/transpose_configs.delete.title')
    @endslot
    @slot('content')
        <p> @lang('DBT/transpose_configs.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.transpose_configs.destroy', $config->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}

        <div class="form-group">
            <p><b> @lang('DBT/transpose_configs.attributes.dbt_attribute_id'): </b>{{$config->dbtAttribute->name}}</p>
            <p><b> @lang('DBT/transpose_configs.attributes.label'): </b>{{$config->label}} </p>
        </div>

        <div class="btn-toolbar">
            <button type="button" class="btn btn-sm btn-warning pull-right" data-dismiss="modal">
                <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
            </button>
            <button type="submit" class="btn btn-sm btn-danger pull-right">
                <i class="fas fa-fw fa-trash-alt"></i> @lang('common.form.delete')
            </button>
        </div>

        {!! Form::close() !!}
    @endslot
@endcomponent
