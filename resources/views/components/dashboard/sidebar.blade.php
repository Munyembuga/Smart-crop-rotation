<div class="sidebar">
    <div class="menu-item {{ request()->is('farmer/dashboard') ? 'active' : '' }}">
        <a href="{{ route('farmer.dashboard') }}">Dashboard</a>
    </div>
    <div class="menu-item {{ request()->is('farm*') ? 'active' : '' }}">
        <a href="{{ route('farm') }}">My Farms</a>
    </div>
    <div class="menu-item {{ request()->is('crop-history*') ? 'active' : '' }}">
        <a href="#">Crop History</a>
    </div>
    <div class="menu-item {{ request()->is('rotation-plans*') ? 'active' : '' }}">
        <a href="#">Rotation Plans</a>
    </div>
    <div class="menu-item {{ request()->is('soil-data*') ? 'active' : '' }}">
        <a href="#">Soil Data</a>
    </div>
    <div class="menu-item {{ request()->is('reports*') ? 'active' : '' }}">
        <a href="#">Reports</a>
    </div>
    <div class="menu-item {{ request()->is('settings*') ? 'active' : '' }}">
        <a href="#">Settings</a>
    </div>
</div>
