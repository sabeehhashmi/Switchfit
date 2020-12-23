<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="icon" href="{{asset('/assets/images/title.png')}}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" id="_token" value="{{csrf_token()}}">
    <title>SWITCHFIT</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    {{--    jquery--}}
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>

    <!-- App css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/metismenu.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('assets/plugins/switchery/switchery.min.css')}}">

    <!-- DataTables -->
    <link href="{{asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="{{asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>

<link href="{{ url('/') }}/assets/components/imgareaselect/css/imgareaselect-default.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="{{ url('/') }}/assets/css/jquery.awesome-cropper.css">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/custom.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/mycustom.css')}}"/>
    <script src="{{asset('assets/js/modernizr.min.js')}}"></script>

    {{--    notiflix--}}
    <script src="{{asset('assets/js/notiflix-aio-2.3.3.min.js')}}"></script>

    {{--    dropzone css--}}
    <link href="{{asset('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{asset('assets/plugins/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css"/>


    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>--}}
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>--}}
    <style type="text/css">
        span.fa.fa-calendar.input-calendar {
    position: absolute;
    right: 15px;
    top: 43px;
    font-size: 22px;
    z-index: 999;
    color: #3c15a3;
}
.payout-status-pending{
    width: 50%;
    float: left;
}
.notification-list .notify-item .notify-details {
    margin-bottom: 0;
    overflow: hidden;
    margin-left: 45px;
    white-space: initial;
}
.dropdown-item.active {
    color: #16181b;
    text-decoration: none;
    background-color: #f8f9fa;
}
.slimscroll {
    max-height: 460px !important;
}
.dropdown-lg {
    width: 369px;
}
#sidebar-menu > ul > li > a.active {
    color: #ffced6;
}
.btn.yes.btn-primary {
    margin: 0px 17px;
    background: #3c15a3 !important;
    border-color: #3c15a3 !important;

}
.modal-footer .btn-danger,.btn.yes.btn-primary{
    border-radius: 5px;
    font-size: 17px;
}
.modal-footer .btn-danger{
    margin: 0px 17px;
}
a.googl-redirct-link{
    color: #3c15a3;
    font-size: 14px;
    font-family: 'ProximaNova-Regular';
}
.btn.btn-info.btn-download-xlx {
    background: #3c15a3;
    border-color: #3c15a3;
}
.login-panel .checkbox label{
    font-size: 16px;
}
.login-panel h1,.login-panel .btn.btn-md.primay-btn{
    text-transform: none !important;
}
    </style>

    @yield('style')
    @yield('head_script')

</head>
@include('includes.alerts')
