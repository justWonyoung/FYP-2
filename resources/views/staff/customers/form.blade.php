@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="section-card">
    <div class="section-title mb-4">Customer Information</div>
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST') @method($method) @endif
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label" for="customer_name">Customer Name</label><input id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name', $customer?->customer_name) }}" required></div>
            <div class="col-md-6"><label class="form-label" for="phone">Phone Number</label><input id="phone" name="phone" class="form-control" value="{{ old('phone', $customer?->phone) }}"></div>
            <div class="col-md-6"><label class="form-label" for="email">Email</label><input id="email" type="email" name="email" class="form-control" value="{{ old('email', $customer?->email) }}"></div>
            <div class="col-md-6"><label class="form-label" for="address">Address</label><textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $customer?->address) }}</textarea></div>
        </div>
        <div class="mt-4"><button class="btn btn-primary">{{ $method === 'POST' ? 'Create Customer' : 'Update Customer' }}</button><a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a></div>
    </form>
</div>
