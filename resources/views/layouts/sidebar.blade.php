    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ url('/') }}/dist/img/rtmart-logo1.png" alt="RTmart"
            class="brand-image" style="opacity: .8">
        <span class="brand-text font-weight-light">RTmart Dashboard</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{url('/')}}/home" class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Home
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('master*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p> Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link {{ Request::is('master/product*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Product
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('product.list') }}" class="nav-link {{ Request::is('*product/list') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Product List</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.category') }}" class="nav-link {{ Request::is('*product/category') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Product Categpry</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.uom') }}" class="nav-link {{ Request::is('*product/uom') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Product UOM</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.type') }}" class="nav-link {{ Request::is('*product/type') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Product Type</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.brand') }}" class="nav-link {{ Request::is('*product/brand') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Brand</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('ppob*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            PPOB
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('/')}}/ppob/topup"
                                class="nav-link {{ Request::is('ppob/topup*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Topup</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/')}}/ppob/transaction"
                                class="nav-link {{ Request::is('ppob/transaction*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transaksi</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('merchant*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Merchant
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('/')}}/merchant/account"
                                class="nav-link {{ Request::is('merchant/account*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Akun</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/')}}/merchant/otp"
                                class="nav-link {{ Request::is('merchant/otp*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>OTP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/')}}/merchant/restock"
                                class="nav-link {{ Request::is('merchant/restock*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Restock</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('customer*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Customer
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('/')}}/customer/account"
                                class="nav-link {{ Request::is('customer/account*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Akun</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/')}}/customer/otp"
                                class="nav-link {{ Request::is('customer/otp*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>OTP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/')}}/customer/transaction"
                                class="nav-link {{ Request::is('customer/transaction*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transaksi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @if (Auth::user()->RoleID == "IT")
                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('setting*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Pengaturan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('/')}}/setting/users"
                                class="nav-link {{ Request::is('setting/users*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengguna</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->