<div id="modal-header" class="title modal-title">
    {{ trans('DBT/configuration.send.mail.title') }}
</div>

<div class="border-box">
    <table>
        <thead>
        <tr>
            <td id="modal-message">
                {{ trans('DBT/configuration.send.mail.description') }}
            </td>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>
                <form id="sendForm" action="{{ route('site::dbt.configurazione-vas.send-mail') }}" method="POST"
                      autocomplete="off">
                    @csrf

                    <input type="hidden" name="file_path" value="{{ $file_path }}">

                    <table class="form-content">
                        <tr>
                            <td>
                                <label for="email">{{ trans('DBT/configuration.send.mail.label') }}</label>
                                <br>
                                <input type="email" name="email" class="form-control" required>
                            </td>

                            <td class="img">
                                <button type="submit">
                                    <img title="{{ trans('DBT/configuration.attributes.go') }}"
                                         src="{{ asset('/images/vai_button.gif') }}"
                                         alt="{{ trans('DBT/configuration.attributes.go') }}">
                                </button>
                            </td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="img">
                                <button type="button" data-dismiss="modal">
                                    <img title="{{ trans('DBT/configuration.attributes.close') }}"
                                         src="{{ asset('/images/chiudi_button.gif') }}"
                                         alt="{{ trans('DBT/configuration.attributes.close') }}">
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    $('input[name="email"]').focus();

    $('#sendForm').on('submit', function (event) {
        event.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const formData = form.serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: formData,

            success: function (response) {
                if (response.success) {
                    $('#modal-header').html(response.title);
                    $('#modal-message').html(response.message);
                } else {
                    $('#modal-header').html(response.title);
                    $('#modal-message').html(response.message);
                }

                $('.form-content').html('');
                $('#myModal').modal('show');
            },

            error: function () {
                $('#modal-header').html("{{ trans('DBT/configuration.send.mail_title_error') }}");
                $('#modal-message').html("{{ trans('DBT/configuration.send.mail_message_error') }}");
                $('.form-content').html("");

                $('#myModal').modal('show');
            }
        });
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').html('');
    });
</script>
