@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">GST CALCULATION</span>
            </h5>
        </div>
    </div>
    @php
        use Carbon\Carbon;
        $startDate = $startDate ? Carbon::parse($startDate)->format('d-m-Y') : null;
        $endDate = $endDate ? Carbon::parse($endDate)->format('d-m-Y') : null;
    @endphp
    <style>
        /* Print CSS */
        @media print {
            @page {
                size: A4 landscape; /* Set page size to A4 and orientation to landscape */
                margin: 0; /* Optional: Adjust margins as needed */
            }

            body {
                margin: 0; /* Remove default margins */
            }

            #printThis {
                width: 100%; /* Ensure full-width usage for landscape */
                overflow: hidden; /* Hide any overflow content */
            }
        }
    </style>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('company.gst.report.filter') }}" method="GET">
                        <div class="row">
                            <!-- Date Range Filters -->
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request()->get('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request()->get('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary mt-4">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('company.purches.report.index') }}" class="btn btn-primary mt-4">R1</a>
                                <a href="{{ route('company.sales.report.index') }}" class="btn btn-primary mt-4">R2</a>
                                <button id="printdiv" class="btn btn-primary mt-4"> Print Data</button>
                            </div>
                        </div>
                    </form>

                    <div id="printThis">
                        <div class="row border mt-4">
                            <!-- Date Range Filters -->
                            <div class="col-md-4 d-flex mb-4">
                                <button id="filterBtn" class="btn btn-primary mt-4">GSTR :- {{ $companyDetail->gstin }}</button>
                            </div>
                            <div class="col-md-8 mb-4 d-flex justify-content-center">
                                <button id="filterBtn" class="btn btn-primary mt-4">{{ $companyDetail->name }}</button>
                                <button id="filterBtn" class="btn btn-primary mt-4" style="margin-left: 5%;">{{ $startDate }} - {{ $endDate }}</button>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap mt-4">
                            <table class="table table-bordered" id="variationTable">
                                <thead>
                                    <tr>
                                        <th>S. No.</th>
                                        <th>Particular</th>
                                        <th>Voucher Count</th>
                                        <th>Taxable</th>
                                        <th>IGST</th>
                                        <th>CGST</th>
                                        <th>SGST</th>
                                        <th>Cess</th>
                                        <th>Tax Amount</th>
                                        <th>Invoice Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>B2B invoices -4A,4B,4C,6B,6C</td>
                                        <td>{{ $b2btotalInvoiceCount }}</td>
                                        <td>{{ $b2btotalAmountBeforeTax }}</td>
                                        <td>{{ $b2btotalIgst }}</td>
                                        <td>{{ $b2btotalSgst }}</td>
                                        <td>{{ $b2btotalCgst }}</td>
                                        <td>0</td>
                                        <td>{{ $b2btotalIgst + $b2btotalSgst + $b2btotalCgst }}</td>
                                        <td>{{ $b2btotalGrandTotal }}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>B2C ( large Invoices )-5A,5B</td>
                                        <td>{{ $b2ctotalInvoiceCount }}</td>
                                        <td>{{ $b2ctotalAmountBeforeTax }}</td>
                                        <td>{{ $b2ctotalIgst }}</td>
                                        <td>{{ $b2ctotalSgst }}</td>
                                        <td>{{ $b2ctotalCgst }}</td>
                                        <td>0</td>
                                        <td>{{ $b2ctotalIgst + $b2ctotalSgst + $b2ctotalCgst }}</td>
                                        <td>{{ $b2ctotalGrandTotal }}</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Credit / Debit Note ( Reg)-9B</td>
                                        <td>{{ $b2bSalesBookItemInvoiceCount }}</td>
                                        <td>{{ $b2bTotalPrice }}</td>
                                        <td>{{ $b2bTotalTaxValue }}</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>{{ $b2bTotalTaxValue }}</td>
                                        <td>{{ $b2bTotalPrice + $b2bTotalTaxValue }}</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Credit/ debit note (unreg)-9B</td>
                                        <td>{{ $b2cSalesBookItemInvoiceCount }}</td>
                                        <td>{{ $b2cTotalPrice }}</td>
                                        <td>{{ $b2cTotalTaxValue }}</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>{{ $b2cTotalTaxValue }}</td>
                                        <td>{{ $b2cTotalPrice + $b2cTotalTaxValue }}</td>
                                    </tr>
                                    <tr id="total">
                                        <td></td>
                                        <td>TOTAL</td>
                                        <td>{{ $b2btotalInvoiceCount + $b2ctotalInvoiceCount + $b2bSalesBookItemInvoiceCount + $b2cSalesBookItemInvoiceCount }}</td>
                                        <td>{{ $b2btotalAmountBeforeTax + $b2ctotalAmountBeforeTax + $b2bTotalPrice + $b2cTotalPrice }}</td>
                                        <td>{{ $b2btotalIgst + $b2ctotalIgst + $b2bTotalTaxValue + $b2cTotalTaxValue }}</td>
                                        <td>{{ $b2btotalSgst + $b2ctotalSgst }}</td>
                                        <td>{{ $b2btotalCgst + $b2ctotalCgst }}</td>
                                        <td>0</td>
                                        <td>{{ ($b2btotalIgst + $b2btotalSgst + $b2btotalCgst) + ($b2ctotalIgst + $b2ctotalSgst + $b2ctotalCgst) + $b2bTotalTaxValue + $b2cTotalTaxValue }}</td>
                                        <td>{{ $b2btotalGrandTotal + $b2ctotalGrandTotal + ($b2bTotalPrice + $b2bTotalTaxValue) + ($b2cTotalPrice + $b2cTotalTaxValue) }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#printdiv').click(function() {
            var printContents = $('#printThis').html();
            var originalContents = $('body').html();

            $('body').html(printContents);
            window.print();
            $('body').html(originalContents);
            // Reload the page after print dialog is closed
            window.location.reload();
        });
    });
</script>
@endsection
