@component('components.modal')
    @slot('header_classes')
        bg-warning
    @endslot
    @slot('title')
        @lang('common.warning')
    @endslot
    @slot('content')
        <p>{{ $message }}</p>
        <button type="button" class="btn btn-sm btn-primary pull-right control-btn"
                data-dismiss="modal">@lang('common.form.back')</button>
    @endslot
@endcomponent