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
    <form role="form" action="{{ route('company.receipt.book.voucher.store') }}" method="post" id="receipt_voucher" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Receipt Voucher details form -->
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <label for="receipt" class="form-label">Receipt Voucher No.</label>
                                    <input type="text" class="form-control @error('receipt') is-invalid @enderror" id="receipt" name="receipt" value="{{ old('receipt',$finalInvoiceNumber) }}" readonly>
                                    @error('receipt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-end">
                                    <label for="date" class="form-label">Date</label>
                                    <input class="form-control @error('date') is-invalid @enderror" type="date" id="date" name="date" value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="customer" class="form-label">Received From:</label>
                                    <select class="form-select @error('customer') is-invalid @enderror" id="customer" name="customer">
                                        <option value="">Select</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                data-salesbook-amount="{{ $salesbooks->where('customer_id', $customer->id)->sum('grand_total') }}"
                                                data-salesbook-received="{{ $salesbooks->where('customer_id', $customer->id)->sum('recived_amount') }}"
                                                 {{ old('customer') == $customer->id ? 'selected' : '' }}>
                                                 {{ $customer->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                </td>
                                <td>
                                    <label for="remark" class="form-label">Remarks</label>
                                    <textarea class="form-control @error('remark') is-invalid @enderror" id="remark" name="remark" rows="2">{{ old('remark') }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="amount" class="form-label">Amount</label> <span id="paymentStatus" style="color: green; display: none;">Customer has paid the full amount.</span> 
                                    <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 0) }}">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="discount" class="form-label">Discount</label>
                                    <input type="text" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ old('discount', 0) }}">
                                    @error('discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="round_off" class="form-label">Round off</label>
                                    <input type="text" class="form-control @error('round_off') is-invalid @enderror" id="round_off" name="round_off" value="{{ old('round_off', 0) }}">
                                    @error('round_off')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="grand_total" class="form-label">Net Total</label>
                                    <input type="text" class="form-control" id="grand_total" name="grand_total" value="" readonly>
                                </td>
                            </tr>
                        </table>
                        <!-- Payment method options -->
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="cash" name="payment_method" value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cash">Cash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="cheque" name="payment_method" value="cheque" {{ old('payment_method') == 'cheque' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cheque">Cheque</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="online_bank" name="payment_method" value="online bank" {{ old('payment_method') == 'online bank' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="online_bank">Online Bank</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="other" name="payment_method" value="other" {{ old('payment_method') == 'other' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                    @error('payment_method')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <label for="signature" class="form-label">Signature:</label>
                                    <input type="text" class="form-control @error('signature') is-invalid @enderror" id="signature" name="signature" value="{{ old('signature') }}">
                                    @error('signature')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </table>
                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
    });
    
    document.getElementById('customer').addEventListener('change', function () {
    const selectedCustomer = this.options[this.selectedIndex];
    const grandTotalAmount = selectedCustomer.getAttribute('data-salesbook-amount') || 0;
    const recievedAmount = selectedCustomer.getAttribute('data-salesbook-received') || 0;
    const salesbookAmount = grandTotalAmount - recievedAmount;
    console.log(salesbookAmount);
    

    // Update the amount input with the corresponding value
    document.getElementById('amount').value = salesbookAmount;

    document.getElementById('grand_total').value = salesbookAmount;

    const paymentStatus = document.getElementById('paymentStatus');
    if (salesbookAmount===0) {
        paymentStatus.style.display = 'inline';
        paymentStatus.textContent =  '(Customer has paid the full amount.)';
    }else{
        paymentStatus.style.display = 'none';
    }
     
});
</script>
@endsection