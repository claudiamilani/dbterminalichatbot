@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('users.delete.title')
    @endslot
    @slot('content')
        <p>@lang('users.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::users.destroy', $account->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('users.attributes.fullname'): </b> {{$account->fullName}}</p>
            <p><b>@lang('users.attributes.email'): </b> {{$account->email}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
