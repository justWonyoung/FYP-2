<!DOCTYPE html>
<html>

<head>

<title>
DBFM DIGITAL MANAGEMENT SYSTEM
</title>


<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>

body{

    background:#f8fafc;

    font-family:Inter, sans-serif;

}


.login-card{

    max-width:450px;

    margin:auto;

    margin-top:120px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 10px 30px rgba(0,0,0,.08);

}


.logo{

    font-size:28px;

    font-weight:800;

    color:#7c3aed;

}


</style>


</head>


<body>



<div class="container">


<div class="login-card text-center">


<div class="logo mb-3">

DBFM

</div>



<h5 class="mb-3">

Management System

</h5>



<p class="text-muted">

Business management platform

</p>



<a href="{{ route('login') }}" 
class="btn btn-primary px-5 mt-3">


Login


</a>



</div>


</div>



</body>


</html>