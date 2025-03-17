@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('common.forbidden_title')
    @endslot
    @slot('content')
        <p class="text-center">{{ $message }}</p>
        <button type="button" class="btn btn-sm btn-primary pull-right control-btn"
                data-dismiss="modal">@lang('common.form.back')</button>
    @endslot
@endcomponent