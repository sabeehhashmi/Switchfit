<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
<body>
@include('layouts.header')
@if(notAllowedSideBarTopBarAndFooter())
    @include('layouts.footer')
@endif
@include('layouts.footer_scripts')
</body>
</html>
