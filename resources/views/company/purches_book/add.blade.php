@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Purchase Book</span>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card mb-4">
                <h5 class="card-header">Add Purchase</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input class="form-control" type="date" value="" id="date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor" class="form-label">Vendor</label>
                            <select class="form-select" id="vendor">
                              <option selected>Select</option>
                              @foreach ( $vendors as $vendor)
                                  <option value="{{ $vendor->id }}">{{ $vendor->full_name }}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transport" class="form-label">Transport</label>
                            <input type="text" class="form-control" id="transport">
                        </div>
                    </div>
                </div>
                <h5 class="card-header">Add Item</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="item" class="form-label">Item</label>
                            <select class="form-select" id="item">
                                <option selected>Select</option>
                                @foreach ( $items as $item)
                                    <option value="{{ $item->id }}" data-tax="{{ $item->tax_rate }}" data-variation="{{ $item->variation_name }}">{{ $item->name }}</option>
                                @endforeach
                              </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="qty" class="form-label">Quantity</label>
                            <input type="text" class="form-control" id="qty">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" class="form-control" id="amount">
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-primary" id="addItem">Add Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>

</script>
@endsection
