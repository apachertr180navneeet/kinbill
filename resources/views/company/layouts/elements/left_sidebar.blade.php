<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('company.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
            <a href="{{ route('company.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        {{--  Master Menu  --}}
        <li class="menu-item {{ request()->routeIs('company.variation.*') || request()->routeIs('company.tax.*') || request()->routeIs('company.item.*') || request()->routeIs('company.vendor.*') || request()->routeIs('company.customer.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div class="text-truncate" data-i18n="Master">Master</div>
            </a>
            <ul class="menu-sub">
                @foreach([
                    ['route' => 'company.variation.index', 'text' => 'Variation'],
                    ['route' => 'company.tax.index', 'text' => 'Tax'],
                    ['route' => 'company.item.index', 'text' => 'Item'],
                    ['route' => 'company.vendor.index', 'text' => 'Vendor'],
                    ['route' => 'company.customer.index', 'text' => 'Customer'],
                ] as $mastermenu)
                    <li class="menu-item {{ request()->routeIs($mastermenu['route']) ? 'active' : '' }}">
                        <a href="{{ route($mastermenu['route']) }}" class="menu-link">
                            <i class="menu-icon tf-icons"></i>
                            <div data-i18n="{{ $mastermenu['text'] }}">{{ $mastermenu['text'] }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        {{--  Process menu  --}}
        <li class="menu-item {{ request()->routeIs(['company.purches.book.*', 'company.sales.book.*', 'company.receipt.book.voucher.*', 'company.payment.book.*']) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div class="text-truncate" data-i18n="Master">Process</div>
            </a>
            <ul class="menu-sub">
                @foreach([
                    ['route' => 'company.purches.book.index', 'text' => 'Purchase Book'],
                    ['route' => 'company.sales.book.index', 'text' => 'Sales Book'],
                    ['route' => 'company.receipt.book.voucher.index', 'text' => 'Receipt Book Voucher'],
                    ['route' => 'company.payment.book.index', 'text' => 'Payment Book'],
                ] as $processmenu)
                    <li class="menu-item {{ request()->routeIs($processmenu['route']) ? 'active' : '' }}">
                        <a href="{{ route($processmenu['route']) }}" class="menu-link">
                            <i class="menu-icon tf-icons"></i>
                            <div data-i18n="{{ $processmenu['text'] }}">{{ $processmenu['text'] }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

         {{--  Report menu  --}}
         <li class="menu-item {{ request()->routeIs(['company.purches.report.*', 'company.sales.report.*']) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div class="text-truncate" data-i18n="Master">Report</div>
            </a>
            <ul class="menu-sub">
                @foreach([
                    ['route' => 'company.purches.report.index', 'text' => 'Purchase Reprt'],
                    ['route' => 'company.sales.report.index', 'text' => 'Sales Reprt'],
                ] as $reportmenu)
                    <li class="menu-item {{ request()->routeIs($reportmenu['route']) ? 'active' : '' }}">
                        <a href="{{ route($reportmenu['route']) }}" class="menu-link">
                            <i class="menu-icon tf-icons"></i>
                            <div data-i18n="{{ $reportmenu['text'] }}">{{ $reportmenu['text'] }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>




    </ul>
</aside>
