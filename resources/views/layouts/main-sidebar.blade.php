<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="assets/images/users/user-1.jpg" alt="user-img" title="Mat Helme"
                class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                    data-bs-toggle="dropdown">Geneva Kennedy</a>
                <div class="dropdown-menu user-pro-dropdown">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user me-1"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings me-1"></i>
                        <span>Settings</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock me-1"></i>
                        <span>Lock Screen</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-log-out me-1"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </div>
            <p class="text-muted">Admin Head</p>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title mt-2">The List</li>

                <li>
                    <a href="#sidebarTables" data-bs-toggle="collapse">
                        <i class="mdi mdi-table"></i>
                        <span> Companies </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarTables">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('company.create') }}">Add Company</a>
                            </li>
                            <li>
                                <a href="{{ route('company.index') }}">Companies List</a>
                            </li>
                        </ul>
                    </div>
                </li>



            </ul>
            <ul id="side-menu">

                <li class="menu-title mt-2">The List</li>

                <li>
                    <a href="#sidebarCrm" data-bs-toggle="collapse">
                        <i class="mdi mdi-table"></i>
                        <span> Products </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCrm">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('product.create') }}">Add Product</a>
                            </li>
                            <li>
                                <a href="{{ route('product.index') }}">Products List</a>
                            </li>
                        </ul>
                    </div>
                </li>




            </ul>
            <ul id="side-menu">

                <li class="menu-title mt-2">Invoices</li>

                <li>
                    <a href="#sidebarEmail" data-bs-toggle="collapse">
                        <i class="mdi mdi-table"></i>
                        <span> Invoices </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEmail">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('invoice.create') }}">Add Invoice</a>
                            </li>
                            <li>
                                <a href="{{ route('invoice.index') }}">Invoices List</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarCreditNotes" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-document-outline"></i>
                        <span> Credit Notes </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCreditNotes">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('credit-note.create') }}">Add Credit Note</a>
                            </li>
                            <li>
                                <a href="{{ route('credit-note.index') }}">Credit Notes List</a>
                            </li>
                        </ul>
                    </div>
                </li>



            </ul>
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
