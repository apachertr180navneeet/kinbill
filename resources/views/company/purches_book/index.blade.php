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
        <div class="col-md-6 text-end">
            <a href="{{ route('company.purches.book.add') }}" class="btn btn-primary">
                Add Purchase Book
            </a>
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
                                    <th>Invoice number</th>
                                    <th>Amount</th>
                                    <th>Vendor</th>
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
        const table = $('#variationTable').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('company.purches.book.getall') }}",
                type: 'GET',
            },
            columns: [
                { data: "date" },
                { data: "invoice_number" },
                { data: "grand_total" },
                { data: "vendor_name" },
                {
                    data: "action",
                    render: (data, type, row) => {

                        const deleteButton = `<button type="button" class="btn btn-sm btn-danger" onclick="delete(${row.id})">Delete</button>`;
                        const editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="edit(${row.id})">Edit</button>`;
                        return `${deleteButton} ${editButton}`;
                    },
                },
            ],
        });

        // Delete
        window.delete = function(userId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Tax?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = '{{ route("company.tax.destroy", ":userId") }}'.replace(":userId", userId);
                    $.ajax({
                        type: "DELETE",
                        url,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                setFlash("success", "Tax deleted successfully.");
                            } else {
                                setFlash("error", "There was an issue deleting the tax. Please contact your system administrator.");
                            }
                            table.ajax.reload();
                        },
                        error: function () {
                            setFlash("error", "There was an issue processing your request. Please try again later.");
                        },
                    });
                }
            });
        };

        // Flash message function using Toast.fire
        function setFlash(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        }
    });
</script>
@endsection
