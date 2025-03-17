@extends('layouts.adminlte.template',['page_title' => trans('DBT/otas.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => '12', 'searchbox' => ['admin::dbt.otas.index'], 'sortable'=>true, 'hide_required_legend'=>true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('type', $type, request('type') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('sub_type', $sub_type, request('sub_type') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('ext_0', $ext_0, request('ext_0') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('ext_number', $ext_number, request('ext_number') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">
                                @can('create', 'App\DBT\Models\Ota')
                                    <a href="{{ route('admin::dbt.otas.create') }}" class="btn btn-sm btn-success"
                                       title="@lang('DBT/otas.create.title')">
                                        <i class="fas fa-fw fa-plus"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/otas.attributes.id'))) !!}</td>
                            <td>{!! sort_link('name', trans(('DBT/otas.attributes.name'))) !!}</td>
                            <td>{!! sort_link('type', trans(('DBT/otas.attributes.type'))) !!}</td>
                            <td>{!! sort_link('sub_type', trans(('DBT/otas.attributes.sub_type'))) !!}</td>
                            <td>{!! sort_link('ext_0', trans(('DBT/otas.attributes.ext_0'))) !!}</td>
                            <td>{!! sort_link('ext_number', trans(('DBT/otas.attributes.ext_number'))) !!}</td>
                            <td>{!! sort_link('created_at', trans(('DBT/otas.attributes.created_at'))) !!}</td>
                            <td>{!! sort_link('updated_at', trans(('DBT/otas.attributes.updated_at'))) !!}</td>
                        </tr>
                    @endslot

                    @slot('body')
                        @foreach($otas as $ota)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('view',$ota)
                                        <a
                                                href="{{ route('admin::dbt.otas.show', $ota->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="@lang('DBT/otas.show.title')">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update',$ota)
                                        <a
                                                href="{{ route('admin::dbt.otas.edit', $ota->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="@lang('DBT/otas.edit.title')">
                                            <i class="fas fa-pen fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$ota)
                                        <a
                                                href="{{ route('admin::dbt.otas.delete', $ota->id) }}"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal"
                                                title="@lang('DBT/otas.delete.title')">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $ota->id }}</td>
                                <td>{{ $ota->name }}</td>
                                <td>{{ $ota->type }}</td>
                                <td>{{ $ota->sub_type }}</td>
                                <td>{{ $ota->ext_0 }}</td>
                                <td>{{ $ota->ext_number }}</td>
                                <td>{{ $ota->createdAtInfo }}</td>
                                <td>{{ $ota->updatedAtInfo }}</td>
                            </tr>
                        @endforeach
                    @endslot
                @endcomponent
            @endslot

            @slot('footer')
                @component('components.pagination',['contents' => $otas, 'searchFilters' => request(['search', 'type', 'sub_type', 'ext_0', 'ext_number'])])@endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
