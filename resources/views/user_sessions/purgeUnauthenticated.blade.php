@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('user_sessions.purge.title')
    @endslot
    @slot('content')
        <p>@lang('user_sessions.purge.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::user_sessions.destroySessions','unauthenticated'], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="btn-toolbar">
            <button type="button" class="btn btn-sm btn-secondary pull-right control-btn" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back') </button>
            <button type="submit" class="btn btn-sm btn-danger pull-right"><i class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
