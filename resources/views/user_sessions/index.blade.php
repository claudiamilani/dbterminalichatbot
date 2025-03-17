@extends('layouts.adminlte.template',['page_title' => trans('user_sessions.title'), 'fa_icon_class' => 'fa-users'])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::user_sessions.index']])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm">
                    {!! Form::select('user_agent', $user_agents,request('user_agent'), [ 'class' => 'form-control pull-right','placeholder' => trans('user_sessions.attributes.user_agent')]) !!}
                </div>
                <div class="input-group input-group-sm">
                    {!! Form::select('robot', [trans('common.yes') => trans('common.yes'),trans('common.no') => trans('common.no')],request('robot'), [ 'class' => 'form-control pull-right','placeholder' => trans('user_sessions.attributes.robot')]) !!}

                </div>
            @endslot
            @slot('title')@lang('user_sessions.sections.a_sessions') ({{ count($sessions) }})@endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">@can('purge','App\Auth\UserSession')<a
                                        href="{{ route('admin::user_sessions.purgeAuthenticated','authenticated') }}"
                                        class="btn btn-sm btn-warning" data-toggle="modal" data-target="#myModal"><i class="fas fa-trash-alt fa-fw" title="{{trans('user_sessions.purge.title_auth')}}"></i> </a>@endcan
                            </td>
                            <td>@lang('user_sessions.attributes.id')</td>
                            <td>@lang('user_sessions.attributes.user')</td>
                            <td>@lang('user_sessions.attributes.nameWithMail')</td>
                            <td>@lang('user_sessions.attributes.ip_address')</td>
                            <td>@lang('user_sessions.attributes.user_agent')</td>
                            <td>@lang('user_sessions.attributes.robot')</td>
                            <td>@lang('user_sessions.attributes.last_activity')</td>
                            <td>@lang('user_sessions.attributes.last_page')</td>
                            <td>@lang('user_sessions.attributes.expiring_in')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($sessions as $session)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('delete',$session)<a
                                            href="{{ route('admin::user_sessions.delete', $session->id) }}"
                                            class="btn btn-sm btn-danger"
                                            data-toggle="modal" data-target="#myModal"><i class="fas fa-trash-alt fa-fw" title="{{trans('user_sessions.delete.title')}}"></i> </a>
                                    @endcan
                                </td>
                                <td title="{{$session->id}}">{{ str_limit($session->id, 5) }}</td>
                                <td>{{ optional($session->account)->user ?? 'N/A' }}</td>
                                <td>{{ optional($session->account)->nameWithMail ?? 'N/A' }}</td>
                                <td>{{ $session->ip_address }}</td>
                                <td>{{ $session->browser }}@if($session->OS)<br>{{ $session->OS }}@endif</td>
                                <td>{{ $session->isRobot }}</td>
                                <td>{{ $session->last_activity->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $session->lastPage }}</td>
                                <td>{{ $session->expiringIn }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $sessions,'searchFilters' => request(['search','user_agent', 'robot'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::user_sessions.index'],'searchbox_name' => 'search_guest'])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm">
                    {!! Form::select('u_user_agent', $u_user_agents,request('u_user_agent'), [ 'class' => 'form-control pull-right','placeholder' => trans('user_sessions.attributes.user_agent')]) !!}
                </div>
                <div class="input-group input-group-sm">
                    {!! Form::select('u_robot', [trans('common.yes') => trans('common.yes'),trans('common.no') => trans('common.no')],request('u_robot'), [ 'class' => 'form-control pull-right','placeholder' => trans('user_sessions.attributes.robot')]) !!}

                </div>
            @endslot
            @slot('title')@lang('user_sessions.sections.u_sessions') ({{ count($u_sessions) }})@endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">@can('purge','App\Auth\UserSession')<a
                                        href="{{ route('admin::user_sessions.purgeUnauthenticated','unauthenticated') }}"
                                        class="btn btn-sm btn-warning" data-toggle="modal" data-target="#myModal"><i class="fas fa-trash-alt fa-fw" title="{{trans('user_sessions.purge.title')}}"></i> </a>@endcan
                            </td>
                            <td>@lang('user_sessions.attributes.id')</td>
                            <td>@lang('user_sessions.attributes.ip_address')</td>
                            <td>@lang('user_sessions.attributes.user_agent')</td>
                            <td>@lang('user_sessions.attributes.robot')</td>
                            <td>@lang('user_sessions.attributes.last_activity')</td>
                            <td>@lang('user_sessions.attributes.expiring_in')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($u_sessions as $session)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('delete',$session)<a
                                            href="{{ route('admin::user_sessions.delete', $session->id) }}"
                                            class="btn btn-sm btn-danger"
                                            data-toggle="modal" data-target="#myModal"><i class="fas fa-trash-alt fa-fw" title="{{trans('user_sessions.delete.title')}}"></i> </a>
                                    @endcan
                                </td>
                                <td title="{{$session->id}}">{{ str_limit($session->id, 5) }}</td>
                                <td>{{ $session->ip_address }}</td>
                                <td>{{ $session->browser }}@if($session->OS)<br>{{ $session->OS }}@endif</td>
                                <td>{{ $session->isRobot }}</td>
                                <td>{{ $session->last_activity->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $session->expiringIn }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $u_sessions,'searchFilters' => request(['search_guest', 'u_user_agent', 'u_robot'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection