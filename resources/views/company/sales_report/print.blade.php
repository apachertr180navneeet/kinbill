@extends('company.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2"><span class="text-primary fw-light">Sales Invoice</span></h5>
        </div>
        <div class="col-md-6 text-end">
            <button id="print" class="btn btn-primary">Print</button>
        </div>
    </div>
    <div class="row invoice-preview" id="printdata">
        <div id="printHeader" class="row">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Logo -->
                <div class="text-start">
                    <img src="{{ $companyDetail->logo }}" alt="" id="logoImage" width="100" height="100" class="d-none me-3">
                </div>
                <!-- Company Details -->
                <div class="text-center flex-grow-1">
                    <h4>Tax Invoice</h4>
                    <h4 id="companyName" class="mb-0"></h4>
                    <h5 id="companyAddress" class="mb-0"></h5>
                    <h5 id="companyPhone" class="mb-0"></h5>
                    <h5 id="gstNumber" class="mb-0"></h5>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body m-0 p-0">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                        <div class="mb-xl-0 mb-4">
                            <p class="mb-1" style="color: #000">Party Name :- {{ $salesReport->customer_name }}</p>
                            <p class="mb-1">Address</p>
                            <p class="mb-0" style="color: #000">{{ $salesReport->customer_address }}</p>
                            <p class="mb-0" style="color: #000">{{ $salesReport->customer_city }}({{ $salesReport->customer_phone }})</p>
                            <p class="mb-0" style="color: #000">{{ $salesReport->customer_state }} (State Code :- {{ substr($salesReport->customer_gst_no, 0, 2) }})</p>
                            <p class="mb-0" style="color: #000">GST No.: {{ $salesReport->customer_gst_no }}</p>
                        </div>
                        <div>
                            <h4 style="color: #000">Invoice #{{ $salesReport->dispatch_number }}</h4>
                            <div class="me-1">
                                <span class="me-1" style="color: #000">Date:</span>
                                <span class="fw-medium" style="color: #000">{{ $salesReport->date }}</span>
                            </div>
                            <div>
                                <span class="fw-medium" style="color: #000">Transport :- {{ $salesReport->transport }}</span><br>
                                <span class="fw-medium" style="color: #000"> Vechile No. :- {{ $salesReport->vehicle_no }}</span><br>
                                <span class="fw-medium" style="color: #000">Place Of Supply :- {{ $salesReport->item_weight }}</span><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive m-0">
                    <table class="table border-top m-0">
                        <thead>
                            <tr>
                                <th class="fw-bolder" style="font-size: 17px;">No.</th>
                                <th class="fw-bolder" style="font-size: 17px;">Item</th>
                                <th class="fw-bolder" style="font-size: 17px;">HSN</th>
                                <th class="fw-bolder" style="font-size: 17px;">Qty</th>
                                <th class="fw-bolder" style="font-size: 17px;">Unit</th>
                                <th class="fw-bolder" style="font-size: 17px;">Price</th>
                                <th class="fw-bolder" style="font-size: 17px;">Tax</th>
                                <th class="fw-bolder" style="font-size: 17px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesReport->salesbookitem as $index => $item)
                                <tr>
                                    <td class="text-nowrap" style="color: #000">{{ $index + 1 }}</td>
                                    <td class="text-nowrap" style="color: #000">{{ $item->item->name }}</td>
                                    <td class="text-nowrap" style="color: #000">{{ $item->item->hsn_hac }}</td>
                                    <td class="text-nowrap" style="color: #000">{{ $item->quantity ?? 'N/A' }}</td>
                                    <td class="text-nowrap" style="color: #000">{{ $item->item->variation->code ?? 'N/A' }}</td>
                                    <td class="text-nowrap" style="color: #000">₹{{ number_format(floatval($item->rate ?? 0), 2) }}</td>
                                    <td class="text-nowrap" style="color: #000">{{ $item->item->tax->rate }} %</td>
                                    <td class="text-nowrap" style="color: #000">₹{{ number_format(floatval($item->amount ?? 0), 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="">
                                    <span style="color: #000">Rupees {{ $grandtotalwrod }}</span><br>
                                    <span style="color: #000">Bank :</span><br>
                                    <span style="color: #000">Account Name :- {{ $bank->name }}</span><br>
                                    <span style="color: #000">Account Number :- {{ $bank->account_number }}</span><br>
                                    <span style="color: #000">IFSC Code :- {{ $bank->ifsc_code }}</span><br>
                                    <span style="color: #000">Bank Name :- {{ $bank->bank_name }}</span><br>
                                    <h6 class="mb-2 mt-4" style="color: #000">Terms and Conditions</h6>
                                    <ul>
                                        <li style="color: #000; font-size: 12px;">Interest @ 24% will be payable if this bill is not paid within 1 month</li>
                                        <li style="color: #000; font-size: 12px;">No claim for leakage, shortage, or theft in transit</li>
                                        <li style="color: #000; font-size: 12px;">Our responsibility ceases once the material leaves our premises</li>
                                        <li style="color: #000; font-size: 12px;">Products are for industrial use only</li>
                                        <li style="color: #000; font-size: 12px;">Computer-generated invoice</li>
                                    </ul>
                                </td>
                                <td class="text-end">
                                    <p class="mb-1" style="color: #000">SubTotal :</p>
                                    <p class="mb-1" style="color: #000">Other Exp.:</p>
                                    <p class="mb-1" style="color: #000">Discount:</p>
                                    <p class="mb-1" style="color: #000">Tax(IGST):</p>
                                    <p class="mb-1" style="color: #000">Tax(SGST / CGST):</p>
                                    <p class="mb-1" style="color: #000">Round Off:</p>
                                    <p class="mb-0" style="color: #000">Total:</p>
                                    <p class="mb-0" style="color: #000">Received :</p>
                                    <p class="mb-0" style="color: #000">Balance :</p>
                                </td>
                                <td class="text-end">
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->amount_before_tax ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->other_expense ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->discount ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->igst ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->sgst ?? 0), 2) }} / ₹{{ number_format(floatval($salesReport->cgst ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-1" style="color: #000">₹{{ number_format(floatval($salesReport->round_off ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-0" style="color: #000">₹{{ number_format(floatval($salesReport->grand_total ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-0" style="color: #000">₹{{ number_format(floatval($salesReport->recived_amount ?? 0), 2) }}</p>
                                    <p class="fw-medium mb-0" style="color: #000">₹{{ number_format(floatval($salesReport->balance_amount ?? 0), 2) }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4 text-end">
            <p>For : {{ $companyDetail->name }}</p>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('#print').on('click', function() {
            // Set company details dynamically
            $('#companyName').text("{{ $companyDetail->name }}");
            $('#gstNumber').text("GSTIN: {{ $companyDetail->gstin }}");
            $('#companyAddress').text("{{ $companyDetail->address }}");
            $('#companyPhone').text("{{ $companyDetail->phone }}");

            // Set image source and alt attributes dynamically
            $('#logoImage').attr('src', "{{ $companyDetail->logo }}");
            $('#logoImage').attr('alt', "{{ $companyDetail->name }}");
            $('#logoImage').removeClass('d-none');

            // Capture content for printing
            var printContents = $('#printdata').html();
            $('body').html(printContents);
            window.print();
            location.reload();
        });
    });
</script>
@endsection
