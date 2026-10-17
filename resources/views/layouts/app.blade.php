<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">


<title>
{{ $pageTitle ?? 'DBFM DIGITAL MANAGEMENT SYSTEM' }}
</title>


<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="{{ asset('css/app.css') }}?v=shared-ui-u6a" rel="stylesheet">


</head>


<body class="{{ Auth::check() ? 'app-shell' : 'auth-shell' }}">


@auth


<div class="dashboard-wrapper">



<!-- ================= SIDEBAR ================= -->


<aside class="sidebar" aria-label="Primary navigation">



<a href="{{ route(Auth::user()->role.'.dashboard') }}" class="sidebar-brand">


<div class="brand-title" aria-label="DBFM">

DBFM

</div>


<small>

OPERATIONAL SYSTEM

</small>


</a>





<div class="sidebar-menu-title">

MAIN

</div>





<ul class="nav flex-column">





<!-- ================= ADMIN MENU ================= -->


@if(Auth::user()->role == 'admin')



<li>

<a href="{{ route('admin.dashboard') }}"
class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
Dashboard

</a>

</li>





<li>

<a href="{{ route('admin.users.index') }}"
class="nav-link {{ request()->routeIs('admin.users.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 20v-1.5A3.5 3.5 0 0 0 12.5 15h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="7" r="3"/><path d="M17 8a3 3 0 0 1 0 6M20 20v-1.5A3.5 3.5 0 0 0 17.5 15"/></svg>
User Management

</a>

</li>



@endif







@if(Auth::user()->role === 'staff')
<li>
<a href="{{ route('staff.dashboard') }}"
class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active':'' }}">
<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
Dashboard
</a>
</li>
@endif

<!-- ================= OPERATION MODULE ================= -->


@if(in_array(Auth::user()->role,['admin','staff']))



<li>

<a href="{{ route('inventory.index') }}"
class="nav-link {{ request()->routeIs('inventory.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.8 7.5 4.2 7.5-4.2M12 12v9"/></svg>
Inventory

</a>

</li>





<li>

<a href="{{ route('production.index') }}"
class="nav-link {{ request()->routeIs('production.index') || request()->routeIs('production.report') || request()->routeIs('production.create') || request()->routeIs('production.log') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20h14M7 20V9l5-5 5 5v11M10 13h4M10 17h4"/></svg>
Production

</a>

</li>

@if(Auth::user()->role === 'staff')
<li>

<a href="{{ route('production.capacity.index') }}"
class="nav-link {{ request()->routeIs('production.capacity.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15v-3M12 15V8M16 15v-5"/></svg>
Production Capacity

</a>

</li>

<li>

<a href="{{ route('production.schedule-recommendations.index') }}"
class="nav-link {{ request()->routeIs('production.schedule-recommendations.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16M4 12h10M4 19h7"/><path d="m17 15 3 3-3 3"/></svg>
Scheduling Recommendation

</a>

</li>
@endif



@endif


@if(Auth::user()->role === 'staff')

<li>

<a href="{{ route('staff.orders.index') }}"
class="nav-link {{ request()->routeIs('staff.orders.*') || request()->routeIs('staff.customers.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg>
Customer Orders

</a>

</li>


<li>

<a href="{{ route(Auth::user()->role.'.suppliers.index') }}"
class="nav-link {{ request()->routeIs('admin.suppliers.*') || request()->routeIs('staff.suppliers.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V9l4-4h8l4 4v11M8 20v-5h8v5M9 9h6"/></svg>
Supplier Management

</a>

</li>


@endif







<!-- ================= FINANCE MODULE ================= -->


@if(Auth::user()->role === 'finance')



<li>

<a href="{{ route('finance.dashboard') }}"
class="nav-link {{ request()->routeIs('finance.dashboard') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15v-3M12 15V8M16 15v-5"/></svg>
Dashboard

</a>

</li>

<li>

<a href="{{ route('finance.expenses') }}"
class="nav-link {{ request()->routeIs('finance.expenses*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h.01M17 15h.01M9 12h6"/></svg>
Expense Management

</a>

</li>

<li>

<a href="{{ route('finance.report') }}"
class="nav-link {{ request()->routeIs('finance.report') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h10l4 4v14H5zM15 3v5h5M8 13h8M8 17h6"/></svg>
Financial Report

</a>

</li>



@endif







<!-- ================= ADMIN ANALYTICS ================= -->


@if(Auth::user()->role == 'admin')



<li>

<a href="{{ route('admin.reports') }}"
class="nav-link {{ request()->routeIs('admin.reports') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15v-3M12 15V8M16 15v-5"/></svg>
Analytics

</a>

</li>

<li>

<a href="{{ route('admin.activity-logs.index') }}"
class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active':'' }}">

<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5"/></svg>
Activity Log

</a>

</li>



@endif





</ul>



</aside>









<!-- ================= MAIN CONTENT ================= -->


<div class="main-content">







<!-- ================= TOP NAVBAR ================= -->


<header class="top-navbar">



<div>


<div class="top-navbar-title">
<span class="top-navbar-crumb">{{ ucfirst(Auth::user()->role) }}</span>
<span class="top-navbar-separator">/</span>
{{ $moduleTitle ?? 'Dashboard' }}
</div>





@if(isset($moduleSubtitle))


<div class="top-navbar-subtitle">

{{ $moduleSubtitle }}

</div>


@endif



</div>










<div class="d-flex align-items-center gap-3">



<span class="role-badge role-badge-{{ Auth::user()->role }}">

{{ ucfirst(Auth::user()->role) }}

</span>






<form action="{{ route('logout.custom') }}" method="POST">

@csrf


<button class="btn btn-outline-danger btn-sm px-3">

Logout

</button>


</form>





</div>





</header>









<!-- ================= PAGE CONTENT ================= -->


<main class="page-container">


@yield('content')


</main>






</div>






</div>






@else




<div class="auth-page-shell">
    <div class="auth-glow auth-glow-one" aria-hidden="true"></div>
    <div class="auth-glow auth-glow-two" aria-hidden="true"></div>
    <div class="auth-content">
        @yield('content')
    </div>
</div>




@endauth







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')




</body>


</html>
