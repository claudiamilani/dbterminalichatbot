@extends('layouts.adminlte.template',['page_title' => trans('DBT/attributes.title')])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.attributes.index'],'sortable' => true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('category_id', $category ?? [], request('category_id') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('attribute_type', $attribute_types ?? [], request('attribute_type') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('published', $published, request('published') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">
                                @can('create','App\DBT\Models\DbtAttribute')
                                    <a href="{{ route('admin::dbt.attributes.create') }}"
                                       class="btn btn-sm btn-success" title="@lang('DBT/attributes.create.title')"> <i class="fas fa-plus fa-fw"></i></a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id',trans('DBT/attributes.attributes.id')) !!}</td>
                            <td>{!! sort_link('name',trans('DBT/attributes.attributes.name')) !!}</td>
                            <td>{!! sort_link('description',trans('DBT/attributes.attributes.description')) !!}</td>
                            <td>{!! sort_link('attr_category_id',trans('DBT/attributes.attributes.attr_category_id')) !!}</td>
                            <td>{!! sort_link('type',trans('DBT/attributes.attributes.type')) !!}</td>
                            <td>{!! sort_link('published',trans('DBT/attributes.attributes.published')) !!}</td>
                            <td>{!! sort_link('created_at', trans('DBT/attributes.attributes.created_at')) !!}</td>
                            <td>{!! sort_link('updated_at', trans('DBT/attributes.attributes.updated_at')) !!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($attributes as $attribute)
                            <tr>
                                <td class="btn-toolbar vertical-margin-sm">
                                    @can('view',$attribute)
                                        <a href="{{ route('admin::dbt.attributes.show', $attribute->id) }}"
                                                class="btn btn-sm btn-primary" title="@lang('DBT/attributes.show.title')">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update',$attribute)
                                        <a href="{{ route('admin::dbt.attributes.edit', $attribute->id) }}"
                                                class="btn btn-sm btn-primary" title="@lang('DBT/attributes.edit.title')">
                                            <i class="fas fa-pen fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$attribute)
                                        <a href="{{ route('admin::dbt.attributes.delete', $attribute->id) }}"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal" title="@lang('DBT/attributes.delete.title')">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $attribute->id }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>{{ $attribute->description }}</td>
                                <td>{{ $attribute->category->name }}</td>
                                <td>{{ $attribute->AttributeTypeLabel }}</td>
                                <td>
                                    <i class="fas fa-fw {{$attribute->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$attribute->published ? trans('common.yes') : trans('common.no') }}
                                </td>
                                <td>{{ $attribute->createdAtInfo }}</td>
                                <td>{{ $attribute->updatedAtInfo }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $attributes,'searchFilters' => request(['search','attribute_type','category_id', 'published'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'category_id', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.attributes.select2Category'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/attributes.attributes.attr_category_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
    <script>
        $("[data-sort_reset]").contextmenu(function (e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;

        });

    </script>
@endpush