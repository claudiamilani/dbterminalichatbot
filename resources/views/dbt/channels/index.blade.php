@extends('layouts.adminlte.template',['page_title' => trans('DBT/channels.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.channels.index'],'sortable' => true])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\DBT\Models\Channel')
                                    <a
                                            title="@lang('DBT/channels.create.title')"
                                            href="{{ route('admin::dbt.channels.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/channels.attributes.id'))) !!}</td>
                            <td>{!! sort_link('name',trans('DBT/channels.attributes.name')) !!}</td>
                            <td>{!! sort_link('created_at',trans('DBT/channels.attributes.created_by_id')) !!}</td>
                            <td>{!! sort_link('updated_at',trans('DBT/channels.attributes.updated_by_id')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($channels as $channel)
                            <tr>
                                <td>
                                    @can('view', $channel)
                                        <a
                                                href="{{ route('admin::dbt.channels.show', $channel->id) }}"
                                                title="@lang('DBT/channels.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update', $channel)
                                        <a
                                                href="{{ route('admin::dbt.channels.edit', $channel->id) }}"
                                                title="@lang('DBT/channels.edit.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen fa-fw"></i>

                                        </a>
                                    @endcan
                                    @can('delete', $channel)
                                        <a
                                                href="{{ route('admin::dbt.channels.delete', $channel->id) }}"
                                                title="@lang('DBT/channels.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $channel->id }}</td>
                                <td>{{ $channel->name }}</td>
                                <td>{{ $channel->created_at_info }}</td>
                                <td>{{ $channel->updated_at_info }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $channels,'searchFilters' => request(['search'])])@endcomponent
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