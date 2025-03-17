<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('DBT/configuration.windtre.title') }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ @asset('/js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/configuration.css') }}">
</head>

<body>

<div id="windtre">
    <div id="windtre-config-container">
        <div class="container">

            <form id="windtre-config-form" action="{{ route('site::dbt.configurazione-windtre.showForm') }}"
                  method="POST"
                  autocomplete="off">
                @csrf

                <table>
                    <tr>
                        <td class="td-select label">{{ trans('DBT/configuration.windtre.form.vendor') }}:</td>
                        <td class="td-select label">{{ trans('DBT/configuration.windtre.form.model') }}:</td>
                    </tr>
                    <tr>
                        <td>
                            <select name="vendor" id="vendor" required>
                                <option value=""></option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="model" id="model" required>
                                <option value=""></option>
                            </select>
                        </td>
                        <td>
                            <button type="submit">
                                {{ trans('DBT/configuration.windtre.form.button') }}
                            </button>
                        </td>
                    </tr>
                </table>
            </form>

            <div id="vas-result"></div>

        </div>

        <script>
            $('#vendor').change(function () {
                const vendorId = $(this).val();

                if (vendorId) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('site::dbt.configurazione-vas.getModels', ['vendor' => ':vendor']) }}".replace(':vendor', vendorId),
                        success: function (data) {
                            $('#model').empty().append('<option value=""></option>');

                            $.each(data, function (index, terminal) {
                                $('#model').append('<option value="' + terminal.id + '">' + terminal.name + '</option>');
                            });
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#model').empty().append('<option value="">-</option>');
                }
            });

            $('#windtre-config-form').submit(function (e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();

                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: formData,

                    success: function (response) {
                        $('#vas-result').html(response);
                    },

                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
    </div>
</div>
</body>
</html>
