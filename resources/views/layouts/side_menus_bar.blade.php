<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">
        <!-- LOGO -->
        <div class="topbar-left">
            <a href="{{route('dashboard')}}" class="logo">
                  <span>
                  <img src="/assets/images/logo.png" alt="" height="22">
                  </span>
                <i>
                    <img src="/assets/images/logo.png" alt="" height="28">
                </i>
            </a>
        </div>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <!--<li class="menu-title">Navigation</li>-->
                <li>
                    <a href="{{route('dashboard')}}" class="{{getCurrentRouteName()==route('dashboard')?'active':''}}">
                        <i class="fi-air-play"></i> <span> Dashboard </span>
                    </a>
                </li>
                @if(isSuperAdmin())
                    <li>
                        <a href="{{route('gym_owner.list')}}" class="{{getUrlSegment(1)=='gym_owner' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/001-gym.png"></i>
                            <span>All Gym Owners </span>
                        </a>
                    </li>
                @endif

                @if(isSuperAdmin() || isGymOwner())
                    <li>
                        <a href="{{route('gym.list')}}" class="{{getUrlSegment(1)=='gym' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/001-dumbbell-1.png"></i>
                            <span>All Gyms </span>
                        </a>
                    </li>
                @endif

                @if(isSuperAdmin())
                    <li class="{{(getUrlSegment(1)=='trainer' || getUrlSegment(1) == 'activities' || getUrlSegment(2) == 'activity')?'active':''}}">
                        <a href="{{route('trainer.index')}}" class="{{(getUrlSegment(1)=='trainer' || getUrlSegment(1) == 'activities' || getUrlSegment(2) == 'activity')?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/users.png"></i>
                            <span>All Personal Trainers </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('facility.list')}}" class="{{getUrlSegment(1)=='facility' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/003-protein.png"></i>
                            <span>All Facilities </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('amenity.list')}}" class="{{getUrlSegment(1)=='amenity' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/003-protein.png"></i>
                            <span>All Amenities </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('category.list')}}" class="{{getUrlSegment(1)=='category' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/003-protein.png"></i>
                            <span>All Categories </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('faqs.list')}}" class="{{getUrlSegment(1)=='faqs' ?'active':''}}">
                            <i class="fi-layers"></i> <span>All FAQ's </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('/')}}/user/list/normal_users" class="{{getUrlSegment(1)=='user' ?'active':''}}">
                            <i class="fa fa-user"></i> <span>App Users </span>
                        </a>
                    </li>
                @endif

                @if(isGymOwner())
                    <li>
                        <a href="{{route('manage_visits.list')}}" class="{{getUrlSegment(1)=='manage' ?'active':''}}">
                            <i class="icon-nav"><img src="/assets/images/bank.png"></i>
                            <span>Manage Visits </span>
                        </a>
                    </li>
            @endif

            <!-- <li>
                   <a href="javascript: void(0);"><i class="fi-paper"></i> <span> Tables </span> <span class="menu-arrow"></span></a>
                   <ul class="nav-second-level" aria-expanded="false">
                      <li><a href="tables-basic.html">Basic Tables</a></li>
                      <li><a href="tables-datatable.html">Data Tables</a></li>
                      <li><a href="tables-responsive.html">Responsive Table</a></li>
                      <li><a href="tables-tablesaw.html">Tablesaw Tables</a></li>
                      <li><a href="tables-foo.html">Foo Tables</a></li>
                   </ul>
                </li> -->
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
