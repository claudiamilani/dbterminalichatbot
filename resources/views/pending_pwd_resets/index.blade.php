@extends('layouts.adminlte.template',['page_title' => trans('pending_pwd_resets.title'),'fa_icon_class' => ''])


@section('content')
    <div class="row">
        @component('components.widget',['searchbox' => ['admin::pending_pwd_resets.index'],'size' => 12])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>@can('create','App\Auth\PasswordRecovery')<a
                                        href="{{ route('admin::pending_pwd_resets.create') }}"
                                        class="btn btn-sm btn-success"><i class="fas fa-plus fa-fw" title="{{trans('pending_pwd_resets.create.title')}}"></i> </a>@endcan
                            </td>
                            <td>@lang('pending_pwd_resets.attributes.fullname_withmail')</td>
                            <td>@lang('pending_pwd_resets.attributes.account')</td>
                            <td>@lang('pending_pwd_resets.attributes.created_at')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($pending_pwd_resets as $reset)
                            <tr>
                                <td>
                                    @can('view',$reset)<a
                                            href="{{ route('admin::pending_pwd_resets.show', $reset->id) }}"
                                            class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"><i class="fas fa-search fa-fw" title="{{trans('pending_pwd_resets.title')}}"></i> </a>
                                    @endcan
                                    @can('delete',$reset)<a
                                            href="{{ route('admin::pending_pwd_resets.delete', $reset->id) }}"
                                            class="btn btn-sm btn-danger"
                                            data-toggle="modal" data-target="#myModal"><i class="fas fa-trash-alt fa-fw" title="{{trans('pending_pwd_resets.delete.title')}}"></i> </a>
                                    @endcan
                                </td>
                                <td>{{ $reset->account->NameWithMail }}</td>
                                <td>{{ $reset->account->user }}</td>
                                <td>{{ optional($reset->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $pending_pwd_resets,'searchFilters' => request(['search'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection
