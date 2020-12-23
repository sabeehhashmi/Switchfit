<!-- Top Bar Start -->
<div class="topbar">
    <nav class="navbar-custom">
        <div class="list-unstyled topbar-left-menu float-left mb-0">
           @yield('breadcrumb')
       </div>
       <style type="text/css">

           .delete-notification {
            padding: 0px 5px;
            display: inline !important;
            cursor: pointer;
            position: absolute;
            top: -37px;
            left: -38px;
        }
        .delete-notification {
            border: 2px solid #e08353;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            text-align: center;
            line-height: 17px;
            font-size: 16px !important;
            color: #e08353;
        }

        .notification-list .notify-item .notify-details {
            padding: 0 10px 0 0;
        }

        .delete-notification-p {
            position: relative;
            float: right;
            top: -30px;
        }
    </style>
    @php
    $notifications = get_notifications();
    $notifications_count = get_notifications_count();
    @endphp
    <ul class="list-unstyled topbar-right-menu float-right mb-0">
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            <i class="fi-bell noti-icon"></i>
            @if($notifications_count->count()>0)
            <span class="badge badge-danger badge-pill noti-icon-badge">
                {{($notifications_count->count()>0)?$notifications_count->count():''}}
            </span>
            @endif
        </a>
        @if($notifications->count()>0)
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-lg">
            <!-- item-->
            <div class="dropdown-item noti-title">
                <h5 class="m-0"><span class="float-right">{{-- <a href="{{ url('/') }}/user/clear-notifications" class="text-dark"><small>Clear All</small></a> --}} </span>Notification
                </h5>
            </div>
            <div class="slimscroll" style="max-height: 230px;">

                <!-- item-->
                @if(isSuperAdmin())
                @if(!empty($notifications->first()))
                @foreach($notifications as $notification)
                <a href="{{getLinkFromNotification($notification->screen,$notification->source_id)}}?notification_id={{$notification->id}}" class="dropdown-item notify-item">
                    <div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
                    <p class="notify-details">{{$notification->description}}<small class="text-muted"><i class="fa fa-clock-o" aria-hidden="true"></i>
                        {{date("d M Y h:i A", strtotime($notification->created_at))}}  </small>
                    </p>
                </a>
                <span class="delete-notification-p">
                    <a class="delete-notification" href="/user/delete-notifications/{{$notification->id}}">x</span>
                    </a>
                    @endforeach
                    @endif
                    @else
                    @if(!empty($notifications->first()))
                    @foreach($notifications as $notification)
                    @php
                    $id = $notification->source_id;

                    @endphp
                    <a href="{{getLinkFromNotification($notification->screen,$id)}}?notification_id={{$notification->id}}" class="dropdown-item notify-item">
                        <div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
                        <p class="notify-details">{{$notification->description}}<small class="text-muted"> {{date("d M Y", strtotime($notification->created_at))}}
                         <span class="delete-notification" onclick="deleteNotification({{$notification->id}})">x</span></small></p>
                     </a>
                     @endforeach
                     @endif
                     @endif

                 </div>
                 <!-- All-->

             </div>
             @endif
         </li>
         <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            @if(isGymOwner())
            <img
            src="{{\Illuminate\Support\Facades\Auth::user()->logo ? asset(\Illuminate\Support\Facades\Auth::user()->logo) : '/assets/images/default-dp.JPG'}}"
            alt="user" class="rounded-circle">
            <span class="ml-1">{{\Illuminate\Support\Facades\Auth::user()->first_name}} <i
                class="mdi mdi-chevron-down"></i> </span>
                @else
                <img src="/assets/images/default-dp.jpg" alt="user" class="rounded-circle">
                <span class="ml-1">{{\Illuminate\Support\Facades\Auth::user()->first_name}} <i
                    class="mdi mdi-chevron-down"></i> </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>
                    @if(isGymOwner())
                    <!-- item-->
                    <a href="{{route('user.gym_owner.profile')}}" class="dropdown-item notify-item">
                        <i class="fi-head"></i> <span>My Account</span>
                    </a>
                    <!-- item-->
                    <a href="{{route('bank_account.create')}}" class="dropdown-item notify-item">
                        <i class="fi-cog"></i> <span>Payout Account</span>
                    </a>
                    @else
                     <a href="{{route('user.admin.settings')}}" class="dropdown-item notify-item">
                        <i class="fi-head"></i> <span>Settings</span>
                    </a>
                    @endif
                    <!-- item-->
                    <form action="{{route('logout')}}" method="post">
                        @csrf
                        <button type="submit" class="dropdown-item notify-item">
                            <i class="fi-power"></i> <span>Logout</span>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
</div>
<script type="text/javascript">

    function deleteNotification(id){

        location.href = "/delete-notifications/"+id;

    }

</script>
<!-- Top Bar End -->
