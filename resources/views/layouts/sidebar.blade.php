<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
        <a href="{{ route('dashboard') }}" class="logo">
            <span>
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="logo-small" class="logo-sm">
            </span>
            <span>
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <!--end brand-->

    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">

                    <li class="menu-label mt-2"><span>Main</span></li>

                    {{-- Dashboard --}}
                    @if(hasPermission('dashboard'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="las la-home menu-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endif

                    {{-- Departments --}}
                    @if(hasPermission('departments.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"
                            href="{{ route('departments.index') }}">
                            <i class="las la-building menu-icon"></i>
                            <span>Departments</span>
                        </a>
                    </li>
                    @endif

                    {{-- Items --}}
                    @if(hasPermission('items.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}"
                            href="{{ route('items.index') }}">
                            <i class="las la-cubes menu-icon"></i>
                            <span>Items</span>
                        </a>
                    </li>
                    @endif

                    {{-- Material Requisitions --}}
                    @if(hasPermission('requisitions.index') || hasPermission('requisitions.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('requisitions.*') ? '' : 'collapsed' }}"
                            href="#sidebarRequisitions" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('requisitions.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarRequisitions">
                            <i class="las la-tasks menu-icon"></i>
                            <span>Material Requisitions</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('requisitions.*') ? 'show' : '' }}"
                            id="sidebarRequisitions">
                            <ul class="nav flex-column">
                                @if(hasPermission('requisitions.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('requisitions.create') ? 'active' : '' }}"
                                        href="{{ route('requisitions.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('requisitions.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('requisitions.index') ? 'active' : '' }}"
                                        href="{{ route('requisitions.index') }}">All Requisitions</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- LPOs --}}
                    @if(hasPermission('lpos.index') || hasPermission('lpos.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('lpos.*') ? '' : 'collapsed' }}" href="#sidebarLpos"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('lpos.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarLpos">
                            <i class="las la-file-invoice-dollar menu-icon"></i>
                            <span>Local Purchase Orders</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('lpos.*') ? 'show' : '' }}" id="sidebarLpos">
                            <ul class="nav flex-column">
                                @if(hasPermission('lpos.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('lpos.create') ? 'active' : '' }}"
                                        href="{{ route('lpos.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('lpos.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('lpos.index') ? 'active' : '' }}"
                                        href="{{ route('lpos.index') }}">All LPOs</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- GRNs --}}
                    @if(hasPermission('grns.index') || hasPermission('grns.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('grns.*') ? '' : 'collapsed' }}" href="#sidebarGrns"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('grns.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarGrns">
                            <i class="las la-clipboard-check menu-icon"></i>
                            <span>GRNs</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('grns.*') ? 'show' : '' }}" id="sidebarGrns">
                            <ul class="nav flex-column">
                                @if(hasPermission('grns.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('grns.create') ? 'active' : '' }}"
                                        href="{{ route('grns.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('grns.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('grns.index') ? 'active' : '' }}"
                                        href="{{ route('grns.index') }}">All GRNs</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- SIFs --}}
                    @if(hasPermission('sifs.index') || hasPermission('sifs.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sifs.*') ? '' : 'collapsed' }}" href="#sidebarSifs"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('sifs.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarSifs">
                            <i class="las la-dolly menu-icon"></i>
                            <span>Stock Issuance Forms</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('sifs.*') ? 'show' : '' }}" id="sidebarSifs">
                            <ul class="nav flex-column">
                                @if(hasPermission('sifs.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('sifs.create') ? 'active' : '' }}"
                                        href="{{ route('sifs.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('sifs.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('sifs.index') ? 'active' : '' }}"
                                        href="{{ route('sifs.index') }}">All SIFs</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- Work Orders --}}
                    @if(hasPermission('workorders.index') || hasPermission('workorders.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('workorders.*') ? '' : 'collapsed' }}"
                            href="#sidebarWorkOrders" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('workorders.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarWorkOrders">
                            <i class="las la-briefcase menu-icon"></i>
                            <span>Work Orders</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('workorders.*') ? 'show' : '' }}"
                            id="sidebarWorkOrders">
                            <ul class="nav flex-column">
                                @if(hasPermission('workorders.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('workorders.create') ? 'active' : '' }}"
                                        href="{{ route('workorders.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('workorders.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('workorders.index') ? 'active' : '' }}"
                                        href="{{ route('workorders.index') }}">Saved Work Orders</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- Quotations --}}
                    @if(hasPermission('quotations.index') || hasPermission('quotations.create'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('quotations.*') ? '' : 'collapsed' }}"
                            href="#sidebarQuotations" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('quotations.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarQuotations">
                            <i class="las la-file-invoice-dollar menu-icon"></i>
                            <span>Quotations</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('quotations.*') ? 'show' : '' }}"
                            id="sidebarQuotations">
                            <ul class="nav flex-column">
                                @if(hasPermission('quotations.create'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('quotations.create') ? 'active' : '' }}"
                                        href="{{ route('quotations.create') }}">Add New</a>
                                </li>
                                @endif
                                @if(hasPermission('quotations.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('quotations.index') ? 'active' : '' }}"
                                        href="{{ route('quotations.index') }}">Saved Quotations</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- Roles (Only GM) --}}
                    @if(auth()->user() && auth()->user()->role === 'GM')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">
                            <i class="las la-user-shield menu-icon"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    @endif
                    {{-- User Management --}}
                    @if(hasPermission('users.index') || hasPermission('users.create') ||
                    hasPermission('user.roles.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') || request()->routeIs('user.roles.*') ? '' : 'collapsed' }}"
                            href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('users.*') || request()->routeIs('user.roles.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarUsers">
                            <i class="las la-user-cog menu-icon"></i>
                            <span>User Management</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('users.*') || request()->routeIs('user.roles.*') ? 'show' : '' }}"
                            id="sidebarUsers">
                            <ul class="nav flex-column">

                                {{-- Users --}}
                                @if(hasPermission('users.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}">Users</a>
                                </li>
                                @endif

                           

                                {{-- User Roles / Designations --}}
                                @if(hasPermission('user-roles.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user-roles.index') ? 'active' : '' }}"
                                        href="{{ route('user-roles.index') }}">User Designations</a>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                    @endif


                </ul>
            </div>
        </div>
    </div>
</div>
<div class="startbar-overlay d-print-none"></div>