@extends('company.layouts.app')

@section('style')
<!-- Add any necessary styles here -->
<style>
    .table-borderless td, .table-borderless th {
        border: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Receipt Voucher</span>
            </h5>
        </div>
    </div>
    <form role="form" action="{{ route('company.receipt.book.voucher.update', $receiptBook->id) }}" method="post" id="receipt_voucher" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Receipt Voucher details form -->
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <label for="receipt" class="form-label">Receipt Voucher No.</label>
                                    <input type="text" class="form-control" id="receipt" name="receipt" value="{{ $receiptBook->receipt_vouchers_number }}">
                                </td>
                                <td class="text-end">
                                    <label for="date" class="form-label">Date</label>
                                    <input class="form-control" type="date" id="date" name="date" value="{{ $receiptBook->date }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="customer" class="form-label">Received From:</label>
                                    <select class="form-select" id="customer" name="customer">
                                        <option>Select</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $customer->id == $receiptBook->customer_id ? 'selected' : '' }}>
                                                {{ $customer->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <label for="remark" class="form-label">Remarks</label>
                                    <textarea class="form-control" id="remark" name="remark" rows="2">{{ $receiptBook->remark }}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" value="{{ $receiptBook->amount }}" name="amount">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="discount" class="form-label">Discount</label>
                                    <input type="number" class="form-control" id="discount" name="discount" value="{{ $receiptBook->discount }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="round_off" class="form-label">Round off</label>
                                    <input type="text" class="form-control" id="round_off" name="round_off" value="{{ $receiptBook->round_off }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="grand_total" class="form-label">Net Total</label>
                                    <input type="number" class="form-control" id="grand_total" name="grand_total" value="{{ $receiptBook->grand_total }}" readonly>
                                </td>
                            </tr>
                        </table>
                        <!-- Payment method options -->
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="cash" name="payment_method" value="cash" {{ $receiptBook->payment_type == 'cash' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cash">Cash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="cheque" name="payment_method" value="cheque" {{ $receiptBook->payment_type == 'cheque' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cheque">Cheque</label>payment_type
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="online_bank" name="payment_method" value="online bank" {{ $receiptBook->payment_type == 'online bank' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="online_bank">Online Bank</label>payment_type
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="other" name="payment_method" value="other" {{ $receiptBook->payment_type == 'other' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                </td>
                                <td>
                                    <label for="signature" class="form-label">Signature:</label>
                                    <input type="text" class="form-control" id="signature" name="signature" value="{{ $receiptBook->signature }}">
                                </td>
                            </tr>
                        </table>
                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        function calculateGrandTotal() {
            var amount = parseFloat($('#amount').val()) || 0;
            var discount = parseFloat($('#discount').val()) || 0;
            var round_off = parseFloat($('#round_off').val()) || 0;

            var grand_total = amount - discount + round_off;

            $('#grand_total').val(grand_total.toFixed(2));
        }

        $('#amount, #discount, #round_off').on('keyup', function() {
            calculateGrandTotal();
        });

        // Trigger the calculation on page load
        calculateGrandTotal();
    });
</script>

@endsection
