@component('components.modal')
    @slot('header_classes')
        bg-warning
    @endslot
    @slot('title')
        @lang('roles.default_permissions.title')
    @endslot
    @slot('content')
        <p class="text-center">@lang('roles.default_permissions.confirm_msg')</p>
        {!! Form::open(['route' => 'admin::roles.defaultPermissions', 'method' => 'post', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-primary control-btn"><i
                        class="fas fa-save fa-fw"></i> @lang('common.form.confirm')</button>
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
