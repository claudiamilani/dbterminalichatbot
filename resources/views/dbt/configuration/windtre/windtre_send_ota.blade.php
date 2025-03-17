<script src="{{ @asset('/js/app.js') }}"></script>

<div id="modal-header" class="modal-title">
    {{ trans('DBT/configuration.send.ota.title') }}
</div>

<table>
    <tr>
        <td>
            <div class="content">
                <div id="modal-message" class="modal-message"></div>

                <form class="form-content" id="sendForm" action="{{ route('site::dbt.configurazione-vas.send-ota') }}"
                      method="POST"
                      autocomplete="off">
                    @csrf

                    <input type="hidden" name="terminal_id" value="{{ $param_1 }}">
                    <input type="hidden" name="ota_id" value="{{ $param_2 }}">

                    <table>
                        <tr>
                            <td class="hint">
                                {{ trans('DBT/configuration.send.ota.ota_hint') }}
                            </td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td>
                                <div class="ota-input">
                                    <span class="hint">{{ trans('DBT/configuration.send.ota.label') }}:</span>
                                    <input type="text" name="phone"
                                           placeholder="{{ trans('DBT/configuration.send.ota.placeholder') }}" required>

                                    <button type="submit">
                                        {{ trans('DBT/configuration.attributes.send') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="hint">
                                {{ trans('DBT/configuration.send.ota.hint') }}
                            </td>
                        </tr>
                    </table>
                </form>

                <button type="button" data-dismiss="modal">
                    {{ trans('DBT/configuration.attributes.close') }}
                </button>
            </div>
        </td>
    </tr>
</table>

<script>
    $('input[name="phone"]').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    }).focus();

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

                $('.form-content, .hint').html('');
                $('#myModal').modal('show');
            },

            error: function () {
                $('#modal-header').html("{{ trans('DBT/configuration.send.mail_title_error') }}");
                $('#modal-message').html("{{ trans('DBT/configuration.send.mail_message_error') }}");

                $('.form-content, .hint').html('');
                $('#myModal').modal('show');
            }
        });
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').html('');
    });
</script>
