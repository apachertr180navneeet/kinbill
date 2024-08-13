<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('company.dashboard')}}" class="app-brand-link">
			<span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('company.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>

        <li class="menu-item {{ request()->is('admin/variation') ? 'active' : ''}}">
			<a href="{{route('company.variation.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="variation">Variation</div>
			</a>
		</li>


        <li class="menu-item {{ request()->is('admin/tax') ? 'active' : ''}}">
			<a href="{{route('company.tax.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="tax">Tax</div>
			</a>
		</li>

	</ul>
</aside>
