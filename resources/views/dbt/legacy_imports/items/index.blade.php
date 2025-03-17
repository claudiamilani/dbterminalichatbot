@component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.legacy_imports.show',$import->id],'sortable' => true])
    @slot('advancedSearchBox')
        <div class="input-group input-group-sm" style="max-width:250px;">
            {!! Form::select('status', $item_status, request('status') ?? '-', ['class' => 'form-control pull-right']) !!}
        </div>
        <div class="input-group input-group-sm" style="max-width:250px;">
            {!! Form::select('result', $item_results, request('result') ?? '-', ['class' => 'form-control pull-right']) !!}
        </div>
    @endslot
    @slot('body')
        @component('components.table-list')
            @slot('head')
                <tr>
                    <td></td>
                    <td>{!! sort_link('legacy_id', trans(('DBT/legacy_import_items.attributes.legacy_id'))) !!}</td>
                    <td>{!! sort_link('status', trans(('DBT/legacy_import_items.attributes.status'))) !!}</td>
                    <td>{!! sort_link('result', trans(('DBT/legacy_import_items.attributes.result'))) !!}</td>
                    <td>{!! sort_link('created_at',trans('DBT/legacy_import_items.attributes.created_at')) !!}</td>
                    <td>{!! sort_link('updated_at',trans('DBT/legacy_import_items.attributes.updated_at')) !!}</td>
                </tr>
            @endslot
            @slot('body')
                @foreach($items as $item)
                    <tr>
                        <td>
                            @can('view',$import)
                                <a
                                        href="{{ route('admin::dbt.legacy_imports.items.show', $item->id) }}"
                                        title="@lang('DBT/legacy_import_items.show.title')"
                                        class="btn btn-sm btn-primary"
                                        data-toggle="modal" data-target="#myModalLg">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                            @endcan
                        </td>
                        <td>{{ $item->legacy_id }}</td>
                        <td>@lang('DBT/legacy_imports.status.'.$item->status)</td>
                        <td>@if($item->result)
                                @lang('DBT/legacy_imports.items.result.'.$item->result)
                            @endif</td>
                        <td>{{ optional($item->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ optional($item->updated_at)->format('d/m/Y H:i:s') }}</td>
                    </tr>
                @endforeach
            @endslot
            @slot('paginator')
                @component('components.pagination',['contents' => $items,'searchFilters' => request(['search','status','result'])])@endcomponent
            @endslot
        @endcomponent
    @endslot
@endcomponent
