@extends('layouts.app')


@php

$pageTitle = "User Management | DBFM";
$moduleTitle = "User Management";
$moduleSubtitle = "Staff Account & Access Control";

@endphp



@section('content')


<div class="d-flex justify-content-end mb-4">

    <a href="{{ route('admin.users.create') }}"
   class="btn btn-primary">

        <i class="bi bi-person-plus me-1"></i>

        Add New User

    </a>

</div>




<div class="section-card">


    <div class="section-title mb-4">

        System Users

    </div>




    <div class="table-responsive">


        <table class="table align-middle">


            <thead>

                <tr>

                    <th>
                        NAME & USERNAME
                    </th>


                    <th>
                        EMAIL
                    </th>


                    <th>
                        PHONE
                    </th>


                    <th>
                        ROLE
                    </th>


                    <th>
                        STATUS
                    </th>


                    <th>
                        ACTION
                    </th>


                </tr>

            </thead>




            <tbody>


            @forelse($users as $user)


                <tr>


                    <td>


                        <strong>

                            {{ $user->full_name }}

                        </strong>


                        <br>


                        <small class="text-muted">

    {{ '@'.$user->username }}

</small>


                    </td>





                    <td>

                        {{ $user->email }}

                    </td>





                    <td>

                        {{ $user->phone_number ?? '-' }}

                    </td>





                    <td>


                        @if($user->role == 'admin')


                            <span class="badge bg-primary">

                                Admin

                            </span>


                        @elseif($user->role == 'finance')


                            <span class="badge bg-warning text-dark">

                                Finance

                            </span>


                        @else


                            <span class="badge bg-info text-dark">

                                Staff

                            </span>


                        @endif



                    </td>






                    <td>


                        @if($user->status == 'active')


                            <span class="badge bg-success">

                                Active

                            </span>


                        @else


                            <span class="badge bg-danger">

                                Inactive

                            </span>


                        @endif


                    </td>







                    <td>


                        <a href="{{ route('admin.users.edit',$user->user_id) }}"
class="btn btn-sm btn-outline-secondary">

Edit

</a>



                        @if($user->role != 'admin')


                        <form method="POST"
action="{{ route('admin.users.destroy',$user->user_id) }}"
class="d-inline"
onsubmit="return confirm('Delete this user account?');">


@csrf

@method('DELETE')


<button type="submit"
class="btn btn-sm btn-outline-danger">

Delete

</button>


</form>


                        @endif


                    </td>



                </tr>



            @empty



                <tr>


                    <td colspan="6"
                        class="text-center text-muted py-5">


                        No users registered.


                    </td>


                </tr>



            @endforelse




            </tbody>


        </table>


    </div>



</div>



@endsection