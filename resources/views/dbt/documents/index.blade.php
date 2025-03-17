@extends('layouts.adminlte.template',['page_title' => trans('DBT/documents.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.documents.index'],'sortable' => true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('fileMimeType', $fileMimeType, request('fileMimeType') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('documentType', $documentType, request('documentType') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\DBT\Models\Document')
                                    <a
                                            title="@lang('DBT/documents.create.title')"
                                            href="{{ route('admin::dbt.documents.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/documents.attributes.id'))) !!}</td>
                            <td>{!! sort_link('title',trans('DBT/documents.attributes.file_path')) !!}</td>
                            <td>{!! sort_link('file_mime_type',trans('DBT/documents.attributes.file_mime_type')) !!}</td>
                            <td>{!! sort_link('documentType-name',trans('DBT/documents.attributes.document_type_id')) !!}</td>
                            <td>{!! sort_link('created_at',trans('DBT/documents.attributes.created_by_id')) !!}</td>
                            <td>{!! sort_link('updated_at',trans('DBT/documents.attributes.updated_by_id'))!!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($documents as $document)
                            <tr>
                                <td>
                                    @can('view', $document)
                                        <a
                                                href="{{ route('admin::dbt.documents.show', $document->id) }}"
                                                title="@lang('DBT/documents.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update', $document)
                                        <a
                                                href="{{ route('admin::dbt.documents.edit', $document->id) }}"
                                                title="@lang('DBT/documents.edit.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen fa-fw"></i>

                                        </a>
                                    @endcan
                                    @can('delete', $document)
                                        <a
                                                href="{{ route('admin::dbt.documents.delete', $document->id) }}"
                                                title="@lang('DBT/documents.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $document->id }}</td>
                                <td><a href="{{ Storage::disk('documents')->url($document->file_path)}}"
                                       target="_blank">{{ $document->title }} <sup><i
                                                    class="fas fa-fw fa-arrow-up-right-from-square"></i></sup></a></td>
                                <td>{{ optional($document)->file_mime_type }}</td>
                                <td>{{ $document->documentType->name }}</td>
                                <td>{{$document->created_at_info}}</td>
                                <td>{{$document->updated_at_info}}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('paginator')
                        @component('components.pagination', ['contents' => $documents,'searchFilters' => request(['search', 'documentType', 'fileMimeType'])])@endcomponent
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'documentType', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.document_types.select2'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/documents.attributes.document_type_id'):</b> ' + output.text;
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