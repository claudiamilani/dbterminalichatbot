@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/dwh_operations.create.title'): {{$type}}
    @endslot
    @slot('content')
        <p>@lang('DBT/dwh_operations.create.confirm_msg') {{$type}} </p>
        {!! Form::open(['route' => ['admin::dbt.dwh_operations.executeCreate'], 'method' => 'post', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
            <input type="hidden" name="type" value="{{$type}}">
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-primary"><i
                        class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
