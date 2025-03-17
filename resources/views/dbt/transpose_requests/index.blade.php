@extends('layouts.adminlte.template',['page_title' => trans('DBT/transpose_requests.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.transpose_requests.index'],'sortable' => true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('requested_by', $requested_by, request('requested_by') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('status', $status, request('status') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\DBT\TransposeRequest')
                                    <a
                                            title="@lang('DBT/transpose_requests.create.title')"
                                            href="{{ route('admin::dbt.transpose_requests.create') }}"
                                            class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/transpose_requests.attributes.id'))) !!}</td>
                            <td>{!! sort_link('requestedBy-surname',trans('DBT/transpose_requests.attributes.requested_by_id'))!!}</td>
                            <td>{!! sort_link('created_at',trans('DBT/transpose_requests.attributes.created_at'))!!}</td>
                            <td>{!! sort_link('started_at',trans('DBT/transpose_requests.attributes.started_at'))!!}</td>
                            <td>{!! sort_link('ended_at',trans('DBT/transpose_requests.attributes.ended_at'))!!}</td>
                            <td>{!! sort_link('status',trans('DBT/transpose_requests.attributes.status'))!!}</td>
                            <td>@lang('DBT/transpose_requests.attributes.elapsed_time')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($tr_requests as $tr_request)
                            <tr>
                                <td>
                                    @can('download',$tr_request)
                                        @if($tr_request->status == App\DBT\TransposeRequest::STATUS_PROCESSED)
                                            <a
                                                    href="{{ route('admin::dbt.transpose_requests.download', $tr_request->id) }}"
                                                    title="@lang('Esporta')"
                                                    class="btn btn-sm btn-success">
                                                <i class="fas fa-file-csv fa-fw"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('view',$tr_request)
                                        <a
                                                href="{{ route('admin::dbt.transpose_requests.show', $tr_request->id) }}"
                                                title="@lang('DBT/transpose_requests.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$tr_request)
                                        <a
                                                href="{{ route('admin::dbt.transpose_requests.delete', $tr_request->id) }}"
                                                title="@lang('DBT/transpose_requests.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $tr_request->id }}</td>
                                <td>{{ $tr_request->requestedBy ? $tr_request->requestedBy->fullname : trans('common.system')  }}</td>
                                <td>{{ optional($tr_request->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>{{ optional($tr_request->started_at)->format('d/m/Y H:i:s') }}</td>
                                <td>{{ optional($tr_request->ended_at)->format('d/m/Y H:i:s') }}</td>
                                <td>@lang('DBT/legacy_imports.status.'.$tr_request->status)</td>
                                <td>{{ $tr_request->started_at ? $tr_request->started_at->diffForHumans($tr_request->ended_at,Carbon\CarbonInterface::DIFF_ABSOLUTE) : ''}}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $tr_requests,'searchFilters' => request(['search', 'requested_by', 'status'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'requested_by', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::users.list')])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/transpose_requests.attributes.requested_by_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
    <script>
        $("[data-sort_reset]").contextmenu(function (e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;
        });
    </script>
@endpush