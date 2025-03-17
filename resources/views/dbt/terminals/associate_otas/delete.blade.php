@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/terminal_associate_otas.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/terminal_associate_otas.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.terminals.unlink', $terminal], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}

        <div class="form-group">
            <p><b>@lang('DBT/terminal_associate_otas.attributes.ota_vendor'): </b> {{ $terminal->ota_vendor }}</p>
            <p><b>@lang('DBT/terminal_associate_otas.attributes.ota_model'): </b> {{ $terminal->ota_model }}</p>
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

