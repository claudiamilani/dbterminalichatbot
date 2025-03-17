<script>
    @isset($bind_to)
    $('{{ $bind_to }}').on('{{ $bind_event ?? 'click' }}',function () {
        {{ $name }}();
    });

    @endisset

    function {{$name}}() {
        var items = $('#{{ $form }}').serializeArray();
        @if(isset($redirect_on_success))
        items.push({name: '_redirectOnSuccess', value: '{{ $redirect_on_success }}'});
        @endif
        @if(isset($method))
        items.push({name: '_method', value: '{{ strtolower($method ?? 'POST') }}'});
        @endif
        @if(isset($before_ajax))
        var output_before_ajax = {!! $before_ajax ?? 'null' !!}
        @endif
        $.ajax({
            url: "{{ $route }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: items,
            timeout: {{$timeout ?? 3000}},
            type: "{{ $method ?? 'POST' }}",
            success: function (result) {
                var callback = {!! $callback ?? 'null'  !!}
                if (result.redirectTo) {
                    window.location.replace(result.redirectTo);
                } else {
                    @if(!isset($disable_notifications))
                    toastr.success(result.msg);
                    @endif
                }
            },
            error: function (xhr) {
                var error_callback = {!! $error_callback ?? 'null' !!}
                console.log(xhr);
                @if(!isset($disable_notifications))
                parseAjaxErrors(xhr);
                @endif
            }
        });
    }

</script>