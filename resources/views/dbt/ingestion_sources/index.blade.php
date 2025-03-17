@extends('layouts.adminlte.template',['page_title' =>trans('DBT/ingestion_sources.title')])
@section('content')
    <div class="row">
        @component('components.widget',['sortable' => true,'searchbox' => ['admin::dbt.ingestion_sources.index'], 'size'=>12])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('enabled', $enabled, request('enabled') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td></td>
                            <td>{!! sort_link('name', trans('DBT/ingestion_sources.attributes.name')) !!}</td>
                            <td>{!! sort_link('priority', trans('DBT/ingestion_sources.attributes.priority')) !!}</td>
                            <td>{!! sort_link('enabled', trans('DBT/ingestion_sources.attributes.enabled')) !!}</td>
                            <td>{!! sort_link('updated_at', trans('DBT/ingestion_sources.attributes.updated_at')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($ingestion_sources as $ingestion_source)
                            <tr>
                                <td>
                                    @can('view',$ingestion_source)
                                        <a
                                                href="{{ route('admin::dbt.ingestion_sources.show', $ingestion_source->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="{{trans('DBT/ingestion_sources.show.title')}}">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update',$ingestion_source)
                                        <a
                                                href="{{ route('admin::dbt.ingestion_sources.edit', $ingestion_source->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="{{trans('DBT/ingestion_sources.edit.title')}}">
                                            <i class="fas fa-pen fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $ingestion_source->name }}</td>
                                <td>{{ $ingestion_source->priority }}</td>
                                <td>
                                    <i class=" {{$ingestion_source->enabled ? 'fas fa-fw fa-check text-success' : 'fas fw-fw fa-xmark text-danger' }}"></i> {{$ingestion_source->enabled ? trans('common.active') : trans('common.disabled') }}
                                </td>
                                <td>{{ $ingestion_source->updatedAtInfo }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $ingestion_sources,'searchFilters' => request(['search', 'enabled'])])@endcomponent
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