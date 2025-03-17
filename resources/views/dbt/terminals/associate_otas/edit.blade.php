@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot

    @slot('title')
        @lang('DBT/terminal_associate_otas.edit.title')
    @endslot

    @slot('content')
        {!! Form::model($terminal, ['method' => 'patch', 'route' => ['admin::dbt.terminals.link', $terminal],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
        <div class="form-group required">
            <div class="col-md-6">
                <label for="vendor">@lang('DBT/terminal_associate_otas.edit.select_vendor')</label>
                <select name="vendor" id="vendor" class="form-control" required>
                    <option value="">@lang('DBT/terminal_associate_otas.edit.select_vendor')</option>
                </select>
            </div>

            <div class="col-md-6 required">
                <label for="model">@lang('DBT/terminal_associate_otas.edit.select_model')</label>
                <select name="model" id="model" class="form-control" required>
                    <option value="">@lang('DBT/terminal_associate_otas.edit.select_model')</option>
                </select>
            </div>
        </div>

        <div class="btn-toolbar pull-right" style="margin-top: 30px">
            <button type="submit" class="btn btn-sm btn-primary" id="save-button">
                <i class="fas fa-save fa-fw"></i> @lang('common.form.save')
            </button>
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                <i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')
            </button>
        </div>
        {!! Form::close() !!}

        <script>
            $.ajax({
                type: 'POST',
                url: "{{ route('admin::dbt.terminals.getVendors') }}",
                data: {
                    _token: '{{ csrf_token() }}'
                },

                success: function (data) {
                    $('#vendor').empty().append('<option value="">@lang('DBT/terminal_associate_otas.edit.select_vendor')</option>');

                    $(data).find('vendor').each(function () {
                        const vendorName = $(this).attr('name');
                        $('#vendor').append('<option value="' + vendorName + '">' + vendorName + '</option>');
                    });
                },

                error: function (xhr) {
                    console.error("Errore nella richiesta:", xhr.responseText);
                }
            });

            $('#vendor').change(function () {
                const vendorName = $(this).val();

                if (vendorName) {
                    $.get("{{ route('admin::dbt.terminals.getModels', ['vendor' => ':vendor']) }}".replace(':vendor', vendorName), function (data) {
                        $('#model').empty().append('<option value="">@lang('DBT/terminal_associate_otas.edit.select_model')</option>');

                        $(data).find('vendor').each(function () {
                            const vendor = $(this);
                            const name = vendor.attr('name');

                            if (name === vendorName) {
                                vendor.find('model').each(function () {
                                    const modelName = $(this).attr('name');
                                    $('#model').append('<option value="' + modelName + '">' + modelName + '</option>');
                                });
                            }
                        });
                    });
                } else {
                    $('#model').empty().append('<option value="">@lang('DBT/terminal_associate_otas.edit.select_model')</option>');
                }
            });
        </script>
    @endslot
@endcomponent
