@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Stock Report</span>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="variationTable">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Current Stock</th>
                                    <th>Sales</th>
                                    <th>S Return</th>
                                    <th>Purches</th>
                                    <th>P resturn</th>
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
        const table = $('#variationTable').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('company.stock.report.getall') }}",
                type: 'GET',
            },
            columns: [
                { data: "name" },
                {
                    data: "total_stock_quantity",
                    render: function(data) {
                        return data || 0; // Returns 0 if data is null or undefined
                    }
                },
                {
                    data: null, // Use null for custom data rendering
                    render: function(row) {
                        const totalSalesQty = row.total_sales_book_qty || 0;
                        const totalReturnQty = row.total_sreturn || 0;
                        return totalSalesQty - totalReturnQty; // Calculate and return the value
                    }
                },
                {
                    data: "total_sreturn",
                    render: function(data) {
                        return data || 0; // Returns 0 if data is null or undefined
                    }
                },
                {
                    data: null, // Use null for custom data rendering
                    render: function(row) {
                        const totalPurchasesQty = row.total_purches_book_qty || 0;
                        const totalPReturnQty = row.total_preturn || 0;
                        return totalPurchasesQty - totalPReturnQty; // Calculate and return the value
                    }
                },
                {
                    data: "total_preturn",
                    render: function(data) {
                        return data || 0; // Returns 0 if data is null or undefined
                    }
                },
            ]
        });
    });

</script>
@endsection
