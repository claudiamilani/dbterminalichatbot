@component('components.widget', ['title' => trans('DBT/terminal_configs.title'),
    'collapsible' => true,
    'size' => 12,
    'sortable' => true,
    'withAnchor' => str_slug(trans('DBT/terminal_configs.title')),
    'sort_key' => 'sort_configs'
])

    @slot('body')
        @component('components.table-list')
            @slot('head')
                <tr>
                    <td>
                        @can('create', 'App\DBT\Models\TerminalConfig')
                            <a
                                    href="{{ route('admin::dbt.terminals.configs.create', paramsWithBackTo([$terminal->id],'admin::dbt.terminals.show', [$terminal->id,'#'.nav_fragment('DBT/terminal_configs.title')])) }}"
                                    class="btn btn-sm btn-success"
                                    title="@lang('DBT/terminal_configs.create.title')">
                                <i class="fas fa-plus fa-fw"></i>
                            </a>
                        @endcan
                    </td>
                    <td>{!! sort_link('id', trans('DBT/terminal_configs.attributes.id'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) )!!}</td>
                    <td>{!! sort_link('ota-id', trans('DBT/terminal_configs.attributes.ota'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) ) !!}</td>
                    <td>{!! sort_link('document-id', trans('DBT/terminal_configs.attributes.document'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) ) !!}</td>
                    <td>{!! sort_link('created_at',trans('DBT/terminal_configs.attributes.created_at'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) ) !!}</td>
                    <td>{!! sort_link('updated_at',trans('DBT/terminal_configs.attributes.updated_at'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) )!!}</td>
                    <td>{!! sort_link('published', trans('DBT/terminal_configs.attributes.published'), 'sort_configs' ,str_slug(trans('DBT/terminal_configs.title')) ) !!}</td>
                </tr>
            @endslot

            @slot('body')
                @foreach($configs as $config_info)
                    <tr>
                        <td>
                            @can('view', $config_info)
                                <a
                                        href="{{ route('admin::dbt.terminals.configs.show', paramsWithBackTo(['terminal_id'=>$terminal->id, 'config_id'=>$config_info->id],'admin::dbt.terminals.show', ['id'=>$terminal->id,'#'.nav_fragment('DBT/terminal_configs.title')])) }}"
                                        class="btn btn-sm btn-primary"
                                        title="@lang('DBT/terminal_configs.show.title')">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                            @endcan

                            @can('update', $config_info)
                                <a
                                        href="{{ route('admin::dbt.terminals.configs.edit', paramsWithBackTo(['terminal_id'=>$terminal->id, 'config_id'=>$config_info->id],'admin::dbt.terminals.show', ['id'=>$terminal->id,'#'.nav_fragment('DBT/terminal_configs.title')])) }}"
                                        class="btn btn-sm btn-primary"
                                        title="@lang('DBT/terminal_configs.edit.title')">
                                    <i class="fas fa-pen fa-fw"></i>
                                </a>
                            @endcan

                            @can('delete', $config_info)
                                <a
                                        class="btn btn-sm btn-danger"
                                        href="{{ route('admin::dbt.terminals.configs.delete', paramsWithBackTo(['terminal_id'=>$terminal->id, 'config_id'=>$config_info->id],'admin::dbt.terminals.show', ['id'=>$terminal->id,'#'.nav_fragment('DBT/terminal_configs.title')])) }}"
                                        data-toggle="modal" data-target="#myModal"
                                        title="@lang('DBT/terminal_configs.delete.title')">
                                    <i class="fas fa-trash-alt fa-fw"></i>
                                </a>
                            @endcan
                        </td>

                        <td>{{ $config_info->id }}</td>
                        <td>{{ $config_info->ota->name }}</td>

                        <td>
                            {!! optional($config_info->document)->file_path
                                ? '<a href="' . Storage::disk('documents')->url(optional($config_info->document)->file_path) . '" target="_blank">'
                                    . optional($config_info->document)->title . ' <sup><i class="fas fa-fw fa-arrow-up-right-from-square"></i></sup>'
                                . '</a>'
                                : '' !!}
                        </td>

                        <td>{{ $config_info->createdAtInfo }}</td>
                        <td>{{ $config_info->updatedAtInfo }}</td>
                        <td>
                            <i class="fas fa-fw {{ $config_info->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                            {{ $config_info->published ? 'Sì' : 'No' }}
                        </td>
                    </tr>
                @endforeach
            @endslot

            @slot('paginator')
                @component('components.pagination',['contents' => $configs,'searchFilters' => request(['search','search_tacs'])])@endcomponent
            @endslot
        @endcomponent
    @endslot
@endcomponent
