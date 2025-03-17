@extends('layouts.adminlte.template',['page_title' => trans('DBT/attr_categories.title')])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.attr_categories.index'],'sortable' => true])
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
                                @can('create','App\DBT\Models\AttrCategory')
                                    <a href="{{ route('admin::dbt.attr_categories.create') }}"
                                       class="btn btn-sm btn-success" title="@lang('DBT/attr_categories.create.title')"> <i class="fas fa-plus fa-fw"></i></a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id',trans('DBT/attr_categories.attributes.id')) !!}</td>
                            <td>{!! sort_link('name',trans('DBT/attr_categories.attributes.name')) !!}</td>
                            <td>{!! sort_link('published',trans('DBT/attr_categories.attributes.published')) !!}</td>
                            <td>{!! sort_link('created_at', trans('DBT/attr_categories.attributes.created_at')) !!}</td>
                            <td>{!! sort_link('updated_at', trans('DBT/attr_categories.attributes.updated_at')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($attr_categories as $attr_category)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('view',$attr_category)
                                        <a
                                                href="{{ route('admin::dbt.attr_categories.show', $attr_category->id) }}"
                                                class="btn btn-sm btn-primary" title="@lang('DBT/attributes.show.title')">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                        @can('update',$attr_category)
                                            <a
                                                    href="{{ route('admin::dbt.attr_categories.edit', $attr_category->id) }}"
                                                    class="btn btn-sm btn-primary" title="@lang('DBT/attributes.edit.title')">
                                                <i class="fas fa-pen fa-fw"></i>
                                            </a>
                                        @endcan
                                    @can('delete',$attr_category)
                                        <a
                                                href="{{ route('admin::dbt.attr_categories.delete', $attr_category->id) }}"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal" title="@lang('DBT/attributes.delete.title')">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $attr_category->id }}</td>
                                <td>{{ $attr_category->name }}</td>
                                <td>
                                    <i class="fas fa-fw {{$attr_category->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$attr_category->published ? trans('common.yes') : trans('common.no') }}
                                </td>
                                <td>{{ $attr_category->createdAtInfo }}</td>
                                <td>{{ $attr_category->updatedAtInfo }}</td>

                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $attr_categories,'searchFilters' => request(['search', 'published'])])@endcomponent
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