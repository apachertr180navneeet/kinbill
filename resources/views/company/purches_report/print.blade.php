@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Purches Invoice</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button id="print" class="btn btn-primary">
                Print
            </button>
        </div>
    </div>
    <div class="row invoice-preview" id="printdata">
        <!-- Invoice -->
        <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                        <div class="mb-xl-0 mb-4">
                            <div class="d-flex svg-illustration mb-3 gap-2">
                                <span class="app-brand-logo demo">
                                    Logo
                                </span>
                            </div>
                            <p class="mb-1">Bill From :- {{ $purchaseReport->date  }} </p>
                            <p class="mb-1">Address</p>
                            <p class="mb-0">{{ $purchaseReport->vendor_city }} {{ $purchaseReport->vendor_state }} ({{ $purchaseReport->vendor_phone }})</p>
                        </div>
                        <div>
                            <h4>Invoice #{{ $purchaseReport->invoice_number }}</h4>
                            <div class="me-1">
                                <span class="me-1">Date:</span>
                                <span class="fw-medium">{{ $purchaseReport->date }}</span>
                            </div>
                            <div>
                                <span class="me-1">GST No.:</span>
                                <span class="fw-medium">{{ $purchaseReport->vendor_gst_no }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table border-top m-0">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Tax</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseReport->purchesbookitem as $index => $item)
                                <tr>
                                    <td class="text-nowrap">{{ $index + 1 }}</td>
                                    <td class="text-nowrap">{{ $item->item->name }}</td>
                                    <td class="text-nowrap">{{ $item->quantity ?? 'N/A' }}</td>
                                    <td class="text-nowrap">₹{{ number_format($item->rate, 2) ?? '0.00' }}</td>
                                    <td class="text-nowrap">₹{{ number_format($item->tax, 2) ?? '0.00' }}</td>
                                    <td class="text-nowrap">₹{{ number_format($item->amount, 2) ?? '0.00' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="align-top px-4 py-5">
                                    {{--  <p class="mb-2">
                                        <span class="me-1 fw-medium">Salesperson:</span>
                                        <span>Alfie Solomons</span>
                                    </p>  --}}
                                    <span>Thanks for your business</span>
                                </td>
                                <td class="text-end px-4 py-5">
                                    <p class="mb-2">Other Expenses(+) :</p>
                                    <p class="mb-2">Discount(-):</p>
                                    <p class="mb-2">Round Off(-/+):</p>
                                    <p class="mb-0">Grand Total:</p>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="fw-medium mb-2">₹{{ $purchaseReport->other_expense }}</p>
                                    <p class="fw-medium mb-2">₹{{ $purchaseReport->discount }}</p>
                                    <p class="fw-medium mb-2">₹{{ $purchaseReport->round_off }}</p>
                                    <p class="fw-medium mb-0">₹{{ $purchaseReport->grand_total }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /Invoice -->
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('#print').on('click', function() {
            var printContents = $('#printdata').html();
            $('body').html(printContents);
            window.print();
            location.reload();
        });
    });
</script>
@endsection
