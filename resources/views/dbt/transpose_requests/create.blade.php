@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/transpose_requests.create.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/transpose_requests.create.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.transpose_requests.store'], 'method' => 'post', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-primary"><i
                        class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                        class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
