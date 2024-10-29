@extends('company.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Contra Report</span>
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
                                    <th>Particular</th>
                                    <th>Amount</th>
                                    <th>Deposit in</th>
                                    <th>Withdraw  From</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $bankandcashs as $bankandcash )
                                    <tr>
                                        <td>{{ $bankandcash->date }}</td>
                                        <td>{{ $bankandcash->serial_no }}</td>
                                        <td>{{ $bankandcash->particular }}</td>
                                        <td>{{ $bankandcash->amount }}</td>
                                        <td>{{ $bankandcash->deposite_bank_name }}</td>
                                        <td>{{ $bankandcash->withdraw_bank_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--  <div class="row">
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
                    </div>  --}}
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
