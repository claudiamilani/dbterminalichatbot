@extends('layouts.adminlte.template',['page_title' => trans('auth_types.title')])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::auth_types.index'],'sortable' => true])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td></td>
                            <td>{!! sort_link('name', trans(('auth_types.attributes.name'))) !!}</td>
                            <td>{!! sort_link('enabled',trans('auth_types.attributes.enabled')) !!}</td>
                            <td>{!! sort_link('default',trans('auth_types.attributes.default'))!!}</td>
                            <td>{!! sort_link('auto_register',trans('auth_types.attributes.auto_register'))!!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($auth_types as $auth_type)
                            <tr>
                                <td>
                                    @can('update',$auth_type)
                                        <a
                                                href="{{ route('admin::auth_types.edit', $auth_type->id) }}"
                                                class="btn btn-sm btn-primary" title="{{trans('auth_types.edit.title')}}"><i class="fas fa-pen fa-fw"></i></a>
                                    @endcan
                                </td>
                                <td>{{ $auth_type->name }}</td>
                                <td>
                                    <i class="{{$auth_type->enabled ? 'fas fa-check text-primary' : 'fas fa-times text-warning' }}"></i> {{$auth_type->enabled ? trans('common.yes') : trans('common.no') }}
                                </td>
                                <td>
                                    <i class="{{$auth_type->default ? 'fas fa-check text-primary' : 'fas fa-times text-warning' }}"></i> {{$auth_type->default ? trans('common.yes') : trans('common.no') }}
                                </td>
                                <td>
                                    <i class="{{$auth_type->auto_register ? 'fas fa-check text-primary' : (empty($auth_type->auto_register)?'-':'fas fa-times text-warning') }}"></i> {{$auth_type->auto_register ? trans('common.yes') : (empty($auth_type->auto_register)?'-':trans('common.no')) }}
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $auth_types,'searchFilters' => request(['search'])])@endcomponent
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