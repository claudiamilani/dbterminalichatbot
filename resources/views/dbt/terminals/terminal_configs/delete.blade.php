@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/terminal_configs.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/terminal_configs.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.terminals.configs.destroy', paramsWithBackTo([$config->terminal_id, $config->id], 'admin::dbt.terminals.show',$config->terminal_id, $config->id )], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/terminal_configs.attributes.config_id'): </b> {{ $config->id }}</p>
            <p><b>@lang('DBT/terminal_configs.attributes.terminal_name'): </b> {{ $config->terminal->name }}</p>
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

