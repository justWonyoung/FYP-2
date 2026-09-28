<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Beauty Kasih - Operational Management System
    </title>


    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <link 
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <style>

        body {
            background-color:#f8f9fa;
            font-family:'Segoe UI',
            Tahoma,
            Geneva,
            Verdana,
            sans-serif;
        }


        .sidebar {

            min-height:100vh;
            background-color:#0f172a;
            color:white;

        }


        .sidebar .nav-link {

            color:#94a3b8;
            padding:12px 20px;
            font-weight:500;

        }


        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {

            color:white;
            background-color:#1e293b;
            border-left:4px solid #0ea5e9;

        }


        .sidebar-brand {

            font-size:1.25rem;
            font-weight:bold;
            color:white;
            padding:20px;
            display:block;
            text-decoration:none;
            border-bottom:1px solid #1e293b;

        }


        .top-navbar {

            background:white;
            border-bottom:1px solid #e2e8f0;
            padding:12px 24px;

        }


    </style>


</head>


<body>


<div class="container-fluid">


@auth


<div class="row">


<!-- SIDEBAR -->

<div class="col-md-2 p-0 sidebar">


<a href="#" class="sidebar-brand">

Beauty Kasih

<br>

<small class="text-muted fs-6 font-monospace">
OPERATIONAL SYSTEM
</small>

</a>



<ul class="nav flex-column mt-3">


@if(Auth::user()->role == 'admin')


<li class="nav-item">

<a class="nav-link {{request()->is('admin/dashboard')?'active':''}}"
href="{{route('admin.dashboard')}}">

<i class="bi bi-speedometer2 me-2"></i>

Dashboard

</a>

</li>



<li class="nav-item">

<a class="nav-link {{request()->is('admin/users*')?'active':''}}"
href="{{route('admin.users.index')}}">

<i class="bi bi-people me-2"></i>

User Management

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="{{route('inventory.index')}}">

<i class="bi bi-box-seam me-2"></i>

Inventory

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="{{route('production.index')}}">

<i class="bi bi-gear-wide-connected me-2"></i>

Production

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="{{route('finance.report')}}">

<i class="bi bi-graph-up me-2"></i>

Financial Report

</a>

</li>




@elseif(Auth::user()->role == 'staff')



<li class="nav-item">

<a class="nav-link"
href="{{route('staff.dashboard')}}">

<i class="bi bi-speedometer2 me-2"></i>

Dashboard

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="{{route('staff.pr.create')}}">

<i class="bi bi-file-earmark-plus me-2"></i>

Create Purchase Request

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="{{route('inventory.index')}}">

<i class="bi bi-box-seam me-2"></i>

Inventory

</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="{{route('production.index')}}">

<i class="bi bi-gear-wide-connected me-2"></i>

Production

</a>

</li>




@elseif(Auth::user()->role == 'finance')



<li class="nav-item">

<a class="nav-link"
href="{{route('finance.dashboard')}}">

<i class="bi bi-speedometer2 me-2"></i>

Dashboard

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="{{route('finance.expenses')}}">

<i class="bi bi-receipt me-2"></i>

Expenses

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="{{route('finance.report')}}">

<i class="bi bi-graph-up me-2"></i>

Financial Report

</a>

</li>




<li class="nav-item">

<a class="nav-link"
href="{{route('inventory.index')}}">

<i class="bi bi-box-seam me-2"></i>

Inventory

</a>

</li>



@endif


</ul>


</div>




<!-- MAIN CONTENT -->


<div class="col-md-10 p-0">



<div class="top-navbar d-flex justify-content-between align-items-center">


<h5 class="m-0 fw-bold">

Dashboard

<small class="text-muted fs-6">

/ Overview

</small>

</h5>



<div class="d-flex align-items-center gap-3">


<span class="badge bg-primary px-3 py-2">

Role:
{{ucfirst(Auth::user()->role)}}

</span>



<form action="{{ route('logout.custom') }}" method="POST">

@csrf

<button type="submit" class="btn btn-outline-danger btn-sm">

<i class="bi bi-box-arrow-right"></i>

Logout

</button>

</form>


</div>


</div>




<div class="p-4">

@yield('content')

</div>



</div>



</div>



@else


<div class="p-4">

@yield('content')

</div>


@endauth



</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>