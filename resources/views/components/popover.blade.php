<script>
    function initPopovers_{{$identifier}}() {
        $("{{$selector}}").popover({
            trigger: 'hover',
            delay: {'show': 1000},
            html: true,
            container: 'body',
            placement: 'right',
            content: '<div id="myPopover_{{$identifier}}">Caricamento...</div>'
        });
        $("{{$selector}}").on('shown.bs.popover', function () {
            ajaxCall_{{$identifier}}($(this), function (formattedContent) {
                console.log(formattedContent)
                $("#myPopover_{{$identifier}}").html(formattedContent)
            })
        })
    }
    function ajaxCall_{{$identifier}}(element, callback) {
        $.ajax({
            url: "{{$ajax_url}}",
            @if($method != 'GET')
            headers: {
                'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
            },
            @endif
            method: '{{$method ?? 'GET'}}',
            data: {@foreach($dataParams as $key => $dataParam)
            {{$key}}:element.data('{{$dataParam}}'),
            @endforeach},
            success: function (data) {
                callback(data)
            },
            error: function () {
                return 'Errore'
            }
        });
    }
</script>