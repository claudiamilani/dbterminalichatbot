@component('components.widget',[
    'size' => 12,
    'searchbox' => ['admin::dbt.ingestion_sources.show',$ingestion_source->id],
    'sortable' => true, 'title' => trans('DBT/attribute_header_mappings.title'),
    'sort_key'=>'sort_attribute_header_mappings',
    'withAnchor' => str_slug(trans('DBT/attribute_header_mappings.title')),
    ])
    @slot('body')
        @component('components.table-list')
            @slot('head')
                <tr>
                    <td>
                        @can('create', 'App\DBT\Models\AttributeHeaderMapping')
                            <a
                                    title="@lang('DBT/attribute_header_mappings.create.title')"
                                    href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.create', paramsWithBackTo(['id' => $ingestion_source->id], 'admin::dbt.ingestion_sources.show', [$ingestion_source->id, '#'.nav_fragment('DBT/attribute_header_mappings.title')])) }}"
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-plus fa-fw"></i>
                            </a>
                        @endcan
                        @if(!$mappings->count())
                            @can('create', 'App\DBT\Models\AttributeHeaderMapping')
                                <a href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.import_request', $ingestion_source->id) }}"
                                   class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal">
                                    <i class="fas fa-fw fa-file-import"></i>
                                </a>
                            @endcan
                        @endif
                    </td>
                    <td>{!! sort_link('id', trans('DBT/attribute_header_mappings.attributes.id'), 'sort_attribute_header_mappings', str_slug(trans('DBT/attribute_header_mappings.title'))) !!}</td>
                    <td>{!! sort_link('header_name', trans('DBT/attribute_header_mappings.attributes.header_name'), 'sort_attribute_header_mappings', str_slug(trans('DBT/attribute_header_mappings.title'))) !!}</td>
                    <td>{!! sort_link('dbtAttribute-name', trans('DBT/attribute_header_mappings.attributes.dbt_attribute_id'), 'sort_attribute_header_mappings', str_slug(trans('DBT/attribute_header_mappings.title'))) !!}</td>
                    <td>{!! sort_link('created_at',trans('DBT/attribute_header_mappings.attributes.created_by_id'), 'sort_attribute_header_mappings', str_slug(trans('DBT/attribute_header_mappings.title'))) !!}</td>
                    <td>{!! sort_link('updated_at',trans('DBT/attribute_header_mappings.attributes.updated_by_id'), 'sort_attribute_header_mappings', str_slug(trans('DBT/attribute_header_mappings.title'))) !!}</td>
                </tr>
            @endslot
            @slot('body')
                @foreach($mappings as $mapping)
                    <tr>
                        <td>
                            @can('view', $mapping)
                                <a
                                        href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.show', paramsWithBackTo($mapping->id, 'admin::dbt.ingestion_sources.show', [$ingestion_source->id, '#'.nav_fragment('DBT/attribute_header_mappings.title')])) }}"
                                        title="@lang('DBT/attribute_header_mappings.show.title')"
                                        class="btn btn-sm btn-primary">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                            @endcan
                            @can('update', $mapping)
                                <a href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.edit', paramsWithBackTo($mapping->id, 'admin::dbt.ingestion_sources.show', [$ingestion_source->id, '#'.nav_fragment('DBT/attribute_header_mappings.title')])) }}"
                                        title="@lang('DBT/attribute_header_mappings.edit.title')"
                                        class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen fa-fw"></i>
                                </a>
                            @endcan
                            @can('delete', $mapping)
                                <a href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.delete', paramsWithBackTo($mapping->id, 'admin::dbt.ingestion_sources.show',[$ingestion_source->id, '#'.nav_fragment('DBT/attribute_header_mappings.title')])) }}"
                                        title="@lang('DBT/attribute_header_mappings.delete.title')"
                                        class="btn btn-sm btn-danger"
                                        data-toggle="modal" data-target="#myModal">
                                    <i class="fas fa-trash-alt fa-fw"></i>
                                </a>
                            @endcan
                        </td>
                        <td>{{ $mapping->id }}</td>
                        <td>{{ $mapping->header_name }}</td>
                        <td>{{ $mapping->dbtAttribute->name }}</td>
                        <td>{{ $mapping->createdAtInfo }}</td>
                        <td>{{ $mapping->updatedAtInfo }}</td>
                    </tr>
                @endforeach
            @endslot
            @slot('paginator')
                @component('components.pagination',['contents' => $mappings,'searchFilters' => request(['search'])])@endcomponent
            @endslot
        @endcomponent
    @endslot
@endcomponent