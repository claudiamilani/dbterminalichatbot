@php use App\DBT\Models\TerminalPicture; @endphp
@component('components.widget', ['title' => trans('DBT/terminal_pictures.title'),
    'collapsible' => true,
    'size' => 12,
    'sortable' => true,
    'withAnchor' => str_slug(trans('DBT/terminal_pictures.title')),
])
    @slot('body')
        @component('components.table-list')
            @slot('head')
                <tr>
                    <td>
                        @can('create', 'App\DBT\Models\TerminalPicture')
                            <a
                                    title="@lang('DBT/terminal_pictures.create.title')"
                                    href="{{ route('admin::dbt.terminals.pictures.create', paramsWithBackTo(['terminal_id' => $terminal->id],'admin::dbt.terminals.show',['id' => $terminal->id, '#'.nav_fragment('DBT/terminal_pictures.title')])) }}"
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-plus fa-fw"></i>
                            </a>
                        @endcan
                    </td>
                    <td>{!! sort_link('id', trans('DBT/terminal_pictures.attributes.id'), 'sort_pics' ,str_slug(trans('DBT/terminal_pictures.title'))) !!}</td>
                    <td>{!! sort_link('title',trans('DBT/terminal_pictures.attributes.title'), 'sort_pics' ,str_slug(trans('DBT/terminal_pictures.title'))) !!}</td>
                    <td>{!! sort_link('display_order',trans('DBT/terminal_pictures.attributes.display_order'), 'sort_pics' ,str_slug(trans('DBT/terminal_pictures.title'))) !!}</td>
                    <td>{!! sort_link('created_at',trans('DBT/terminal_pictures.attributes.created_by_id'), 'sort_pics' ,str_slug(trans('DBT/terminal_pictures.title'))) !!}</td>
                    <td>{!! sort_link('updated_at',trans('DBT/terminal_pictures.attributes.updated_by_id'), 'sort_pics' ,str_slug(trans('DBT/terminal_pictures.title')))!!}</td>
                </tr>
            @endslot

            @slot('body')
                @foreach($pictures as $picture_info)
                    <tr>
                        <td>
                            @can('update', $picture_info)
                                <a
                                        href="{{ route('admin::dbt.terminals.pictures.edit', paramsWithBackTo(['terminal_id' => $terminal->id, 'picture_id' => $picture_info->id],'admin::dbt.terminals.show',['id'=>$terminal->id, '#'.nav_fragment('DBT/terminal_pictures.title')])) }}"
                                        title="@lang('DBT/terminal_pictures.edit.title')"
                                        class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen fa-fw"></i>
                                </a>
                            @endcan

                            @can('delete', $picture_info)
                                <a
                                        href="{{ route('admin::dbt.terminals.pictures.delete', paramsWithBackTo(['terminal_id' => $terminal->id, 'picture_id' => $picture_info->id],'admin::dbt.terminals.show',['id'=>$terminal->id, '#'.nav_fragment('DBT/terminal_pictures.title')])) }}"
                                        class="btn btn-sm btn-danger"
                                        data-toggle="modal" data-target="#myModal"
                                        title="@lang('DBT/terminal_pictures.delete.title')">
                                    <i class="fas fa-trash-alt fa-fw"></i>
                                </a>
                            @endcan
                        </td>

                        <td>{{ $picture_info->id }}</td>

                        <td>
                            <a href="{{(Storage::disk('terminal-pictures')->url($picture_info->file_path))}}"
                               target="_blank">
                                {{ $picture_info->title }} <sup><i class="fas fa-fw fa-arrow-up-right-from-square"></i></sup>
                            </a>
                        </td>

                        <td>{{ $picture_info->display_order }}</td>
                        <td>{{ $picture_info->createdAtInfo }}</td>
                        <td>{{ $picture_info->updatedAtInfo }}</td>
                    </tr>
                @endforeach
            @endslot

            @slot('paginator')
                @component('components.pagination',['contents' => $pictures,'searchFilters' => request(['search'])])@endcomponent
            @endslot
        @endcomponent
    @endslot
@endcomponent
