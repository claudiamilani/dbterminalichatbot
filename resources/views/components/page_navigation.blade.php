<div style="margin-top:15px;min-width:90px;" id="{{ $container ?? $container = str_random(5)}}">
    <i id="collapse-btn{{ $container }}" class="fa fa-minus-square collapse-btn"> QuickNav</i>
    <div class="btn-toolbar pagenav-items"></div>
</div>

<a id="{{ $backToTheTop = 'top'.$container }}" class="backTopBtn"><i class="glyphicon glyphicon-chevron-up"></i></a>

@push('scripts')
    @once
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
                integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    @endonce
    <script>
        var container = $('#{{ $container}}');
        var items = $('#{{ $container}} .pagenav-items');
        //console.log('Appending page nav links to '+ '{{ $container }}');
        $('.mdl-page-anchor').each(function () {
            try {
                label = $(this).attr('data-anchor-title');
                hash = '#' + $(this).attr('id').substring(1);
                //console.log('Building link for '+ label + ' with hash '+hash);
                items.append("<a href='" + hash + "' class='btn btn-xs btn-warning pull-left mdl-anchor'><i class='glyphicon glyphicon-link'></i> " + label + "</a>");
            } catch (e) {
                console.log('Error generating page nav links: ' + e.toString())
            }

        });
        $('.mdl-anchor').click(function (event) {
            event.preventDefault();
            //console.log('Scrolling to ' + this.hash);
            try {
                if (window.location.hash === this.hash) {
                    $('html, body').animate({
                        scrollTop: $(this.hash.replace('#', '#_')).offset().top - 20
                    }, 500);
                }
                window.location.hash = this.hash;

                //console.log('Scrolling...')
            } catch (e) {
                console.log('Error scrolling: ' + e.toString())
            }

        });

        $('#collapse-btn{{$container}}').click(function (event) {
            try {
                $('#{{ $container}} .pagenav-items').toggle(350);
                $(this).toggleClass('fa-minus-square').toggleClass('fa-plus-square').closest('span').hide();
            } catch (e) {
                console.log('Error scrolling: ' + e.toString())
            }

        });

        $(document).ready(function () {
            scrollToHash();
        });

        function scrollToHash() {
            var page_hash = window.location.hash;
            //age hash: ' + window.location.hash);
            //console.log('Page hash typeof: ' + typeof $(page_hash).valueOf());
            if (window.location.hash && typeof $(page_hash).valueOf() !== 'undefined') {
                //console.log('Scrolling to: ' + window.location.hash.replace('#', '#_'));
                try {
                    $('html, body').animate({
                        scrollTop: $(window.location.hash.replace('#', '#_')).offset().top - 42
                    }, 500);
                    //console.log('Scrolling...')
                } catch (e) {
                    console.log('Error scrolling: ' + e.toString())
                }
            }
        }

        $(window).on('hashchange', function (e) {
            //console.log( 'Hash changed, scrolling to: ' + window.location.hash);
            scrollToHash();
        });

    </script>
    <script>
        var never_dragged = false;

        window.onscroll = function () {
            scrollFunction{{$backToTheTop}}()
        };
        $('#{{ $backToTheTop }}').click(function () {
            $('html, body').animate({scrollTop: 0});
        });

        function scrollFunction{{ $backToTheTop }}() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                $('#{{ $backToTheTop}}').css('display', 'table');
            } else {
                $('#{{ $backToTheTop}}').css('display', 'none');
            }
            if (document.body.scrollTop > 110 || document.documentElement.scrollTop > 110) {

                $('#{{ $container}}').addClass('floatingNav');
                $('#{{ $container}}').draggable({
                    containment: "window",
                    drag: function (event, ui) {
                        $('#{{ $container}}').css('right', '');
                        $(".selector").draggable("widget").css('width', 'auto')
                    }
                });

                if (never_dragged === false) {
                    $('#{{ $container}}').draggable("widget").css({'top': 10, 'right': 20});
                }
                never_dragged = true;
            } else {
                $('#{{ $container}}').removeClass('floatingNav');
                $('#collapse-btn{{ $container}}').removeClass('fa-plus-square').addClass('fa-minus-square');
                $('#{{ $container}} .pagenav-items').show();
            }
        }
    </script>
@endpush

