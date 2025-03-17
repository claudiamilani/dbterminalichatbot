@extends('layouts.adminlte.template',['page_title' => trans('DBT/dwh_operations.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 6])
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="col-md-6 btn-toolbar"></td>
                            <td>@lang('DBT/dwh_operations.attributes.type')</td>
                            <td>@lang('DBT/dwh_operations.attributes.is_present')</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($dwh_operations as $type =>$details)
                            <tr>
                                <td>
                                    @can('create_views',\App\DBT\Models\TransposeConfig::class)
                                    <a
                                            href="{{ route('admin::dbt.dwh_operations.create', ['type'=>$type]) }}"
                                            title="@lang('DBT/dwh_operations.create.title')"
                                            class="btn btn-sm btn-success"
                                            data-target="#myModal"
                                            data-toggle="modal">
                                        <i class="fas fa-plus fa-fw"></i>{{trans('DBT/dwh_operations.generate_view')}}
                                    </a>
                                    @endcan
                                <td>{{$details['title']}}</td>
                                <td>
                                    @if($details['is_present'])
                                        <i class="fas fa-check fa-fw"></i>{{trans('common.yes')}}
                                    @else
                                        <i class="fas fa-times fa-fw"></i>{{trans('common.no')}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
