@section('topbar_content')
<style>
    .navbar-nav ul.notifications li > a {
        padding: 9px 0 !important;
    }
    .notifications {
        max-height: 400px; /* Set maximum height */
        overflow-y: auto; /* Add vertical scroll when content exceeds the height */
    }

    .notification-item {
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
<nav class="navbar navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-brand text-center">
            <a href="{{route('admin.dashboard')}}">
                <img src="{{ asset('/backend_assets/assets/images/logo.png')}}" alt="SSG Logo" class="img-responsive logo">
              </a>
        </div>

        <div class="navbar-right">
            <ul class="list-unstyled clearfix mb-0">
                <li>
                    <div class="navbar-btn btn-toggle-show">
                        <button type="button" class="btn-toggle-offcanvas"><i class="lnr lnr-menu fa fa-bars"></i></button>
                    </div>
                    <a href="javascript:void(0);" class="btn-toggle-fullwidth btn-toggle-hide"><i class="fa fa-bars"></i></a>
                </li>
                {{--<li>
                    <form id="navbar-search" class="navbar-form search-form">
                        <input value="" class="form-control" placeholder="Search here..." type="text">
                        <button type="button" class="btn btn-default"><i class="icon-magnifier"></i></button>
                    </form>
                </li>--}}
                <li>
                    <div id="navbar-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle icon-menu" data-toggle="dropdown">
                                    <i class="icon-bell"></i>
                                    <span class="notification-dot"></span>
                                </a>
                                @php
                                    $notifications = App\Models\Notification::getNotifications();
                                   
                                    function displayDate($date) {
                                        $carbonDate = \Carbon\Carbon::parse($date);
                                        $today = \Carbon\Carbon::today();
                                        $yesterday = \Carbon\Carbon::yesterday();

                                        if ($carbonDate->isToday()) {
                                            return $carbonDate->format('h:i A') . ' Today';
                                        } elseif ($carbonDate->isYesterday()) {
                                            return 'Yesterday';
                                        } else {
                                            return $carbonDate->format('M d, Y');
                                        }
                                    } 
                                @endphp 
                                
                                <ul class="dropdown-menu animated bounceIn notifications">
                                    <li class="header"><strong>You have {{$notifications->total()}} new Notifications</strong></li>
                                    @foreach($notifications as $notification)
                                    <li>
                                        <a href="javascript:void(0);">
                                            <div class="media">
                                                <div class="media-left">
                                                    <i class="icon-bell text-warning"></i>
                                                </div>
                                                <div class="media-body">
                                                    <p class="text"> {{$notification->messages}}</p>
                                                    <span class="timestamp">{{ displayDate($notification->created_at) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach 
                                    <li class="footer"><a href="{{url('admin/notification')}}" class="more">See all notifications</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle icon-menu" data-toggle="dropdown">
                                    <img class="rounded-circle" src="{{ asset('/backend_assets/assets/images/users.png')}}" width="30" alt="">
                                </a>
                                <div class="dropdown-menu animated flipInY user-profile">
                                    <div class="d-flex p-3 align-items-center">
                                        <div class="drop-left m-r-10">
                                            <img src="{{ asset('/backend_assets/assets/images/users.png')}}" class="rounded" width="50" alt="">
                                        </div>
                                        <div class="drop-right">
                                            <h4>{{ auth()->user()->name ?? null }}</h4>
                                            <p class="user-name">{{ auth()->user()->email ?? null }}</p>
                                        </div>
                                    </div>
                                    <div class="m-t-10 p-3 drop-list">
                                        <ul class="list-unstyled">
                                            <li><a href="#" data-toggle="modal" data-animation="bounce" data-target=".bs-example-modal-center"><i class="icon-lock"></i>Password Change</a></li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                                    <i class="icon-power"></i>
                                                    Logout
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endsection
