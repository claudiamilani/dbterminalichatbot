@extends('layouts.adminlte.template',['page_title' => trans('external_roles.title')])
@section('content')
    <div class="row">
        @component('components.widget',['sortable' => true,'searchbox' => ['admin::external_roles.index'], 'size'=>12])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>@can('create','App\Auth\ExternalRole')<a
                                        href="{{ route('admin::external_roles.create') }}"
                                        class="btn btn-sm btn-success " title="{{trans('external_roles.create.title')}}"><i class="fas fa-plus fa-fw"></i></a>@endcan
                            </td>
                            <td>{!! sort_link('auth_type_id', trans(('external_roles.attributes.auth_type_id'))) !!}</td>
                            <td>{!! sort_link('external_role_id', trans(('external_roles.attributes.external_role_id'))) !!}</td>
                            <td>{!! sort_link('auto_register_users', trans(('external_roles.attributes.auto_register_users'))) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($external_roles as $external_role)
                            <tr>
                                <td>
                                    @can('update',$external_role)<a
                                            href="{{ route('admin::external_roles.edit', $external_role->id) }}"
                                            class="btn btn-sm btn-primary" title="{{trans('external_roles.edit.title')}}">
                                        <i class="fas fa-pen fa-fw"></i>
                                    </a>
                                    @endcan
                                    @can('delete',$external_role)<a
                                            href="{{ route('admin::external_roles.delete', $external_role->id) }}"
                                            class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" title="{{trans('external_roles.delete.title')}}">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $external_role->authType->name }}</td>
                                <td>{{ $external_role->external_role_id }}</td>
                                <td>
                                    <i class=" {{$external_role->auto_register_users ? 'fas fa-fw fa-check text-success' : 'fas fw-fw fa-xmark text-danger' }}"></i> {{$external_role->auto_register_users ? trans('common.yes') : trans('common.no') }}
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $external_roles,'searchFilters' => request(['search'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
@push('scripts')
    <script>
        $("[data-sort_reset]").contextmenu(function(e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;
        });
    </script>
@endpush