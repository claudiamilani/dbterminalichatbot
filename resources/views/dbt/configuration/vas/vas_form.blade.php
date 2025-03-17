<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('DBT/configuration.vas.title') }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ @asset('/js/app.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/configuration.css') }}">
</head>

<body>

<div id="vas">
    <div id="vas-config-container">
        <div class="title text-uppercase text-center">
            {{ trans('DBT/configuration.vas.form.title') }}
        </div>

        <form id="vas-config-form" action="{{ route('site::dbt.configurazione-vas.showForm') }}" method="POST"
              autocomplete="off">
            @csrf
            <div class="border-box">
                <table>
                    <tr>
                        <td>
                            <select name="vendor" id="vendor" required>
                                <option value="">{{ trans('DBT/configuration.attributes.vendor_select') }}</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>

                            <select name="model" id="model" required>
                                <option value="">{{ trans('DBT/configuration.attributes.model_select') }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="float-right img">
                            <button type="submit">
                                <img title="{{ trans('DBT/configuration.attributes.go') }}"
                                     src="{{ asset('/images/vai_button.gif') }}"
                                     alt="{{ trans('DBT/configuration.attributes.go') }}">
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </form>

        <div id="vas-result"></div>
    </div>

    <br><br>
</div>

<script>
    $('#vendor').change(function () {
        const vendorId = $(this).val();

        if (vendorId) {
            $.ajax({
                type: 'GET',
                url: "{{ route('site::dbt.configurazione-vas.getModels', ['vendor' => ':vendor']) }}".replace(':vendor', vendorId),
                success: function (data) {
                    $('#model').empty().append('<option value="">{{ trans('DBT/configuration.attributes.model_select') }}</option>');

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

    $('#vas-config-form').submit(function (e) {
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

</body>
</html>
