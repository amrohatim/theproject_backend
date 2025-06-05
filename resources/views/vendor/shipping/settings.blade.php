@extends('layouts.vendor')

@section('title', 'Shipping Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Shipping Settings</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('vendor.shipping.update-settings') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="can_deliver" name="can_deliver" value="1" {{ $company->can_deliver ? 'checked' : '' }}>
                                <label class="custom-control-label" for="can_deliver">My business can handle its own deliveries</label>
                            </div>
                            <small class="form-text text-muted">
                                If enabled, you will be responsible for delivering orders to customers. 
                                If disabled, orders will be shipped via Aramex courier service.
                            </small>
                        </div>

                        <div class="delivery-options {{ $company->can_deliver ? '' : 'd-none' }}" id="delivery-options">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Delivery Responsibilities:</h6>
                                <ul class="mb-0">
                                    <li>You are responsible for delivering products to customers in a timely manner</li>
                                    <li>You must update the shipping status when you process, ship, and deliver orders</li>
                                    <li>You are responsible for any delivery issues or delays</li>
                                    <li>Customers will contact you directly regarding delivery inquiries</li>
                                </ul>
                            </div>
                        </div>

                        <div class="aramex-options {{ $company->can_deliver ? 'd-none' : '' }}" id="aramex-options">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Aramex Courier Service:</h6>
                                <ul class="mb-0">
                                    <li>Orders will be shipped via Aramex courier service</li>
                                    <li>Shipping costs will be calculated based on package weight and dimensions</li>
                                    <li>Customers will receive tracking information automatically</li>
                                    <li>You must prepare packages for pickup by Aramex</li>
                                </ul>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canDeliverCheckbox = document.getElementById('can_deliver');
        const deliveryOptions = document.getElementById('delivery-options');
        const aramexOptions = document.getElementById('aramex-options');

        canDeliverCheckbox.addEventListener('change', function() {
            if (this.checked) {
                deliveryOptions.classList.remove('d-none');
                aramexOptions.classList.add('d-none');
            } else {
                deliveryOptions.classList.add('d-none');
                aramexOptions.classList.remove('d-none');
            }
        });
    });
</script>
@endsection
