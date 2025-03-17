@component('components.modal')

    @slot('header_classes')
        bg-danger
    @endslot

    @slot('title')
        @lang('DBT/vendors.delete.title')
    @endslot

    @slot('content')
        <p> @lang('DBT/vendors.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.vendors.destroy', $vendor->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}

        <div class="form-group">
            <p><b> @lang('DBT/vendors.attributes.name'): </b>{{$vendor->name}} </p>
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
