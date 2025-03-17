@extends('layouts.adminlte.template',['page_title' => trans('DBT/legacy_imports.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.legacy_imports.index'],'sortable' => true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('update_existing', $update_existing, request('update_existing') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('type', $types, request('type') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
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
                                @can('create','App\DBT\Models\LegacyImport')
                                    <a
                                            title="@lang('DBT/legacy_imports.create.title')"
                                            href="{{ route('admin::dbt.legacy_imports.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/legacy_imports.attributes.id'))) !!}</td>
                            <td>{!! sort_link('type',trans('DBT/legacy_imports.attributes.type')) !!}</td>
                            <td>{!! sort_link('update_existing',trans('DBT/legacy_imports.attributes.update_existing')) !!}</td>
                            <td>{!! sort_link('requestedBy-surname',trans('DBT/legacy_imports.attributes.requested_by_id'))!!}</td>
                            <td>{!! sort_link('created_at',trans('DBT/legacy_imports.attributes.created_at'))!!}</td>
                            <td>{!! sort_link('status',trans('DBT/legacy_imports.attributes.status'))!!}</td>
                            <td>@lang('DBT/legacy_imports.attributes.elapsed_time')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($imports as $import)
                            <tr>
                                <td>
                                    @can('view',$import)
                                        <a
                                                href="{{ route('admin::dbt.legacy_imports.show', $import->id) }}"
                                                title="@lang('DBT/legacy_imports.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$import)
                                        <a
                                                href="{{ route('admin::dbt.legacy_imports.delete', $import->id) }}"
                                                title="@lang('DBT/legacy_imports.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $import->id }}</td>
                                <td>{{ trans('DBT/legacy_imports.types.'.$import->type) }}</td>
                                <td>{{ $import->update_existing ? trans('common.yes') : trans('common.no') }}</td>
                                <td>{{ optional($import->requestedBy)->fullname }}</td>
                                <td>{{ optional($import->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>@lang('DBT/legacy_imports.status.'.$import->status)</td>
                                <td>{{ $import->started_at ? $import->started_at->diffForHumans($import->ended_at,Carbon\CarbonInterface::DIFF_ABSOLUTE) : ''}}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $imports,'searchFilters' => request(['search', 'update_existing', 'requested_by', 'status'])])@endcomponent
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
            output ='<b> @lang('DBT/legacy_imports.attributes.requested_by_id'):</b> ' + output.text;
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