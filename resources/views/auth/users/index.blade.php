@extends('layouts.adminlte.template',['page_title' => trans('users.title'), 'fa_icon_class' => 'fa-users'])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::users.index'],'sortable' => true])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\Auth\User')
                                    <a
                                            title="@lang('users.create.title')"
                                            href="{{ route('admin::users.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('surname', trans(('users.attributes.fullname'))) !!}</td>
                            <td>{!! sort_link('email',trans('users.attributes.email')) !!}</td>
                            <td>{!! sort_link('user',trans('users.attributes.user'))!!}</td>
                            <td>{!! sort_link('authType-id',trans('users.attributes.auth_type_id'))!!}</td>
                            @if(Auth::user()->isAdmin())
                                <td>{!! sort_link('login_success_on',trans('users.attributes.login_success_on')) !!}</td>
                                <td>@lang('users.attributes.isAdmin')</td>
                            @endif
                            <td>{!! sort_link('enabled',trans('users.attributes.enabled')) !!}</td>
                            <td>{!! sort_link('locked',trans('users.attributes.locked')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($accounts as $account)
                            <tr>
                                <td>
                                    @can('update',$account)
                                        <a
                                                href="{{ route('admin::users.edit', $account->id) }}"
                                                title="@lang('users.edit.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen fa-fw"></i>

                                        </a>
                                    @endcan
                                    @can('delete',$account)
                                        <a
                                                href="{{ route('admin::users.delete', $account->id) }}"
                                                title="@lang('users.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $account->fullName }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $account->user }}</td>
                                <td>{{ $account->authType->name }}</td>
                                @if(Auth::user()->isAdmin())
                                    <td>
                                        {{ optional($account->login_success_on)->format('d/m/Y H:i') ?? trans('common.never')}}
                                    </td>
                                    <td>
                                        <i class="{{$account->isAdmin() ? 'fas fa-fw fa-star text-yellow' : '' }}"></i> {{$account->isAdmin() ? trans('common.yes') : trans('common.no') }}
                                    </td>
                                @endif
                                <td>
                                    <i class="fas fa-fw {{$account->available ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$account->availableLabel }}
                                </td>
                                <td>
                                    <i class="fas fa-fw {{$account->locked ? 'fa-lock text-danger' : '' }}"></i> {{$account->locked ? trans('common.yes') : trans('common.no') }}
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $accounts,'searchFilters' => request(['search'])])@endcomponent
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