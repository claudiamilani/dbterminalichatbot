@extends('layouts.adminlte.template',['page_title' => trans('DBT/transpose_configs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => '12', 'searchbox' => ['admin::dbt.transpose_configs.index'], 'sortable'=>true, 'hide_required_legend'=>true])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">
                                @can('create', 'App\DBT\Models\TransposeConfig')
                                    <a href="{{ route('admin::dbt.transpose_configs.create') }}"
                                       class="btn btn-sm btn-success" title="@lang('DBT/transpose_configs.create.title')">
                                        <i class="fas fa-fw fa-plus"></i>
                                    </a>
                                @endcan
                                @if(!$configs->count())
                                    @can('create', 'App\DBT\Models\TransposeConfig')
                                        <a href="{{ route('admin::dbt.transpose_configs.import') }}"
                                           class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal" title="@lang('DBT/transpose_configs.import.title')">
                                            <i class="fas fa-fw fa-file-import"></i>
                                        </a>
                                    @endcan
                                @endif
                            </td>
                            <td>{!! sort_link('dbt_attribute_id', trans(('DBT/transpose_configs.attributes.dbt_attribute_id'))) !!}</td>
                            <td>{!! sort_link('label', trans(('DBT/transpose_configs.attributes.label'))) !!}</td>
                            <td>{!! sort_link('type', trans(('DBT/transpose_configs.attributes.type'))) !!}</td>
                            <td>{!! sort_link('display_order', trans(('DBT/transpose_configs.attributes.display_order'))) !!}</td>
                            <td>{!! sort_link('created_at', trans(('DBT/transpose_configs.attributes.created_at'))) !!}</td>
                            <td>{!! sort_link('updated_at', trans(('DBT/transpose_configs.attributes.updated_at'))) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($configs as $config)
                            <tr>
                                <td class="btn-toolbar">
                                    @can('view', $config)
                                        <a href="{{ route('admin::dbt.transpose_configs.show', $config->id) }}"
                                           class="btn btn-sm btn-primary" title="@lang('DBT/transpose_configs.show.title')">
                                            <i class="fas fa-fw fa-search"></i>
                                        </a>
                                    @endcan
                                    @can('update',$config)
                                        <a href="{{ route('admin::dbt.transpose_configs.edit', $config->id) }}"
                                           class="btn btn-sm btn-primary" title="@lang('DBT/transpose_configs.edit.title')">
                                            <i class="fas fa-fw fa-pen"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$config)
                                        <a href="{{ route('admin::dbt.transpose_configs.delete', $config->id) }}"
                                           class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" title="@lang('DBT/transpose_configs.delete.title')">
                                            <i class="fas fa-fw fa-trash-alt"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $config->dbtAttribute->name }}</td>
                                <td>{{ $config->label }}</td>
                                <td>{{ $config->type }}</td>
                                <td>{{ $config->display_order }}</td>
                                <td>{{ $config->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $config->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $configs,'searchFilters' => request(['search'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
