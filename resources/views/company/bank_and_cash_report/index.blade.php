@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Bank And Cash Report</span>
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
                                    <th>Date</th>
                                    <th>Serial No.</th>
                                    <th>Type</th>
                                    <th>Particular</th>
                                    <th>Diposit</th>
                                    <th>Withdraw</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h4>Net Value = <span id="netValue"></span></h4>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <h4>Deposit = <span id="DepositValue"></span></h4>
                        </div>
                        <div class="col-md-3">
                            <h4>Withdraw Value = <span id="WithdrawValue"></span></h4>
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
    $(document).ready(function() {
        const table = $('#variationTable').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('company.bank.and.cash.report.getall') }}",
                type: 'GET',
                dataSrc: function(json) {
                    // Calculate total deposit and withdraw amounts here
                    let totalDeposit = 0;
                    let totalWithdraw = 0;

                    json.data.forEach(function(item) {
                        if (item.payment_take.toLowerCase() === 'deposit') {
                            totalDeposit += parseFloat(item.amount) || 0;
                        } else {
                            totalWithdraw += parseFloat(item.amount) || 0;
                        }
                    });

                    // Set the calculated values in the respective HTML elements
                    $('#DepositValue').text(totalDeposit.toFixed(2));
                    $('#WithdrawValue').text(totalWithdraw.toFixed(2));
                    $('#netValue').text((totalDeposit - totalWithdraw).toFixed(2));

                    // Return the processed data to the DataTable
                    return json.data;
                }
            },
            columns: [
                { data: "date" },
                { data: "serial_no" },
                { data: "payment_type" },
                { data: "particular" },
                {
                    data: "amount",
                    render: function(data, take, row) {
                        if (row.payment_take.toLowerCase() === 'deposit') {
                            return data;
                        }
                        return '0';
                    }
                },
                {
                    data: "amount",
                    render: function(data, take, row) {
                        if (row.payment_take.toLowerCase() !== 'deposit') {
                            return data;
                        }
                        return '0';
                    }
                }
            ]
        });
    });
</script>
@endsection
