<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            {{-- <ul class="sidebar-vertical">
                @if($menus)
                @foreach ($menus as $menu)
                    <li class="{{ $menu->url == '#' ? 'submenu' : '' }}">
                        <a href="{{ $menu->url == '#' ? $menu->url : route($menu->route)  }}"><i class="{{ $menu->icon }}"></i> <span> {{ $menu->name }}</span>
                        @if($menu->url == '#') 
                            <span class="menu-arrow"></span>
                        @endif
                        </a>
                        @if ($menu->children->isNotEmpty())
                            <ul>
                                @foreach ($menu->children as $child)
                                    <li><a class="{{Route::is($child->route) ? 'active' : ''}}" href="{{route($child->route)}}">{{ $child->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
                @else 
                
                @endif
            </ul> --}}
            <ul class="sidebar-vertical">
                <!-- Dashboard -->
                <li class="">
                    <a href="{{route('admin.dashboard') }}"><i class="la la-cube"></i> <span>Dashboard</span>
                    </a>
                </li>

                <!-- User -->
                <li class="submenu">
                    <a href="#"><i class="la la-user"></i> <span>User</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{Route::is('admin.users.create') ? 'active' : ''}}" href="{{route('admin.users.create')}}">Add</a>
                        </li>
                        <li><a class="{{Route::is('admin.users.index') ? 'active' : ''}}" href="{{route('admin.users.index')}}">List</a>
                        </li>
                    </ul>
                </li>

                <!-- Plan -->
                <li class="submenu">
                    <a href="#"><i class="la la-tasks"></i> <span>Plan</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{Route::is('admin.plan.create') ? 'active' : ''}}" href="{{route('admin.plan.create')}}">Add</a>
                        </li>
                        <li><a class="{{Route::is('admin.plan.index') ? 'active' : ''}}" href="{{route('admin.plan.index')}}">List</a>
                        </li>
                    </ul>
                </li>

                <!-- Assign Plan -->
                <li class="submenu">
                    <a href="#"><i class="la la-user-check"></i> <span>Assign Plan</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{Route::is('admin.assign-plan.create') ? 'active' : ''}}" href="{{route('admin.assign-plan.create')}}">Add</a>
                        </li>
                        <li><a class="{{Route::is('admin.assign-plan.index') ? 'active' : ''}}" href="{{route('admin.assign-plan.index')}}">List</a>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="submenu">
                    <a href="#"><i class="la la-clipboard-list"></i> <span>Reports</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{Route::is('admin.reports.renewals') ? 'active' : ''}}" href="{{route('admin.reports.renewals')}}">Membership Renewal</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>