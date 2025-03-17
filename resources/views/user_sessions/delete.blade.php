@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('user_sessions.delete.title')
    @endslot
    @slot('content')
        <p>@lang('user_sessions.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::user_sessions.destroy', $session->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            @if($session->user_id)
                <p><b>@lang('user_sessions.attributes.nameWithMail'): </b> {{$session->account->nameWithMail}}</p>
            @endif
            <p><b>@lang('user_sessions.attributes.browser'): </b> {{$session->browser}}</p>
            <p><b>@lang('user_sessions.attributes.os'): </b> {{$session->os}}</p>
            <p><b>@lang('user_sessions.attributes.ip_address'): </b> {{$session->ip_address}}</p>
        </div>
        <div class="btn-toolbar">
            <button type="button" class="btn btn-sm btn-secondary pull-right control-btn" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
            <button type="submit" class="btn btn-sm btn-danger pull-right"><i class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete') </button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
