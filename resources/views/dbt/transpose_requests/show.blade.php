@extends('layouts.adminlte.template',['page_title' => trans('DBT/transpose_requests.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 12, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/transpose_requests.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$tr_request->id}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.requested_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $tr_request->requestedBy ? $tr_request->requestedBy->fullname : trans('common.system') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($tr_request->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($tr_request->updated_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.started_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($tr_request->started_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.ended_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($tr_request->ended_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.status')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">@lang('DBT/transpose_requests.status.'.$tr_request->status)</p>
                        </div>
                    </div>
                    @if($tr_request->status == \App\DBT\TransposeRequest::STATUS_ERROR)
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/transpose_requests.attributes.message')</label>
                            <div class="col-md-9">
                                {!! Form::textarea('message', $tr_request->message, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>
                    @endif
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('download',$tr_request)
                        @if($tr_request->status == App\DBT\TransposeRequest::STATUS_PROCESSED)
                            <a
                                    href="{{ route('admin::dbt.transpose_requests.download', $tr_request->id) }}"
                                    title="@lang('Esporta CSV')"
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-file-csv fa-fw"></i>
                                @lang('Esporta CSV')
                            </a>
                        @endif
                    @endcan
                    <a href="{{backToSource('admin::dbt.transpose_requests.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
    </div>
@endsection
@push('scripts')
@endpush