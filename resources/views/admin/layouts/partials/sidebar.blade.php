<style>
    .sidebar .sidebar-menu ul li a .menu-arrow::before,
.two-col-bar .sidebar-menu ul li a .menu-arrow::before {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900; /* for solid style */
    content: "\f105";
}
</style>

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
                    <a href="{{ route('admin.dashboard') }}"><i class="la la-cube"></i> <span>Dashboard</span>
                    </a>
                </li>

                @if (!$isGymManager)
                    <!-- Gym Manager -->
                    <li class="submenu">
                        <a href="javacript:void(0);"><i class="la la-user"></i> <span>Gym Manager</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.gym.create') ? 'active' : '' }}"
                                    href="{{ route('admin.gym.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.gym.index') ? 'active' : '' }}"
                                    href="{{ route('admin.gym.index') }}">List</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Gym Listing Manager -->
                    <li class="submenu">
                        <a href="javacript:void(0);"><i class="la la-user"></i> <span>Gym Listing Manager</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.gym.gymlist') ? 'active' : '' }}"
                                    href="{{ route('admin.gym.gymlist') }}">List</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (!$isSuperAdmin)
                    <!-- User -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="fas fa-users"></i> <span>User</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.users.create') ? 'active' : '' }}"
                                    href="{{ route('admin.users.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.users.index') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">List</a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="fas fa-user-tie"></i> <span>Trainers</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.trainers.create') ? 'active' : '' }}"
                                    href="{{ route('admin.trainers.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.trainers.index') ? 'active' : '' }}"
                                    href="{{ route('admin.trainers.index') }}">List</a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="la la-chalkboard-teacher"></i> <span>Assign PT</span>

                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.assign-pt.create') ? 'active' : '' }}"
                                    href="{{ route('admin.assign-pt.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.assign-pt.index') ? 'active' : '' }}"
                                    href="{{ route('admin.assign-pt.index') }}">List</a>
                        </ul>
                    </li>
                    <!-- Videos -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="la la-video"></i> <span>Videos</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Video Categories -->
                            <li class="submenu">
                                <a href="javascript:void(0);"> <span>Category</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li class="px-3"><a class="{{ Route::is('admin.category.create') ? 'active' : '' }}"
                                            href="{{ route('admin.category.create') }}">Add</a></li>
                                    <li class="px-3"><a class="{{ Route::is('admin.category.index') ? 'active' : '' }}"
                                            href="{{ route('admin.category.index') }}">List</a></li>
                                </ul>
                            </li>

                            <!-- Video Add & List -->
                            <li class="px-3"><a class="{{ Route::is('admin.video.create') ? 'active' : '' }}"
                                    href="{{ route('admin.video.create') }}">Add</a></li>
                            <li class="px-3"><a class="{{ Route::is('admin.video.index') ? 'active' : '' }}"
                                    href="{{ route('admin.video.index') }}">List</a></li>
                        </ul>
                    </li>


                    <!-- Plan -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="la la-tasks"></i> <span>Plan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.plan.create') ? 'active' : '' }}"
                                    href="{{ route('admin.plan.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.plan.index') ? 'active' : '' }}"
                                    href="{{ route('admin.plan.index') }}">List</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Assign Plan -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="la la-user-plus"></i> <span>Assign Plan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.assign-plan.create') ? 'active' : '' }}"
                                    href="{{ route('admin.assign-plan.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.assign-plan.index') ? 'active' : '' }}"
                                    href="{{ route('admin.assign-plan.index') }}">List</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Reports -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="la la-chart-bar"></i> <span>Reports</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.reports.renewals') ? 'active' : '' }}"
                                    href="{{ route('admin.reports.renewals') }}">Membership Renewal</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.reports.expired') ? 'active' : '' }}"
                                    href="{{ route('admin.reports.expired') }}">Membership Expired</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.reports.pt') ? 'active' : '' }}"
                                    href="{{ route('admin.reports.pt') }}">Personal Training</a>
                            </li>
                        </ul>
                    </li>


                    <!-- Workout -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="fas fa-dumbbell"></i> <span>Workout</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.workout.create') ? 'active' : '' }}"
                                    href="{{ route('admin.workout.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.workout.index') ? 'active' : '' }}"
                                    href="{{ route('admin.workout.index') }}">List</a>
                        </ul>
                    </li>

                    <!-- Diet Plan -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="las la-apple-alt"></i> <span>Diet Plan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.diet-plan.create') ? 'active' : '' }}"
                                    href="{{ route('admin.diet-plan.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.diet-plan.index') ? 'active' : '' }}"
                                    href="{{ route('admin.diet-plan.index') }}">List</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Equipment -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="las la-dumbbell"></i> <span>Equipment</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.equipment.create') ? 'active' : '' }}"
                                    href="{{ route('admin.equipment.create') }}">Add</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.equipment.index') ? 'active' : '' }}"
                                    href="{{ route('admin.equipment.index') }}">List</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Activity -->
                    <li class="submenu">
                        <a href="javascript:void(0);"><i class="las la-running"></i> <span>Activity</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3"><a class="{{ Route::is('admin.activity.create') ? 'active' : '' }}"
                                    href="{{ route('admin.activity.create') }}">Package</a>
                            </li>
                            <li class="px-3"><a class="{{ Route::is('admin.activity-assign') ? 'active' : '' }}"
                                    href="{{ route('admin.activity-assign-list') }}">Assign</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Transactions -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <i class="fa-solid fa-money-check-dollar"></i> <span>Transactions</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li class="px-3">
                                <a class="{{ Route::is('admin.transactions.index') ? 'active' : '' }}"
                                href="{{ route('admin.transactions.index') }}">List</a>
                            </li>
                        </ul>
                    </li>


                @endif
            </ul>
        </div>
    </div>
</div>
