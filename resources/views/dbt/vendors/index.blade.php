@extends('layouts.adminlte.template',['page_title' => trans('DBT/vendors.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => '12', 'searchbox' => ['admin::dbt.vendors.index'], 'sortable'=>true, 'hide_required_legend'=>true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('published', $published, request('published') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">
                                @can('create', 'App\DBT\Models\Vendor')
                                    <a href="{{ route('admin::dbt.vendors.create') }}"
                                       class="btn btn-sm btn-success"
                                       title="@lang('DBT/vendors.create.title')">
                                        <i class="fas fa-fw fa-plus"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/vendors.attributes.id'))) !!}</td>
                            <td>{!! sort_link('name', trans(('DBT/vendors.attributes.name'))) !!}</td>
                            <td>{!! sort_link('created_at', trans(('DBT/vendors.attributes.created_at'))) !!}</td>
                            <td>{!! sort_link('updated_at', trans(('DBT/vendors.attributes.updated_at'))) !!}</td>
                            <td>{!! sort_link('published', trans(('DBT/vendors.attributes.published'))) !!}</td>
                        </tr>
                    @endslot

                    @slot('body')
                        @foreach($vendors as $vendor)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('view',$vendor)
                                        <a
                                                href="{{ route('admin::dbt.vendors.show', $vendor->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="@lang('DBT/vendors.show.title')">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update',$vendor)
                                        <a
                                                href="{{ route('admin::dbt.vendors.edit', $vendor->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="@lang('DBT/vendors.edit.title')">
                                            <i class="fas fa-pen fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$vendor)
                                        <a
                                                href="{{ route('admin::dbt.vendors.delete', $vendor->id) }}"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal"
                                                title="@lang('DBT/vendors.delete.title')">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $vendor->id }}</td>
                                <td>{{ $vendor->name }}</td>
                                <td>{{ $vendor->createdAtInfo }}</td>
                                <td>{{ $vendor->updatedAtInfo }}</td>
                                <td>
                                    <i class="fas fa-fw {{ $vendor->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                    {{ $vendor->published ? 'Sì' : 'No' }}
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                @endcomponent
            @endslot

            @slot('footer')
                @component('components.pagination',['contents' => $vendors, 'searchFilters' => request(['search', 'published'])])@endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
