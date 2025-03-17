@component('components.widget',['size' => '12', 'searchbox' => ['admin::dbt.terminals.show',$terminal->id],
'sortable'=>true, 'hide_required_legend'=>true,'searchbox_name'=>'search_tacs',
'withAnchor' => str_slug(trans('DBT/tacs.title')),
'sort_key'=>'sort_tacs',
'collapsible'=>true])
    @slot('title')
        @lang('DBT/tacs.title')
    @endslot
    @slot('body')
        @component('components.table-list')
            @slot('head')
                <tr>
                    <td class="btn-toolbar">
                        @can('create', 'App\DBT\Models\Tac')
                            <a href="{{ route('admin::dbt.tacs.create',paramsWithBackTo(['terminal_id'=>$terminal->id],'admin::dbt.terminals.show',[$terminal->id,'#'.nav_fragment('DBT/tacs.title')])) }}"
                               class="btn btn-sm btn-success"
                               title="@lang('DBT/tacs.create.title')">
                                <i class="fas fa-fw fa-plus"></i>
                            </a>
                        @endcan
                    </td>
                    <td>{!! sort_link('value', trans('DBT/tacs.attributes.value'), 'sort_tacs' ,str_slug(trans('DBT/tacs.title')) ) !!}</td>
                    <td>{!! sort_link('created_at', trans('DBT/tacs.attributes.created_at'), 'sort_tacs' ,str_slug(trans('DBT/tacs.title')) ) !!}</td>
                    <td>{!! sort_link('updated_at', trans('DBT/tacs.attributes.updated_at'), 'sort_tacs' ,str_slug(trans('DBT/tacs.title')) )!!}</td>
                </tr>
            @endslot

            @slot('body')
                @foreach($tacs as $tac)
                    <tr>
                        <td class="btn-toolbar">
                            @can('view', $tac)
                                <a href="{{ route('admin::dbt.tacs.show', paramsWithBackTo($tac->id,'admin::dbt.terminals.show',[$terminal->id,'#'.nav_fragment('DBT/tacs.title')])) }}"
                                   class="btn btn-sm btn-primary"
                                   title="@lang('DBT/tacs.show.title')">
                                    <i class="fas fa-fw fa-search"></i>
                                </a>
                            @endcan
                            @can('update',$tac)
                                <a href="{{ route('admin::dbt.tacs.edit', paramsWithBackTo($tac->id,'admin::dbt.terminals.show',[$terminal->id,'#'.nav_fragment('DBT/tacs.title')])) }}"
                                   class="btn btn-sm btn-primary"
                                   title="@lang('DBT/tacs.edit.title')">
                                    <i class="fas fa-fw fa-pen"></i>
                                </a>
                            @endcan
                            @can('delete',$tac)
                                <a href="{{ route('admin::dbt.tacs.delete', paramsWithBackTo($tac->id,'admin::dbt.terminals.show',[$terminal->id,'#'.nav_fragment('DBT/tacs.title')])) }}"
                                   class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal"
                                   title="@lang('DBT/tacs.delete.title')">
                                    <i class="fas fa-fw fa-trash-alt"></i>
                                </a>
                            @endcan
                        </td>
                        <td>{{ $tac->value }}</td>
                        <td>{{ $tac->createdAtInfo }}</td>
                        <td>{{ $tac->updatedAtInfo }}</td>
                    </tr>
                @endforeach
            @endslot
            @slot('paginator')
                @component('components.pagination',['contents' => $tacs,'searchFilters' => request(['search', 'terminal','search_tacs'])])@endcomponent
            @endslot
        @endcomponent
    @endslot
@endcomponent
