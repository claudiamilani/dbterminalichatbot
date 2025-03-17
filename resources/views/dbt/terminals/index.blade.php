@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminals.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 12,'searchbox' => ['admin::dbt.terminals.index'],'sortable' => true,'hide_required_legend'=>true])
            @slot('advancedSearchBox')
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('ota_vendor', $ota_vendor, request('ota_vendor') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('vendor', $vendor, request('vendor') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('ingestion_source', $ingestion_source, request('ingestion_source') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('certified', $certified, request('certified') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('published', $published, request('published') ?? '-', ['class' => 'form-control pull-right']) !!}
                </div>
                <div class="clearfix"></div>
                <div class="input-group input-group-sm" style="max-width:500px;">
                    {!! Form::select('dbt_attribute_id', $dbt_attribute_id, request('dbt_attribute_id') ?? '', ['class' => 'form-control pull-right ' , 'id'=>'dbt_attribute_id']) !!}
                </div>
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::select('attribute_condition', $attribute_condition, request('attribute_condition') ?? '', ['class' => 'form-control pull-right noSubmit','id'=>'attribute_condition']) !!}
                </div>
                <input name="type" type="hidden" value="{{request('attribute_condition') ?? ''}}" id="type">
                <div class="input-group input-group-sm" style="max-width:250px;">
                    {!! Form::text('attribute_value', $attribute_value ?? null, [ 'class' => 'form-control pull-right', 'placeholder' => 'Valore', 'id'=>'attribute_value']) !!}
                    <div class="input-group-btn">
                        {!! Form::button('', ['type' => 'submit','class' => 'btn btn-default fa fa-search']) !!}
                    </div>
                </div>
            @endslot
            @slot('body')
                @component('components.table-list')
                    @slot('head')
                        <tr>
                            <td>
                                @can('create','App\DBT\Models\Terminal')
                                    <a
                                            title="@lang('DBT/terminals.create.title')"
                                            href="{{ route('admin::dbt.terminals.create') }}"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-plus fa-fw"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{!! sort_link('id', trans(('DBT/terminals.attributes.id'))) !!}</td>
                            <td>{!! sort_link('name',trans('DBT/terminals.attributes.name')) !!}</td>
                            <td>{!! sort_link('ota_vendor',trans('DBT/terminal_associate_otas.title')) !!}</td>
                            <td>{!! sort_link('vendor-name',trans('DBT/terminals.attributes.vendor_id')) !!}</td>
                            <td>{!! sort_link('ingestionSource-name',trans('DBT/terminals.attributes.ingestion_source_id')) !!}</td>
                            <td>{!! sort_link('certified',trans('DBT/terminals.attributes.certified'))!!}</td>
                            <td>{!! sort_link('published',trans('DBT/terminals.attributes.published'))!!}</td>
                        </tr>
                    @endslot
                    @slot('body')
                        @foreach($terminals as $terminal)
                            <tr>
                                <td>
                                    @can('view', $terminal)
                                        <a
                                                href="{{ route('admin::dbt.terminals.show', $terminal->id) }}"
                                                title="@lang('DBT/terminals.show.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-search fa-fw"></i>
                                        </a>
                                    @endcan
                                    @can('update', $terminal)
                                        <a
                                                href="{{ route('admin::dbt.terminals.edit', $terminal->id) }}"
                                                title="@lang('DBT/terminals.edit.title')"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen fa-fw"></i>

                                        </a>
                                    @endcan
                                    @can('delete', $terminal)
                                        <a
                                                href="{{ route('admin::dbt.terminals.delete', $terminal->id) }}"
                                                title="@lang('DBT/terminals.delete.title')"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#myModal">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                        </a>
                                    @endcan
                                </td>
                                <td>{{ $terminal->id }}</td>
                                <td>{{ $terminal->name }}</td>
                                <td>
                                    @can('update', $terminal)
                                        @if(!$terminal->ota_vendor && !$terminal->ota_model)
                                            <a
                                                    href="{{ route('admin::dbt.terminals.associate', $terminal->id) }}"
                                                    title="@lang('DBT/terminal_associate_otas.attributes.associate')"
                                                    data-toggle="modal" data-target="#myModal">
                                                @lang('DBT/terminal_associate_otas.attributes.associate')
                                            </a>
                                        @else
                                            <a
                                                    href="{{ route('admin::dbt.terminals.disassociate', $terminal->id) }}"
                                                    title="@lang('DBT/terminal_associate_otas.attributes.disassociate')"
                                                    data-toggle="modal" data-target="#myModal">
                                                @lang('DBT/terminal_associate_otas.attributes.disassociate')
                                            </a>
                                        @endif
                                    @endcan
                                </td>

                                <td>{{ $terminal->vendor->name }}</td>
                                <td>{{ $terminal->ingestionSource->name ?? trans('MDM Admin Ingestion') }}</td>
                                <td>
                                    <i class="fas fa-fw {{$terminal->certified ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$terminal->certified ? trans('common.yes') : trans('common.no') }}
                                </td>
                                <td>
                                    <i class="fas fa-fw {{$terminal->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$terminal->published ? trans('common.yes') : trans('common.no') }}
                                </td>
                            </tr>
                        @endforeach
                    @endslot

                    @slot('paginator')
                        @component('components.pagination',['contents' => $terminals,'searchFilters' => request(['search', 'vendor', 'ota_vendor', 'certified', 'published','attribute_value','dbt_attribute_id','attribute_condition', 'ingestion_source'])])@endcomponent
                    @endslot
                @endcomponent

            @endslot
                @slot('footer')
                    <div class="btn-toolbar">
                        <a href="{{ route('admin::dbt.terminals.export',array_merge(request()->all(),request()->route()->parameters)) }}"
                           class="btn btn-primary btn-md pull-right"> <i
                                    class="fas fa-fw fa-file-excel"></i> @lang('common.export')</a>
                        <a href="{{ route('admin::dbt.terminals.exportTranspose') }}"
                           class="btn btn-success btn-md pull-right"> <i
                                    class="fas fa-fw fa-file-excel"></i> @lang('Esporta Trasposta')</a>

                    </div>
                @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'vendor', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.terminals.select2TerminalVendors'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/terminals.attributes.vendor_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
    @component('components.select2_script',['name' => 'dbt_attribute_id', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.terminals.select2attributes'), 'linkedClear'=>'', 'callback'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output ='<b> @lang('DBT/transpose_configs.attributes.dbt_attribute_id'):</b> ' + output.text;
            return output;
        @endslot
    @endcomponent
    <script>
        $("[data-sort_reset]").contextmenu(function (e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;
        });
        $('#attribute_condition').on("change", function (e) {
            initBoolean()
        })

        $(document).ready(function () {
            loadConditions()
            initBoolean()
        });

        function loadConditions() {
            let select_condition = $('#attribute_condition');
            let attribute_value = $('#attribute_value');
            let type = null;
            if ($('#dbt_attribute_id').val()) {
                if ($('#dbt_attribute_id').select2('data')[0].type) {
                    type = $('#dbt_attribute_id').select2('data')[0].type
                } else {
                    type = '{{$attribute->type ?? ''}}'
                }
            }

            options = {'equals': 'Uguale a', 'like': 'Simile a'};
            select_condition.empty()

            if (type) {
                if (type == '{{\App\DBT\Models\DbtAttribute::TYPE_DECIMAL}}' || type == '{{\App\DBT\Models\DbtAttribute::TYPE_INT}}') {
                    options = {'more': 'Maggiore di', 'less': 'Minore di', 'equals': 'Uguale a'}
                }
                if (type == '{{\App\DBT\Models\DbtAttribute::TYPE_BOOLEAN}}') {
                    options = {"0": 'No', "1": 'Si'}
                }
                $('#type').val(type)
            }

            $.each(options, function (value, label) {
                    var option = $('<option>', {
                        value: value,
                        text: label
                    });
                    if (value === '{{request('attribute_condition')}}') {
                        option.prop('selected', true)
                    }
                    select_condition.append(option);
                }
            );

            if ($('#dbt_attribute_id').val() == null) {
                attribute_value.val('')
            }
        }

        function initBoolean() {
            if ($('#type').val() == 'BOOLEAN') {
                $('#attribute_value').val($('#attribute_condition').val())
                $('#attribute_value').hide()
            } else {
                $('#attribute_value').show()
            }
        }
    </script>
@endpush