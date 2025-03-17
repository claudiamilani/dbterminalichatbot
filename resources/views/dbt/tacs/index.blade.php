@extends('layouts.adminlte.template',['page_title' => trans('DBT/tacs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => '12', 'searchbox' => ['admin::dbt.tacs.index'], 'sortable'=>true, 'hide_required_legend'=>true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('terminal', $terminal, request('terminal') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td class="btn-toolbar">
                                @can('create', 'App\DBT\Models\Tac')
                                    <a href="{{ route('admin::dbt.tacs.create') }}"
                                       class="btn btn-sm btn-success"
                                       title="@lang('DBT/tacs.create.title')">
                                        <i class="fas fa-fw fa-plus"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/tacs.attributes.id'))) !!}</td>
                            <td>{!! sort_link('terminal_id', trans(('DBT/tacs.attributes.terminal_id'))) !!}</td>
                            <td>{!! sort_link('value', trans(('DBT/tacs.attributes.value'))) !!}</td>
                            <td>{!! sort_link('created_at', trans(('DBT/tacs.attributes.created_at'))) !!}</td>
                            <td>{!! sort_link('updated_at', trans(('DBT/tacs.attributes.updated_at'))) !!}</td>
                        </tr>
                    @endslot

                    @slot('body')
                        @foreach($tacs as $tac)
                            <tr>
                                <td class="btn-toolbar">
                                    @can('view', $tac)
                                        <a href="{{ route('admin::dbt.tacs.show', $tac->id) }}"
                                           class="btn btn-sm btn-primary"
                                           title="@lang('DBT/tacs.show.title')">
                                            <i class="fas fa-fw fa-search"></i>
                                        </a>
                                    @endcan
                                    @can('update',$tac)
                                        <a href="{{ route('admin::dbt.tacs.edit', $tac->id) }}"
                                           class="btn btn-sm btn-primary"
                                           title="@lang('DBT/tacs.edit.title')">
                                            <i class="fas fa-fw fa-pen"></i>
                                        </a>
                                    @endcan
                                    @can('delete',$tac)
                                        <a href="{{ route('admin::dbt.tacs.delete', $tac->id) }}"
                                           class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal"
                                           title="@lang('DBT/tacs.delete.title')">
                                            <i class="fas fa-fw fa-trash-alt"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $tac->id }}</td>
                                <td>{{ $tac->terminal->name }}</td>
                                <td>{{ $tac->value }}</td>
                                <td>{{ $tac->createdAtInfo }}</td>
                                <td>{{ $tac->updatedAtInfo }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $tacs,'searchFilters' => request(['search', 'terminal'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'terminal', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.terminals.select2Terminals'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/tacs.attributes.terminal_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
@endpush
