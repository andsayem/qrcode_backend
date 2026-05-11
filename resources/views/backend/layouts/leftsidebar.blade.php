@section('leftsidebar_content')
<style>
    .active-submenu {
        border-left: 5px solid white;
    }
</style>
@php
$user = Auth::user();

// Get all permissions directly assigned to the user
$userPermissions = $user->permissions;

// Get all permissions via roles assigned to the user
$rolePermissions = $user->getPermissionsViaRoles();

// Merge both sets of permissions
$allPermissions = $userPermissions->merge($rolePermissions);

// Convert the collection of permissions to an array of permission names
$allPermissionNames = $allPermissions->pluck('name')->toArray();
//dd($allPermissionNames);
@endphp
<div id="leftsidebar" class="sidebar">
    <div class="sidebar-scroll">
        <nav id="leftsidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu">
                <li class="heading">Main</li>
                <li class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}"><a
                        href="{{ route('admin.dashboard') }}"><i class="icon-home"></i><span>Dashboard</span></a></li>

                <li class="{{ Route::currentRouteName() == 'admin.mgtdashboard' ? 'active' : '' }}"><a
                        href="{{ route('admin.mgtdashboard') }}"><i class="icon-rocket"></i><span>Management Dashboard</span></a></li>

                @if (count(array_intersect(['user-list', 'role-list'], $allPermissionNames)) > 0)
                <li
                    class="{{ in_array(Route::currentRouteName(), ['admin.users.index', 'admin.users.create', 'admin.users.edit', 'admin.roles.index', 'admin.roles.create', 'admin.roles.edit']) ? 'active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="icon-shield"></i>
                        <span>User Management</span>
                    </a>
                    <ul>
                        @if (count(array_intersect(['user-list'], $allPermissionNames)) > 0)
                        <li>
                            <a href="{{ route('admin.users.index') }}">
                                <span>Users</span>
                            </a>
                        </li>
                        @endif
                        @if (count(array_intersect(['role-list'], $allPermissionNames)) > 0)
                        <li>
                            <a href="{{ route('admin.roles.index') }}">
                                <span>Roles</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (count(array_intersect(['category-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.categories.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fa fa-delicious" aria-hidden="true"></i>
                        <span>Category</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['product-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.products.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}">
                        <i class="fa fa-cubes" aria-hidden="true"></i>
                        <span>Product</span>
                    </a>
                </li>
                @endif
                @if (count(array_intersect(['vendor-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.vendors.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.vendors.index') }}">
                        <i class="fa fa-male" aria-hidden="true"></i>
                        <span>Vendor</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['channel-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.channels.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.channels.index') }}">
                        <i class="fa fa-industry" aria-hidden="true"></i>
                        <span>Channels</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['request-code-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.requestcodes.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.requestcodes.index') }}">
                        <i class="fa fa-qrcode" aria-hidden="true"></i>
                        <span>Request Code</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['ssg-code-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.ssgcodes.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.ssgcodes.index') }}">
                        <i class="fa fa-barcode" aria-hidden="true"></i>
                        <span>SSG Code</span>
                    </a>
                </li>
                <li class="{{ Route::currentRouteName() == 'admin.verified-product' ? 'active' : '' }}">
                    <a href="{{ route('admin.verified-product') }}">
                        <i class="fa fa-barcode" aria-hidden="true"></i>
                        <span>Verified product</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['code-print-status-list'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.code-print-status-list.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.code-print-status-list.index') }}">
                        <i class="icon-pencil"></i>
                        <span>Code Print Status Report</span>
                    </a>
                </li>
                @endif
                @if (count(array_intersect(['redeem-list', 'pending-redeem'], $allPermissionNames)) > 0)
                <li class="{{ in_array(Route::currentRouteName(), ['admin.user_point.user_point']) ? 'active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <span>User Point</span>
                    </a>
                    <ul>
                        @if (in_array("redeem-list", $allPermissionNames))
                        <li>
                            <a href="{{ url('admin/user_point?country=1&group_by=technicians.division_id') }}"
                                class="{{ Route::currentRouteName() == 'admin.user_point.user_point' ? 'active-submenu' : '' }}">
                                <span>Point List</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('admin/user_point_monthly') }}"
                                class="{{ Route::currentRouteName() == 'admin.user_point_monthly.user_point_monthly' ? 'active-submenu' : '' }}">
                                <span>Mnthly Point </span>
                            </a>
                        </li>


                        @endif

                    </ul>
                </li>
                @endif
                @if (count(array_intersect(['redeem-list', 'pending-redeem'], $allPermissionNames)) > 0)
                <li
                    class="{{ in_array(Route::currentRouteName(), ['admin.redeem.index', 'admin.redeem.pending_redeem']) ? 'active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <span>Redeem</span>
                    </a>
                    <ul>
                        @if (in_array("redeem-list", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.redeem.index') }}"
                                class="{{ Route::currentRouteName() == 'admin.redeem.index' ? 'active-submenu' : '' }}">
                                <span>Redeem List</span>
                            </a>
                        </li>
                        @endif
                        @if (in_array("pending-redeem", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.redeem.pending_redeem') }}"
                                class="{{ Route::currentRouteName() == 'admin.redeem.pending_redeem' ? 'active-submenu' : '' }}">
                                <span>Pending Redeem</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (count(array_intersect(['approved-technician', 'pending-technician'], $allPermissionNames)) > 0)
                <li class="{{ Route::currentRouteName() == 'admin.users.technician_user' ? 'active' : '' }}">
                    @php
                    $get_status = request()->filled('status') ? request('status') : '';
                    @endphp
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="icon-shield" aria-hidden="true"></i>
                        <span>Technician</span>
                    </a>
                    <ul>
                        @if (in_array("approved-technician", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.users.technician_user', 'status=1') }}"
                                class="{{ $get_status == '1' ? 'active-submenu' : '' }}">
                                <span>Approved Technician</span>
                            </a>
                        </li>
                        @endif
                        @if (in_array("pending-technician", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.users.technician_user', 'status=0') }}"
                                class="{{ $get_status == '0' ? 'active-submenu' : '' }}">
                                <span>Pending Technician</span>
                            </a>
                        </li>
                        @endif
                        @if (in_array("pending-technician", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.users.technician_user', 'status=2') }}"
                                class="{{ $get_status == '0' ? 'active-submenu' : '' }}">
                                <span>Hold Technician</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                @endif
                @if (count(array_intersect(['campaign-list', 'campaign-category'], $allPermissionNames)) > 0)
                <li class="">
                    <a href="{{ route('admin.code-print-status-list.index') }}" class="has-arrow">
                        <i class="icon-trophy"></i>
                        <span>Campaign</span>
                    </a>
                    <ul>
                        @if (in_array("campaign-list", $allPermissionNames))
                        <li>
                            <a href="{{ route('campaigns.index') }}">
                                <span>Campaign List</span>
                            </a>
                        </li>
                        @endif
                        @if (in_array("campaign-category", $allPermissionNames))
                        <li>
                            <a href="{{ route('campaignCategories.index') }}">
                                <span>Campaign Category</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (in_array("settings", $allPermissionNames))
                <li class="{{ Route::currentRouteName() == 'admin.settings.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="fa fa-cubes" aria-hidden="true"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif

                @if (in_array("learning-and-tutorial-list", $allPermissionNames))
                <li class="{{ Route::currentRouteName() == 'admin.learnings.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.learnings.index') }}">
                        <i class="fa fa-cubes" aria-hidden="true"></i>
                        <span>Learnings</span>
                    </a>
                </li>
                @endif

                @if (in_array("offer-list", $allPermissionNames))
                <li class="{{ Route::currentRouteName() == 'admin.offers.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.offers.index') }}">
                        <i class="fa fa-cubes" aria-hidden="true"></i>
                        <span>Offers</span>
                    </a>
                </li>
                @endif

                @if (count(array_intersect(['gift-policies', 'gifts'], $allPermissionNames)) > 0)
                <li class="{{ in_array(Route::currentRouteName(), ['admin.gift-policies.index', 'admin.gift-policies.create', 'admin.gift-policies.edit', 'admin.gifts.index', 'admin.gifts.create', 'admin.gifts.edit']) ? 'active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-gift" aria-hidden="true"></i>
                        <span>Gift Management</span>
                    </a>
                    <ul>
                        @if (in_array("gift-policies", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.gift-policies.index') }}"
                                class="{{ request()->routeIs('admin.gift-policies.*') ? 'active-submenu' : '' }}">
                                <span>Gift Policies</span>
                            </a>
                        </li>
                        @endif
                        @if (in_array("gifts", $allPermissionNames))
                        <li>
                            <a href="{{ route('admin.gifts.index') }}"
                                class="{{ request()->routeIs('admin.gifts.*') ? 'active-submenu' : '' }}">
                                <span>Gifts</span>
                            </a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ route('admin.gift.transactions.index') }}"
                                class="{{ request()->routeIs('admin.gift.transactions.*') ? 'active-submenu' : '' }}">
                                <span>Gift Transactions</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (count(array_intersect(['gift-policies', 'gifts'], $allPermissionNames)) > 0)
                <li class="{{ in_array(Route::currentRouteName(), ['admin.lottery.index' ]) ? 'active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-gift" aria-hidden="true"></i>
                        <span>Lottery Management</span>
                    </a>
                    <ul>

                        <li>
                            <a href="{{ route('admin.gift-policies.index') }}"
                                class="{{ request()->routeIs('admin.gift-policies.*') ? 'active-submenu' : '' }}">
                                <span>Lottery Gifts </span>
                            </a>
                        </li>


                        <li>
                            <a href="{{ route('admin.lotteries.index') }}"
                                class="{{ request()->routeIs('admin.lotteries.*') ? 'active-submenu' : '' }}">
                                <span>Lotteries</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.gift.transactions.index') }}"
                                class="{{ request()->routeIs('admin.gift.transactions.*') ? 'active-submenu' : '' }}">
                                <span>Lottery Winners </span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (in_array("sms-send", $allPermissionNames))
                <li class="has-sub {{ request()->routeIs('admin.sms.*') ? 'active' : '' }}">
                    <a href="javascript:;">
                        <i class="fa fa-envelope"></i>
                        <span>SMS Management</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="{{ Route::currentRouteName() == 'admin.sms.index' ? 'active' : '' }}">
                            <a href="{{ route('admin.sms.index') }}">Send SMS</a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'admin.sms.logs' ? 'active' : '' }}">
                            <a href="{{ route('admin.sms.logs') }}">SMS Logs</a>
                        </li>
                    </ul>
                </li>

                @endif
                @if (in_array("feedback", $allPermissionNames))
                <li class="{{ Route::currentRouteName() == 'admin.feedback.index' ? 'active' : '' }}">
                    <a href="{{ route('admin.feedback.index') }}">
                        <i class="fa fa-cubes" aria-hidden="true"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                @endif

                @if (in_array("notifications", $allPermissionNames))
                <li class="{{ Route::currentRouteName() == 'admin.feedback.index' ? 'active' : '' }}">
                    <a href="{{ route('notifications.index') }}">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                @endif




                <li>
                    <a href="#">
                        <span> </span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>


@endsection