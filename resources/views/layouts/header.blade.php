<!-- Begin page -->
<div id="wrapper">

{{--{{dd(getCurrentRouteName())}}--}}
@if(notAllowedSideBarTopBarAndFooter() )

    @include('layouts.side_menus_bar')
    <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            @include('layouts.top_bar')
            @endif

            @yield('content')
            @if(notAllowedSideBarTopBarAndFooter())
        </div>
    @endif
</div>

@yield('modals')
