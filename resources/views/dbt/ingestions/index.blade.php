@extends('layouts.adminlte.template',['page_title' =>trans('DBT/ingestions.title')])
@section('content')
    <div class="row">
        @component('components.widget',['sortable' => true,'searchbox' => ['admin::dbt.ingestions.index'], 'size'=>12])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('source', $source, request('source') ?? '-', ['class' => 'form-control pull-right']) !!}
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
                                @can('create','App\DBT\Models\Ingestion')
                                    <a
                                            title="@lang('DBT/ingestions.create.title')"
                                            href="{{ route('admin::dbt.ingestions.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans('DBT/ingestions.attributes.id')) !!}</td>
                            <td>{!! sort_link('ingestion_source_id', trans('DBT/ingestions.attributes.ingestion_source_id')) !!}</td>
                            <td>{!! sort_link('status', trans('DBT/ingestions.attributes.status')) !!}</td>
                            <td>{!! sort_link('started_at', trans('DBT/ingestions.attributes.started_at')) !!}</td>
                            <td>{!! sort_link('ended_at', trans('DBT/ingestions.attributes.ended_at')) !!}</td>
                            <td>{!! sort_link('created_at', trans('DBT/ingestions.attributes.created_at')) !!}</td>
                            <td>{!! sort_link('updated_at', trans('DBT/ingestions.attributes.updated_at')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($ingestions as $ingestion)
                            <tr>
                                <td>
                                    @can('view',$ingestion)
                                        <a
                                                href="{{ route('admin::dbt.ingestions.show', $ingestion->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="{{trans('DBT/ingestions.show.title')}}">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                        @can('update',$ingestion)
                                            <a
                                                    href="{{ route('admin::dbt.ingestions.edit', $ingestion->id) }}"
                                                    class="btn btn-sm btn-primary"
                                                    title="{{trans('DBT/ingestions.edit.title')}}">
                                                <i class="fas fa-pen fa-fw"></i>
                                            </a>
                                        @endcan
                                        @can('delete',$ingestion)
                                            <a
                                                    href="{{ route('admin::dbt.ingestions.delete', $ingestion->id) }}"
                                                    title="@lang('DBT/ingestions.delete.title')"
                                                    class="btn btn-sm btn-danger"
                                                    data-toggle="modal" data-target="#myModal">
                                                <i class="fas fa-trash-alt fa-fw"></i>
                                            </a>
                                        @endcan
                                </td>
                                <td>{{ $ingestion->id }}</td>
                                <td>{{ $ingestion->source->name }}</td>
                                <td>{{ $ingestion->statusLabel }}</td>
                                <td>{{ optional($ingestion->started_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ optional($ingestion->ended_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $ingestion->createdAtInfo }}</td>
                                <td>{{ $ingestion->updatedAtInfo }}</td>

                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $ingestions,'searchFilters' => request(['search', 'source', 'status'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
@push('scripts')
    <script>
        $("[data-sort_reset]").contextmenu(function (e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;
        });
    </script>
@endpush