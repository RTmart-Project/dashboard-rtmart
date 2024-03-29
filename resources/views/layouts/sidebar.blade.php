<!-- Brand Logo -->
<a href="#" class="brand-link">
    <img src="{{ url('/') }}/dist/img/rtmart_logo.png" alt="RTmart" class="brand-image ml-2" style="opacity: .8">
    <span class="brand-text font-weight-light">
        Dashboard
    </span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Home
                    </p>
                </a>
            </li>
            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'SM')
            <li class="nav-item">
                <a href="{{ route('priceSubmission') }}"
                    class="nav-link {{ Request::is('price-submission*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>
                        Price Submission
                    </p>
                </a>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'SM' ||
            Auth::user()->RoleID == 'SV')
            <li class="nav-item {{ Request::is('summary*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('summary*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        Summary
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('summary.summary') }}"
                            class="nav-link {{ Request::is('summary/finance*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('summary/financ*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Summary Finance</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'SV')
                    <li class="nav-item">
                        <a href="{{ route('summary.report') }}"
                            class="nav-link {{ Request::is('summary/report*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('summary/report*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Summary Report</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'SV')
                    <li class="nav-item">
                        <a href="{{ route('summary.merchant') }}"
                            class="nav-link {{ Request::is('summary/merchant*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('summary/mercha*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Summary Merchant</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'SV')
                    <li class="nav-item">
                        <a href="{{ route('summary.margin') }}"
                            class="nav-link {{ Request::is('summary/margin*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('summary/margin*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Summary Margin</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'AD' ||
            Auth::user()->RoleID == 'RBTAD' ||
            (Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO') ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'DMO' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'DRV' ||
            Auth::user()->RoleID == 'SM' ||
            Auth::user()->RoleID == 'SV')
            <li class="nav-item {{ Request::is('distribution*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('distribution*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>
                        Distribution
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'AH' || Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('distribution.validationRestock') }}"
                            class="nav-link {{ Request::is('distribution/validation*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/validation*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Validasi Restock</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('distribution.restock') }}"
                            class="nav-link {{ Request::is('distribution/restock*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/restock*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Restock Distributor</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'DRV' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('distribution.billPayLater') }}"
                            class="nav-link {{ Request::is('distribution/bill*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/bill*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Tagihan RTpaylater</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('distribution.settlement') }}"
                            class="nav-link {{ Request::is('distribution/settlement*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/settlement*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Settlement</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'SV')
                    <li class="nav-item">
                        <a href="{{ route('distribution.product') }}"
                            class="nav-link {{ Request::is('distribution/product*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/product*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Produk</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('distribution.merchant') }}"
                            class="nav-link {{ Request::is('distribution/merchant*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('distribution/merchant*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Merchant Grade</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Delivery --}}
            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'AD' ||
            Auth::user()->RoleID == 'RBTAD' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'DRV' ||
            Auth::user()->RoleID == 'SM')
            <li class="nav-item {{ Request::is('delivery*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('delivery*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-truck"></i>
                    <p>
                        Delivery
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'HL')
                    <li class="nav-item">
                        <a href="{{ route('delivery.request') }}"
                            class="nav-link {{ Request::is('delivery/request*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('delivery/request*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Delivery Plan</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'DRV' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('delivery.expedition') }}"
                            class="nav-link {{ Request::is('delivery/on-going*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('delivery/on-going*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Delivery On Going</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('delivery.history') }}"
                            class="nav-link {{ Request::is('delivery/history*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('delivery/history*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Delivery History</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{-- Delivery --}}

            {{-- Master Product --}}
            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'RBTAD' ||
            Auth::user()->RoleID == 'DMO')
            <li class="nav-item {{ Request::is('master/product*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('master/product*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-layer-group"></i>
                    <p> Master Product
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('product.list') }}"
                            class="nav-link {{ Request::is('*product/list*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*product/list*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Product List</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('product.category') }}"
                            class="nav-link {{ Request::is('*product/category*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*product/category*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Product Categpry</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('product.uom') }}"
                            class="nav-link {{ Request::is('*product/uom*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*product/uom*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Product UOM</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('product.type') }}"
                            class="nav-link {{ Request::is('*product/type*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*product/type*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Product Type</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('product.brand') }}"
                            class="nav-link {{ Request::is('*product/brand*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*product/brand*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Brand</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{-- Master Product --}}

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'DMO')
            <li class="nav-item {{ Request::is('ppob*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('ppob*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    <p>
                        PPOB
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('ppob.topup') }}"
                            class="nav-link {{ Request::is('ppob/topup*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Topup</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ppob.transaction') }}"
                            class="nav-link {{ Request::is('ppob/transaction*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'HR' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'DMO')
            <li class="nav-item {{ Request::is('distributor*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('distributor*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-store-alt"></i>
                    <p>
                        Distributor
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('distributor.account') }}"
                            class="nav-link {{ Request::is('distributor/account*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Akun</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'HR' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'DMO')
            <li class="nav-item {{ Request::is('supplier*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('supplier*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-store-alt"></i>
                    <p>
                        Supplier
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('supplier.account') }}"
                            class="nav-link {{ Request::is('supplier/account*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Akun</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'FI')
            <li class="nav-item">
                <a href="{{ route('monthlyReport') }}"
                    class="nav-link {{ Request::is('monthly-report*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-invoice"></i>
                    <p>Monthly Report</p>
                </a>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'AD' ||
            Auth::user()->RoleID == 'INVTR' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'SM' ||
            Auth::user()->RoleID == 'SV')
            <li class="nav-item {{ Request::is('stock*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('stock*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cubes"></i>
                    <p>
                        Stock
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'INVTR' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'SV')
                    <li class="nav-item">
                        <a href="{{ route('stock.listStock') }}"
                            class="nav-link {{ Request::is('stock/list*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('stock/list*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>List Stock</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL')
                    <li class="nav-item">
                        <a href="{{ route('stock.purchasePlan') }}"
                            class="nav-link {{ Request::is('stock/plan-purchase*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('stock/plan-purchase*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Purchase Plan</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'INVTR' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL')
                    <li class="nav-item">
                        <a href="{{ route('stock.purchase') }}"
                            class="nav-link {{ Request::is('stock/purchase*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('stock/purchase*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Purchase Stock</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'INVTR' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL')
                    <li class="nav-item">
                        <a href="{{ route('stock.mutation') }}"
                            class="nav-link {{ Request::is('stock/mutation*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('stock/mutation*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Mutasi</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'INVTR' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'HL')
                    <li class="nav-item">
                        <a href="{{ route('stock.opname') }}"
                            class="nav-link {{ Request::is('stock/opname*') ? 'active' : '' }}">
                            <i class="far nav-icon {{ Request::is('stock/opname*') ? 'fa-dot-circle' : 'fa-circle' }}"></i>
                            <p>Stock Opname</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'RBTAD' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'HR' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'DMO' ||
            Auth::user()->RoleID == 'HL' ||
            Auth::user()->RoleID == 'SM' ||
            Auth::user()->RoleID == 'SKR' ||
            Auth::user()->RoleID == 'AD')
            <li class="nav-item {{ Request::is('merchant*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('merchant*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-store"></i>
                    <p>
                        Merchant
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'HR' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO' ||
                    Auth::user()->RoleID == 'HL' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'AD')
                    <li class="nav-item">
                        <a href="{{ route('merchant.account') }}"
                            class="nav-link {{ Request::is('merchant/account*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/account*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Akun</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'SKR' ||
                    Auth::user()->RoleID == 'AH')
                    <li class="nav-item {{ Request::is('merchant/membership*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('merchant/membership*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/membership*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Master Membership
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (Auth::user()->RoleID == 'IT' ||
                            Auth::user()->RoleID == 'BM' ||
                            Auth::user()->RoleID == 'SM' ||
                            Auth::user()->RoleID == 'CEO' ||
                            Auth::user()->RoleID == 'FI' ||
                            Auth::user()->RoleID == 'AH')
                            <li class="nav-item">
                                <a href="{{ route('merchant.membership') }}"
                                    class="nav-link {{ Request::is('merchant/membership') ? 'active' : '' }}">
                                        <i class="far {{ Request::is('merchant/membership') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Membership</p>
                                </a>
                            </li>
                            @endif
                            @if (Auth::user()->RoleID == 'IT' ||
                            Auth::user()->RoleID == 'BM' ||
                            Auth::user()->RoleID == 'SM' ||
                            Auth::user()->RoleID == 'CEO' ||
                            Auth::user()->RoleID == 'FI' ||
                            Auth::user()->RoleID == 'SKR' ||
                            Auth::user()->RoleID == 'AH')
                            <li class="nav-item">
                                <a href="{{ url('merchant/membership/kospin') }}"
                                    class="nav-link {{ Request::is('merchant/membership/kospin') ? 'active' : '' }}">
                                        <i class="far {{ Request::is('merchant/membership/kospin') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Kospin Sekartama</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'AH')
                    <li class="nav-item">
                        <a href="{{ route('merchant.assessment') }}"
                            class="nav-link {{ Request::is('merchant/assessment*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/assessment*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Assessment</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'SM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'AH')
                    <li class="nav-item">
                        <a href="{{ route('merchant.powermerchant') }}"
                            class="nav-link {{ Request::is('merchant/powermerchant*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/powermerchant*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Akun Power Merchant</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('merchant.otp') }}"
                            class="nav-link {{ Request::is('merchant/otp*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/otp*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>OTP</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'HR' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO' ||
                    Auth::user()->RoleID == 'AD' ||
                    Auth::user()->RoleID == 'SM')
                    <li class="nav-item">
                        <a href="{{ route('merchant.restock') }}"
                            class="nav-link {{ Request::is('merchant/restock*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('merchant/restock*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Restock</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- RT Sales --}}
            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'DMO' ||
            Auth::user()->RoleID == 'SM' ||
            Auth::user()->RoleID == 'SV')
            <li class="nav-item {{ Request::is('rtsales*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('rtsales*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>
                        RT Sales
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('rtsales.surveyReport') }}"
                            class="nav-link {{ Request::is('rtsales/surveyreport*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('rtsales/surveyreport*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Survey Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rtsales.callPlanIndex') }}"
                            class="nav-link {{ Request::is('rtsales/callplan*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('rtsales/callplan*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Call Plan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rtsales.callReport') }}"
                            class="nav-link {{ Request::is('rtsales/callreport*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('rtsales/callreport*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Call Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rtsales.storeList') }}"
                            class="nav-link {{ Request::is('rtsales/store*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('rtsales/store*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Store List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rtsales.saleslist') }}"
                            class="nav-link {{ Request::is('rtsales/saleslist*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('rtsales/saleslist*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Sales List</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            {{-- RT Sales --}}

            {{-- Customer --}}
            @if (Auth::user()->RoleID == 'IT' ||
            Auth::user()->RoleID == 'RBTAD' ||
            Auth::user()->RoleID == 'BM' ||
            Auth::user()->RoleID == 'CEO' ||
            Auth::user()->RoleID == 'FI' ||
            Auth::user()->RoleID == 'HR' ||
            Auth::user()->RoleID == 'AH' ||
            Auth::user()->RoleID == 'DMO')
            <li class="nav-item {{ Request::is('customer*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('customer*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Customer
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'HR' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('customer.account') }}"
                            class="nav-link {{ Request::is('customer/account*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('customer/account*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Akun</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'HR' ||
                    Auth::user()->RoleID == 'AH')
                    <li class="nav-item">
                        <a href="{{ route('customer.otp') }}"
                            class="nav-link {{ Request::is('customer/otp*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('customer/otp*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>OTP</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' ||
                    Auth::user()->RoleID == 'RBTAD' ||
                    Auth::user()->RoleID == 'BM' ||
                    Auth::user()->RoleID == 'CEO' ||
                    Auth::user()->RoleID == 'FI' ||
                    Auth::user()->RoleID == 'HR' ||
                    Auth::user()->RoleID == 'AH' ||
                    Auth::user()->RoleID == 'DMO')
                    <li class="nav-item">
                        <a href="{{ route('customer.transaction') }}"
                            class="nav-link {{ Request::is('customer/transaction*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('customer/transaction*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{-- Customer --}}

            {{-- Banner --}}
            @if (Auth::user()->RoleID == 'IT')
            <li class="nav-item {{ Request::is('banner*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('banner*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-flag"></i>
                    <p>
                        Banner
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('banner.slider') }}"
                            class="nav-link {{ Request::is('banner/slider*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('banner/slider*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Banner Slider</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            {{-- Banner --}}

            {{-- Voucher --}}
            @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'RBTAD')
            <li class="nav-item {{ Request::is('voucher*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('voucher*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p>
                        Voucher
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('voucher.list') }}"
                            class="nav-link {{ Request::is('voucher/list*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('voucher/list*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Voucher List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('voucher.log') }}"
                            class="nav-link {{ Request::is('voucher/log*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('voucher/log*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Penggunaan Voucher</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            {{-- Voucher --}}

            @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO')
            <li class="nav-item {{ Request::is('setting*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('setting*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tools"></i>
                    <p>
                        Pengaturan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (Auth::user()->RoleID == 'IT')
                    <li class="nav-item">
                        <a href="{{ route('setting.monthlyReport') }}"
                            class="nav-link {{ Request::is('setting/monthly-report*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('setting/monthly-report*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Monthly Report</p>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO')
                    <li class="nav-item {{ Request::is('*setting/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*setting/*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('*setting/*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Pengguna
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO')
                            <li class="nav-item">
                                <a href="{{ route('setting.users') }}"
                                    class="nav-link {{ Request::is('*setting/users*') ? 'active' : '' }}">
                                    <i class="far {{ Request::is('*setting/users*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Daftar Pengguna</p>
                                </a>
                            </li>
                            @endif
                            @if (Auth::user()->RoleID == 'IT')
                            <li class="nav-item">
                                <a href="{{ route('setting.role') }}"
                                    class="nav-link {{ Request::is('*setting/role*') ? 'active' : '' }}">
                                    <i class="far {{ Request::is('setting/role*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Role</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO')
                    <li class="nav-item {{ Request::is('setting/module*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('setting/module*') ? 'active' : '' }}">
                            <i class="far {{ Request::is('setting/module*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                            <p>Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (Auth::user()->RoleID == 'IT' || Auth::user()->RoleID == 'BM' || Auth::user()->RoleID == 'CEO')
                            <li class="nav-item">
                                <a href="{{ route('setting.fairbanc') }}"
                                    class="nav-link {{ Request::is('*module/fairbanc*') ? 'active' : '' }}">
                                    <i class="far {{ Request::is('*module/fairbanc*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Fairbanc</p>
                                </a>
                            </li>
                            @endif
                            @if (Auth::user()->RoleID == 'IT')
                            <li class="nav-item">
                                <a href="{{ route('setting.haistar') }}"
                                    class="nav-link {{ Request::is('*module/haistar*') ? 'active' : '' }}">
                                    <i class="far {{ Request::is('*module/haistar*') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Haistar</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif --}}
                </ul>
            </li>
            @endif
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
