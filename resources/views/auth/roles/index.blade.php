@extends('layouts.adminlte.template',['page_title' => trans('roles.title')])
@section('content')
    <div class="row">
        @component('components.widget',['searchbox' => ['admin::roles.index']])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="">
                                @can('create','App\Auth\Role')
                                    <a
                                            title="@lang('roles.create.title')"
                                            href="{{ route('admin::roles.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                                @can('create','App\Auth\Role')
                                    <a
                                            href="{{ route('admin::roles.defaultPermissionsModal') }}"
                                            class="btn btn-sm btn-warning" data-toggle="modal" data-target="#myModal"
                                            title="@lang('roles.default_permissions.title')"><i
                                                class=" fas fa-fw fa-repeat"></i></a>

                                @endcan
                            </td>
                            <td>{!! sort_link('name', trans('roles.attributes.name')) !!}</td>
                            <td>{!! sort_link('description', trans('roles.attributes.description')) !!}</td>
                            <td>@lang('roles.attributes.users_count')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    @can('update',$role)
                                        <a
                                                class="btn btn-sm btn-primary"
                                                title="@lang('roles.edit.title')"
                                                href="{{ route('admin::roles.edit', $role->id) }}">
                                            <i class="fas fa-pen fa-fw"></i></a>
                                    @endcan
                                    @can('delete',$role)
                                        <a
                                                href="{{ route('admin::roles.delete', $role->id) }}"
                                                title="@lang('roles.delete.title')"
                                                class="btn btn-danger btn-sm"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>

                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <i class="{{$role->users_count ? ($role->users_count > 1 ? 'fas fa-fw fa-user-group': 'fas fa-fw fa-user') : '' }}"></i> {{$role->users_count}}
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $roles,'searchFilters' => request(['search'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection