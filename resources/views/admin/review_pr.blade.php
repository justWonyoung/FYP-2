@extends('layouts.app')

@section('content')

<div class="container-fluid" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <span class="text-muted small">
                Admin / Purchase Request Final Approval
            </span>

            <h4 class="fw-bold m-0">
                Review {{ $pr->request_no }}
            </h4>
        </div>


        <a
            href="{{ route('admin.dashboard') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>



    <div class="card stat-card p-4">


        <h5 class="fw-bold mb-4">

            <i class="bi bi-shield-check me-2"></i>

            Admin Final Review for {{ $pr->request_no }}

        </h5>



        <div class="bg-light p-4 rounded mb-4">


            <div class="row g-3">


                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Customer Order
                    </small>

                    <strong>
                        {{ $pr->customer_order_no }}
                    </strong>

                </div>



                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Supplier
                    </small>

                    <strong>
                        {{ $pr->supplier_name }}
                    </strong>

                </div>



                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Material / Item
                    </small>

                    <strong>
                        {{ $pr->material_item }}
                    </strong>

                </div>



                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Quantity
                    </small>

                    <strong>
                        {{ number_format($pr->quantity,2) }}
                        {{ $pr->unit }}
                    </strong>

                </div>



                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Finance Status
                    </small>

                    <span class="badge bg-info text-dark">
                        {{ ucfirst($pr->finance_status) }}
                    </span>

                </div>



                <div class="col-md-6">

                    <small class="text-muted d-block">
                        Admin Approval Status
                    </small>

                    <span class="badge bg-warning text-dark">
                        {{ ucfirst($pr->approval_status) }}
                    </span>

                </div>



            </div>



            <hr>



            <div>

                <small class="text-muted d-block">
                    Estimated Purchase Cost
                </small>


                <h3 class="text-primary fw-bold mb-0">

                    RM {{ number_format($pr->estimated_cost,2) }}

                </h3>

            </div>


        </div>




        <div class="alert alert-info">

            <i class="bi bi-info-circle me-1"></i>

            If approved, this Purchase Request will update inventory
            and complete the approval process.

        </div>





        <div class="d-flex justify-content-end gap-2">


            <!-- Reject -->

            <form
                action="{{ route('admin.purchase.reject', $pr->getKey()) }}"
                method="POST"
                onsubmit="return confirm('Are you sure you want to reject this Purchase Request?');"
            >

                @csrf


                <button
                    type="submit"
                    class="btn btn-outline-danger px-4"
                >

                    <i class="bi bi-x-circle me-1"></i>

                    Reject

                </button>


            </form>





            <!-- Final Approve -->


            <form
                action="{{ route('admin.purchase.approve', $pr->getKey()) }}"
                method="POST"
                onsubmit="return confirm('Final approve this Purchase Request? This will update inventory.');"
            >

                @csrf


                <button
                    type="submit"
                    class="btn btn-success px-4"
                >

                    <i class="bi bi-check-circle me-1"></i>

                    Final Approve

                </button>


            </form>



        </div>



    </div>


</div>


@endsection