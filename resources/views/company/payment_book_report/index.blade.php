@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Payment Report</span>
            </h5>
        </div>
        {{--  <div class="col-md-6 text-end">
            <a href="{{ route('company.payment.book.add') }}" class="btn btn-primary">
                Add Payment Book
            </a>
        </div>  --}}
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="variationTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt number</th>
                                    <th>Customer name</th>
                                    <th>Amount</th>
                                    <th>Payment Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
    $(document).ready(function() {
        // Base URL for the edit route
        const printbaseUrl = "{{ route('company.payment.report.print', ['id' => ':id']) }}";

        const table = $('#variationTable').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('company.payment.report.getall') }}",
                type: 'GET',
            },
            columns: [
                { data: "date" },
                { data: "payment_vouchers_number" },
                { data: "vendor_name" },
                { data: "grand_total" },
                { data: "payment_type" },
                {
                    data: "id",
                    render: function (data, type, row) {
                        const printButton = `<a href="${printbaseUrl.replace(':id', data)}" class="btn btn-sm btn-info">Print</a>`;
                        return `${printButton}`;
                    },
                },
            ],
        });
    });
</script>
@endsection
