    <div class="startbar d-print-none">
            <!--start brand-->
            <div class="brand">
                <a href="" class="logo">
                    <span>
                        <img src="assets/images/logo-sm.png" alt="logo-small" class="logo-sm">
                    </span>
                    <span class="">
                        <img src="assets/images/logo-light.png" alt="logo-large" class="logo-lg logo-light">
                        <img src="assets/images/logo-dark.png" alt="logo-large" class="logo-lg logo-dark">
                    </span>
                </a>
            </div>
            <!--end brand-->

            <div class="startbar-menu" >
                <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                    <div class="d-flex align-items-start flex-column w-100">
                        <!-- Navigation -->
                        <ul class="navbar-nav mb-auto w-100">
                            <li class="menu-label mt-2">
                                <span>Main</span>
                            </li>

                              
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="iconoir-report-columns menu-icon"></i>
                                    <span>Dashboard</span>
                                    <span class="badge text-bg-info ms-auto">New</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('add-new') }}">
                                    <i class="iconoir-report-columns menu-icon"></i>
                                    <span>Dashboard</span>
                                    <span class="badge text-bg-info ms-auto">New</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('departments.index') ? 'active' : '' }}" 
                                href="{{ route('departments.index') }}">
                                    <i class="iconoir-building menu-icon"></i>
                                    <span>Departments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" 
                                href="{{ route('items.index') }}">
                                    <i class="iconoir-box menu-icon"></i>
                                    <span>Items</span>
                                </a>
                            </li>

                            <!-- Material Requisitions (collapsible with routes) -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('requisitions.*') ? '' : 'collapsed' }}" 
                                href="#sidebarRequisitions" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ request()->routeIs('requisitions.*') ? 'true' : 'false' }}" 
                                aria-controls="sidebarRequisitions">
                                    <i class="iconoir-task-list menu-icon"></i>
                                    <span>Material Requisitions</span>
                                </a>
                                <div class="collapse {{ request()->routeIs('requisitions.*') ? 'show' : '' }}" id="sidebarRequisitions">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('requisitions.add-new') ? 'active' : '' }}" 
                                            href="{{ route('requisitions.add-new') }}">Add New</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('requisitions.index') ? 'active' : '' }}" 
                                            href="{{ route('requisitions.index') }}">Saved Requisitions</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lpos.*') ? '' : 'collapsed' }}" 
                                href="#sidebarLpos" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ request()->routeIs('lpos.*') ? 'true' : 'false' }}" 
                                aria-controls="sidebarLpos">
                                    <i class="iconoir-shopping-bag menu-icon"></i>
                                    <span>Local Purchase Orders</span>
                                </a>
                                <div class="collapse {{ request()->routeIs('lpos.*') ? 'show' : '' }}" id="sidebarLpos">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('lpos.create') ? 'active' : '' }}" 
                                            href="{{ route('lpos.create') }}">Add New</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('lpos.index') ? 'active' : '' }}" 
                                            href="{{ route('lpos.index') }}">Saved LPOs</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('grns.*') ? '' : 'collapsed' }}" 
                                href="#sidebarGRNs" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ request()->routeIs('grns.*') ? 'true' : 'false' }}" 
                                aria-controls="sidebarGRNs">
                                    <i class="iconoir-receipt menu-icon"></i>
                                    <span>GRNs</span>
                                </a>
                                <div class="collapse {{ request()->routeIs('grns.*') ? 'show' : '' }}" id="sidebarGRNs">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('grns.create') ? 'active' : '' }}" 
                                            href="{{ route('grns.create') }}">Add New</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('grns.index') ? 'active' : '' }}" 
                                            href="{{ route('grns.index') }}">All GRNs</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>






                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="payment.html">-->
                            <!--        <i class="iconoir-hand-cash menu-icon"></i>-->
                            <!--        <span>Payment</span>-->
                            <!--    </a>-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarTransactions" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarTransactions">-->
                            <!--        <i class="iconoir-task-list menu-icon"></i>-->
                            <!--        <span>Transactions</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarTransactions">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="transactions.html">Overview</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="new-transaction.html">Add Transactions</a>-->
                            <!--            </li> -->
                            <!--        </ul>  -->
                            <!--    </div>   
                            </li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="cards.html">-->
                            <!--        <i class="iconoir-credit-cards menu-icon"></i>-->
                            <!--        <span>Cards</span>-->
                            <!--        <span class="badge text-bg-pink ms-auto">03</span>-->
                            <!--    </a>-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="taxes.html">-->
                            <!--        <i class="iconoir-plug-type-l menu-icon"></i>-->
                            <!--        <span>Texes</span>-->
                            <!--    </a>-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="users.html">-->
                            <!--        <i class="iconoir-group menu-icon"></i>-->
                            <!--        <span>Users</span>-->
                            <!--    </a>-->
                            <!--</li> -->
                        
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="apps-chat.html">-->
                            <!--        <i class="iconoir-chat-bubble menu-icon"></i> -->
                            <!--        <span>Chat</span>-->
                            <!--    </a>-->
                            <!--</li>  -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="apps-contact-list.html">-->
                            <!--        <i class="iconoir-community menu-icon"></i> -->
                            <!--        <span>Contact List</span>-->
                            <!--    </a>-->
                            <!--</li>  -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="apps-calendar.html">-->
                            <!--        <i class="iconoir-calendar menu-icon"></i> -->
                            <!--        <span>Calendar</span>-->
                            <!--    </a>-->
                            <!--</li>   -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="apps-invoice.html">-->
                            <!--        <i class="iconoir-paste-clipboard menu-icon"></i> -->
                            <!--        <span>Invoice</span>-->
                            <!--    </a>-->
                            <!--</li> -->
                    
                            <!--<li class="menu-label mt-2">-->
                            <!--    <small class="label-border">-->
                            <!--        <div class="border_left hidden-xs"></div>-->
                            <!--        <div class="border_right"></div>-->
                            <!--    </small>-->
                            <!--    <span>Components</span>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarElements" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarElements">-->
                            <!--        <i class="iconoir-compact-disc menu-icon"></i>-->
                            <!--        <span>UI Elements</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarElements">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-alerts.html">Alerts</a>-->
                            <!--            </li>  -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-avatar.html">Avatar</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-buttons.html">Buttons</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-badges.html">Badges</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-cards.html">Cards</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-carousels.html">Carousels</a>-->
                            <!--            </li>                                 -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-dropdowns.html">Dropdowns</a>-->
                            <!--            </li>                                    -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-grids.html">Grids</a>-->
                            <!--            </li>                                 -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-images.html">Images</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-list.html">List</a>-->
                            <!--            </li>                                    -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-modals.html">Modals</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-navs.html">Navs</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-navbar.html">Navbar</a>-->
                            <!--            </li>  -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-paginations.html">Paginations</a>-->
                            <!--            </li>    -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-popover-tooltips.html">Popover & Tooltips</a>-->
                            <!--            </li>                                 -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-progress.html">Progress</a>-->
                            <!--            </li>                                 -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-spinners.html">Spinners</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-tabs-accordions.html">Tabs & Accordions</a>-->
                            <!--            </li>                                -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-typography.html">Typography</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="ui-videos.html">Videos</a>-->
                            <!--            </li>  -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarElements-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarAdvancedUI" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarAdvancedUI">-->
                            <!--        <i class="iconoir-peace-hand menu-icon"></i>-->
                            <!--        <span>Advanced UI</span><span class="badge rounded text-success bg-success-subtle ms-1">New</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarAdvancedUI">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-animation.html">Animation</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-clipboard.html">Clip Board</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-dragula.html">Dragula</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-files.html">File Manager</a>-->
                            <!--            </li>  -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-highlight.html">Highlight</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-rangeslider.html">Range Slider</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-ratings.html">Ratings</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-ribbons.html">Ribbons</a>-->
                            <!--            </li>                                   -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-sweetalerts.html">Sweet Alerts</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="advanced-toasts.html">Toasts</a>-->
                            <!--            </li> -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarAdvancedUI-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarForms" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarForms">-->
                            <!--        <i class="iconoir-cube-hole menu-icon"></i>-->
                            <!--        <span>Forms</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarForms">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-elements.html">Basic Elements</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-advanced.html">Advance Elements</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-validation.html">Validation</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-wizard.html">Wizard</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-editors.html">Editors</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-uploads.html">File Upload</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="forms-img-crop.html">Image Crop</a>-->
                            <!--            </li> -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarForms-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarCharts" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarCharts">-->
                            <!--        <i class="iconoir-candlestick-chart menu-icon"></i>-->
                            <!--        <span>Charts</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarCharts">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="charts-apex.html">Apex</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="charts-justgage.html">JustGage</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="charts-chartjs.html">Chartjs</a>-->
                            <!--            </li>  -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="charts-toast-ui.html">Toast</a>-->
                            <!--            </li>  -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarCharts-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarTables" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarTables">-->
                            <!--        <i class="iconoir-list menu-icon"></i>-->
                            <!--        <span>Tables</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarTables">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="tables-basic.html">Basic</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="tables-datatable.html">Datatables</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="tables-editable.html">Editable</a>-->
                            <!--            </li>  -->
                            <!--        </ul>  -->
                            <!--    </div>   
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarIcons" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarIcons">-->
                            <!--        <i class="iconoir-fire-flame menu-icon"></i>-->
                            <!--        <span>Icons</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarIcons">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="icons-fontawesome.html">Font Awesome</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="icons-lineawesome.html">Line Awesome</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="icons-icofont.html">Icofont</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="icons-iconoir.html">Iconoir</a>-->
                            <!--            </li> -->
                            <!--        </ul>  -->
                            <!--    </div>   
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarMaps" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarMaps">-->
                            <!--        <i class="iconoir-map-pin menu-icon"></i>-->
                            <!--        <span>Maps</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarMaps">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="maps-google.html">Google Maps</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="maps-leaflet.html">Leaflet Maps</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="maps-vector.html">Vector Maps</a>-->
                            <!--            </li>  -->
                            <!--        </ul>  -->
                            <!--    </div>  
                            </li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarEmailTemplates" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarEmailTemplates">-->
                            <!--        <i class="iconoir-send-mail menu-icon"></i>-->
                            <!--        <span>Email Templates</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarEmailTemplates">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="email-templates-basic.html">Basic Action Email</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="email-templates-alert.html">Alert Email</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="email-templates-billing.html">Billing Email</a>-->
                            <!--            </li>   -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarEmailTemplates-->
                            <!--</li> -->
                            <!--<li class="menu-label mt-2">-->
                            <!--    <small class="label-border">-->
                            <!--        <div class="border_left hidden-xs"></div>-->
                            <!--        <div class="border_right"></div>-->
                            <!--    </small>-->
                            <!--    <span>Crafted</span>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarPages" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarPages">-->
                            <!--        <i class="iconoir-page-star menu-icon"></i>-->
                            <!--        <span>Pages</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarPages">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-profile.html">Profile</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-notifications.html">Notifications</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-timeline.html">Timeline</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-treeview.html">Treeview</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-starter.html">Starter Page</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-pricing.html">Pricing</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-blogs.html">Blogs</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-faq.html">FAQs</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="pages-gallery.html">Gallery</a>-->
                            <!--            </li>   -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarPages-->
                            <!--</li> -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="#sidebarAuthentication" data-bs-toggle="collapse" role="button"-->
                            <!--        aria-expanded="false" aria-controls="sidebarAuthentication">-->
                            <!--        <i class="iconoir-fingerprint-lock-circle menu-icon"></i>-->
                            <!--        <span>Authentication</span>-->
                            <!--    </a>-->
                            <!--    <div class="collapse " id="sidebarAuthentication">-->
                            <!--        <ul class="nav flex-column">-->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-login.html">Log in</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-register.html">Register</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-recover-pw.html">Re-Password</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-lock-screen.html">Lock Screen</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-maintenance.html">Maintenance</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-404.html">Error 404</a>-->
                            <!--            </li> -->
                            <!--            <li class="nav-item">-->
                            <!--                <a class="nav-link" href="auth-500.html">Error 500</a>-->
                            <!--            </li> -->
                            <!--        </ul>  -->
                            <!--    </div><!--end startbarAuthentication-->
                            <!--</li> -->
                        </ul><!--end navbar-nav--->
                        <!--<div class="update-msg text-center"> -->
                        <!--    <div class="d-flex justify-content-center align-items-center thumb-lg update-icon-box  rounded-circle mx-auto">-->
                                <!-- <i class="iconoir-peace-hand h3 align-self-center mb-0 text-primary"></i> -->
                        <!--         <img src="assets/images/extra/gold.png" alt="" class="" height="45">-->
                        <!--    </div>                   -->
                        <!--    <h5 class="mt-3">Today's <span class="text-white">$2450.00</span></h5>-->
                        <!--    <p class="mb-3 text-muted">Today's best Investment for you.</p>-->
                        <!--    <a href="javascript: void(0);" class="btn text-primary shadow-sm rounded-pill px-3">Invest Now</a>-->
                        <!--</div>-->
                    </div>
                </div><!--end startbar-collapse-->
                
            </div><!--end startbar-menu-->    
        </div><!--end startbar-->
        <div class="startbar-overlay d-print-none"></div>
        <!-- end leftbar-tab-menu-->
        
