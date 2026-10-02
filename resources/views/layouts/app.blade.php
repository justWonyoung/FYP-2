<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">


<title>
{{ $pageTitle ?? 'Beauty Kasih Management System' }}
</title>


<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="{{ asset('css/app.css') }}" rel="stylesheet">


</head>


<body>


@auth


<div class="dashboard-wrapper">



<!-- ================= SIDEBAR ================= -->


<aside class="sidebar">



<a href="{{ route(Auth::user()->role.'.dashboard') }}" class="sidebar-brand">


<div class="brand-title">

Beauty Kasih

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

Dashboard

</a>

</li>





<li>

<a href="{{ route('admin.users.index') }}"
class="nav-link {{ request()->routeIs('admin.users.*') ? 'active':'' }}">

User Management

</a>

</li>



@endif







<!-- ================= OPERATION MODULE ================= -->


@if(in_array(Auth::user()->role,['admin','staff','finance']))



<li>

<a href="{{ route('inventory.index') }}"
class="nav-link {{ request()->routeIs('inventory.*') ? 'active':'' }}">

Inventory

</a>

</li>





<li>

<a href="{{ route('production.index') }}"
class="nav-link {{ request()->routeIs('production.*') ? 'active':'' }}">

Production

</a>

</li>



@endif







<!-- ================= FINANCE MODULE ================= -->


@if(in_array(Auth::user()->role,['admin','finance']))



<li>

<a href="{{ route('finance.expenses') }}"
class="nav-link {{ request()->routeIs('finance.*') ? 'active':'' }}">

Finance

</a>

</li>



@endif







<!-- ================= ADMIN ANALYTICS ================= -->


@if(Auth::user()->role == 'admin')



<li>

<a href="{{ route('admin.reports') }}"
class="nav-link {{ request()->routeIs('admin.reports') ? 'active':'' }}">

Analytics

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

{{ $moduleTitle ?? 'Dashboard' }}

</div>





@if(isset($moduleSubtitle))


<div class="top-navbar-subtitle">

{{ $moduleSubtitle }}

</div>


@endif



</div>










<div class="d-flex align-items-center gap-3">



<span class="role-badge">

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




@yield('content')




@endauth







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




</body>


</html>