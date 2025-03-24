<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">

                @php
                    $user = auth()->user();
                    $isSuperAdmin = $user->roles()->where('name', 'Super Admin')->exists();
                    $isGymManager = $user->roles()->where('name', 'Gym')->exists();
                @endphp

                <!-- Dashboard -->
                <li class="">
                    <a href="{{route('admin.dashboard') }}"><i class="la la-cube"></i> <span>Dashboard</span>
                    </a>
                </li>

                @if(!$isGymManager)
                <!-- Gym Manager -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="la la-user"></i> <span>Gym Manager</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.gym.create') ? 'active' : ''}}" href="{{route('admin.gym.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.gym.index') ? 'active' : ''}}" href="{{route('admin.gym.index')}}">List</a>
                        </li>
                    </ul>
                </li>
                @endif
            
            @if(!$isSuperAdmin)
                <!-- User -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="la la-user"></i> <span>User</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.users.create') ? 'active' : ''}}" href="{{route('admin.users.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.users.index') ? 'active' : ''}}" href="{{route('admin.users.index')}}">List</a>
                        </li>
                    </ul>
                </li>

                <!-- Plan -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="la la-tasks"></i> <span>Plan</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.plan.create') ? 'active' : ''}}" href="{{route('admin.plan.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.plan.index') ? 'active' : ''}}" href="{{route('admin.plan.index')}}">List</a>
                        </li>
                    </ul>
                </li>

                <!-- Assign Plan -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="la la-user-check"></i> <span>Assign Plan</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.assign-plan.create') ? 'active' : ''}}" href="{{route('admin.assign-plan.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.assign-plan.index') ? 'active' : ''}}" href="{{route('admin.assign-plan.index')}}">List</a>
                    </ul>
                </li>

                <!-- Workout -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="las la-dumbbell"></i> <span>Workout</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.workout.create') ? 'active' : ''}}" href="{{route('admin.workout.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.workout.index') ? 'active' : ''}}" href="{{route('admin.workout.index')}}">List</a>
                    </ul>
                </li>

                <!-- Diet Plan -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="las la-apple-alt"></i> <span>Diet Plan</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.diet-plan.create') ? 'active' : ''}}" href="{{route('admin.diet-plan.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.diet-plan.index') ? 'active' : ''}}" href="{{route('admin.diet-plan.index')}}">List</a>
                    </ul>
                </li>

                <!-- Attendance -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="las la-calendar"></i> <span>Attendance</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.attendance.create') ? 'active' : ''}}" href="{{route('admin.attendance.create')}}">Add</a>
                        </li>
                        <li class="px-3"><a class="{{Route::is('admin.attendance.index') ? 'active' : ''}}" href="{{route('admin.attendance.index')}}">List</a>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="la la-clipboard-list"></i> <span>Reports</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.reports.renewals') ? 'active' : ''}}" href="{{route('admin.reports.renewals')}}">Membership Renewal</a>
                    <li class="px-3"><a class="{{Route::is('admin.reports.expired') ? 'active' : ''}}" href="{{route('admin.reports.expired')}}">Membership Expired</a>
                        </li>
                    </ul>
                </li>

                <!-- Notification -->
                <li class="submenu">
                    <a href="javacript:void(0);"><i class="las la-bell"></i> <span>Notification</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li class="px-3"><a class="{{Route::is('admin.notifications.form') ? 'active' : ''}}" href="{{route('admin.notifications.form')}}">Send Notification</a>
                    </ul>
                </li>
            @endif
            </ul>
        </div>
    </div>
</div>