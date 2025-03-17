@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('pending_pwd_resets.delete.title')
    @endslot
    @slot('content')
        <p>@lang('pending_pwd_resets.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::pending_pwd_resets.destroy', $pending_pwd_reset->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('pending_pwd_resets.attributes.fullname_withmail'): </b> {{$pending_pwd_reset->account->NameWithMail}}</p>
            <p><b>@lang('pending_pwd_resets.attributes.account'): </b> {{$pending_pwd_reset->user}}</p>
            <p><b>@lang('pending_pwd_resets.attributes.ipv4'): </b> {{$pending_pwd_reset->ipv4}}</p>
            <p><b>@lang('pending_pwd_resets.attributes.created_at'): </b> {{optional($pending_pwd_reset->created_at)->format('d/m/Y H:i')}}</p>
        </div>
        <div class="btn-toolbar">
            <button type="button" class="btn btn-sm btn-secondary pull-right control-btn" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
            <button type="submit" class="btn btn-sm btn-danger pull-right"><i class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent