<div class="sidebar">
    <div class="menu-item {{ request()->is('farmer/dashboard') ? 'active' : '' }}">
        <i class="fa fa-tachometer-alt"></i> Dashboard
    </div>
    <div class="menu-item {{ request()->is('overview*') ? 'active-green' : '' }}">
        <i class="fa fa-chart-pie"></i> Overview
    </div>
    <div class="menu-item {{ request()->is('recommendation*') ? '' : '' }}">
        <i class="fa fa-thumbs-up"></i> Recommendation
    </div>
    <div class="menu-item {{ request()->is('report*') ? '' : '' }}">
        <i class="fa fa-file-alt"></i> Report
    </div>
</div>
