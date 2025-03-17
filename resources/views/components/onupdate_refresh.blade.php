<script async>
    @php
        $uid = $uid ?? $uid = str_random(5);
        $reload_func = 'isUpToDateDataCheck_'.$uid;
        $reloadingTimeoutVar = 'reloadingTimeout_'.$uid;
        $checksCountVar = 'checksCount_'.$uid;
        $ajaxCallForUpdates_func = 'ajaxCallForUpdates_'.$uid;
        $updated_at_field = $updated_at_field ?? 'updated_at';
    @endphp
    //console.log('Reload uid: {{ $uid }}');
    var {{ $reloadingTimeoutVar }} = {{ $timeout ?? 15000 }};
    var {{ $checksCountVar }} = 0;


    {{ $reload_func }}();

    function {{ $reload_func }}() {
        setTimeout(function () {
            {{ $ajaxCallForUpdates_func }}();
            {{ $checksCountVar }}++;
        }, {{ $reloadingTimeoutVar }});
    }


    function {{ $ajaxCallForUpdates_func }}() {
        if (window.lft_should_not_refresh == true) {
            //console.log('Should not refresh: TRUE. Skipping refresh check')
            {{ $reload_func }}();
            return;
        }
        var url = "{{ route($route,['timestamp' => optional((($paginated ?? true) ? collect($items->items()) : $items)->sortByDesc($updated_at_field)->first())->{$updated_at_field}->timestamp ?? 0]) }}";
        //console.log('preparing ajax call {{ $uid }}');
        $.ajax({
            type: "GET",
            url: url,
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            success: function (result) {
                //console.log('success {{ $uid }}');
                if (result.refresh === true) {
                    //console.log('reloading {{ $uid }}');
                    location.reload();
                }
                {{ $reload_func }}()
                //console.log('requested new check in ' + {{ $reloadingTimeoutVar }} + 'ms')
            }
        });
        //console.log('ajax call done {{ $uid }}');
    }

</script>