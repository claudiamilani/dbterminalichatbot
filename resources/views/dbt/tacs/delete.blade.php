@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/tacs.delete.title')
    @endslot
    @slot('content')
        <p> @lang('DBT/tacs.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.tacs.destroy', paramsWithBackTo($tac->id)], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}

        <div class="form-group">
            <p><b> @lang('DBT/tacs.attributes.terminal_id'): </b>{{$tac->terminal->name}}</p>
            <p><b> @lang('DBT/tacs.attributes.value'): </b>{{$tac->value}} </p>
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
