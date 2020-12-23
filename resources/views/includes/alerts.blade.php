

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-2" >
                <div class="flash-message noti-alert">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <script>
                            setTimeout(function () {
                                notification('{{$msg}}','{{ Session::get('alert-' . $msg) }}');
                                @php session()->forget('alert-' . $msg); @endphp
                            },1500);

                            // $(function(){
                            //     setTimeout(function() {
                            //         $('.flash-message').slideUp();
                            //     }, 5000);
                            // });

                            </script>

{{--                            <p style="margin-right: 30px;margin-left: 30px;"--}}
{{--                               class="alert text-center alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}--}}
{{--                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                                    <span aria-hidden="true">&times;</span>--}}
{{--                                </button>--}}
{{--                            </p>--}}
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

<script>
//     window.addEventListener('popstate', function(event) {
//         // The popstate event is fired each time when the current history entry changes.
// console.log( event);
//         var r = confirm("You pressed a Back button! Are you sure?!");
//
//         if (r == true) {
//             // Call Back button programmatically as per user confirmation.
//             history.back();
//             // Uncomment below line to redirect to the previous page instead.
//             // window.location = document.referrer // Note: IE11 is not supporting this.
//         } else {
//             // Stay on the current page.
//             history.pushState(null, null, window.location.pathname);
//         }
//         history.pushState(null, null, window.location.pathname);
//
//     }, false);

    // jQuery(document).ready(function($) {
    //     // if (window.history && window.history.pushState) {
    //         // window.history.pushState('', null, './');
    //         $(window).on('popstate', function() {
    //             alert('Back button was pressed.',window.history.back());
    //         });
    //
    //     // }
    // });
</script>
