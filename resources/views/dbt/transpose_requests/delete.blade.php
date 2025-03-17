@component('components.modal')
    @slot('header_classes')
        bg-danger
    @endslot
    @slot('title')
        @lang('DBT/transpose_requests.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/transpose_requests.delete.confirm_msg')</p>
        {!! Form::open(['route' => ['admin::dbt.transpose_requests.destroy', $tr_request->id], 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px']) !!}
        <div class="form-group">
            <p><b>@lang('DBT/transpose_requests.attributes.id'): </b> {{$tr_request->id}}</p>
            <p><b>@lang('DBT/transpose_requests.attributes.requested_by_id')
                    : </b> {{optional($tr_request->requestedBy)->fullname}}</p>
            <p><b>@lang('DBT/transpose_requests.attributes.created_at'): </b> {{ optional($tr_request->created_at)->format('d/m/Y H:i:s')}}</p>
            <p><b>@lang('DBT/transpose_requests.attributes.updated_at'): </b> {{ optional($tr_request->updated_at)->format('d/m/Y H:i:s')}}</p>
            <p><b>@lang('DBT/transpose_requests.attributes.started_at'): </b> {{ optional($tr_request->started_at)->format('d/m/Y H:i:s')}}</p>
            <p><b>@lang('DBT/transpose_requests.attributes.ended_at'): </b> {{ optional($tr_request->ended_at)->format('d/m/Y H:i:s')}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="submit" class="btn btn-sm btn-danger"><i
                        class="fas fa-trash-alt fa-fw"></i> @lang('common.form.delete')</button>

            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                        class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
