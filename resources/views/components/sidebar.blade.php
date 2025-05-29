<div class="sidebar">
    <div class="menu-item {{ request()->is('farmer/dashboard') ? 'active' : '' }}">
        <i class="fa fa-tachometer-alt"></i> Dashboard
    </div>

    <a href="{{ route('farmer.soil') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('farmer.soil*') ? 'active' : '' }}">
            <i class="fas fa-seedling"></i> Soil Management
        </div>
    </a>

    <div class="menu-item {{ request()->is('report*') ? '' : '' }}">
        <i class="fa fa-file-alt"></i> Report
    </div>
</div>
