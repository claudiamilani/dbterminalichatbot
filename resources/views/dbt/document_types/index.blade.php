@extends('layouts.adminlte.template',['page_title' => trans('DBT/document_types.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.document_types.index'],'sortable' => true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('channel', $channel, request('channel') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\DBT\Models\DocumentType')
                                    <a
                                            title="@lang('DBT/document_types.create.title')"
                                            href="{{ route('admin::dbt.document_types.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/document_types.attributes.id'))) !!}</td>
                            <td>{!! sort_link('name',trans('DBT/document_types.attributes.name')) !!}</td>
                            <td>{!! sort_link('channel-name',trans('DBT/document_types.attributes.channel_id')) !!}</td>
                            <td>{!! sort_link('created_at',trans('DBT/document_types.attributes.created_by_id')) !!}</td>
                            <td>{!! sort_link('updated_at',trans('DBT/document_types.attributes.updated_by_id'))!!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($document_types as $document_type)
                            <tr>
                                <td>
                                    @can('view', $document_type)
                                        <a
                                                href="{{ route('admin::dbt.document_types.show', $document_type->id) }}"
                                                title="@lang('DBT/document_types.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update', $document_type)
                                        <a
                                                href="{{ route('admin::dbt.document_types.edit', $document_type->id) }}"
                                                title="@lang('DBT/document_types.edit.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen fa-fw"></i>

                                        </a>
                                    @endcan
                                    @can('delete', $document_type)
                                        <a
                                                href="{{ route('admin::dbt.document_types.delete', $document_type->id) }}"
                                                title="@lang('DBT/document_types.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $document_type->id }}</td>
                                <td>{{ $document_type->name }}</td>
                                <td>{{ $document_type->channel->name }}</td>
                                <td>{{$document_type->created_at_info}}</td>
                                <td>{{$document_type->updated_at_info}}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination',['contents' => $document_types,'searchFilters' => request(['search', 'channel'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'channel', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.channels.select2'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/document_types.attributes.channel_id'):</b> ' + output.text;
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