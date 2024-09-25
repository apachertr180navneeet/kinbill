@extends('company.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Bank And Cash Mangement</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Bank And Cash
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="ItemTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Serial No.</th>
                                    <th>Particular</th>
                                    <th>Amount</th>
                                    <th>Payment(Deposit,Withdraw)</th>
                                    <th>Payment Type(Cash,Bank)</th>
                                    <th>Description</th>
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

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Item Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="Date" id="date" class="form-control" placeholder="Enter Date" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="serial_no" class="form-label">Serial No.</label>
                        <input type="text" id="serial_no" class="form-control" placeholder="Enter Serial No." />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_take" class="form-label">Payment</label>
                        <select id="payment_take" class="form-select form-select">
                            <option value="">select</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdraw">Withdraw</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select id="payment_type" class="form-select form-select">
                            <option value="">select</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="particular" class="form-label">Particular</label>
                        <input type="text" id="particular" class="form-control" placeholder="Enter Particular" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" id="amount" class="form-control" placeholder="Enter Amount" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddItem">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Item Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="id" value="" id="editid">
                    <div class="col-md-6 mb-3">
                        <label for="editdate" class="form-label">Date</label>
                        <input type="Date" id="editdate" class="form-control" placeholder="Enter Date" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_serial_no" class="form-label">Serial No.</label>
                        <input type="text" id="edit_serial_no" class="form-control" placeholder="Enter Serial No." />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_payment_take" class="form-label">Payment</label>
                        <select id="edit_payment_take" class="form-select form-select">
                            <option value="">select</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdraw">Withdraw</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_payment_type" class="form-label">Payment Type</label>
                        <select id="edit_payment_type" class="form-select form-select">
                            <option value="">select</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_particular" class="form-label">Particular</label>
                        <input type="text" id="edit_particular" class="form-control" placeholder="Enter Amount" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_amount" class="form-label">Amount</label>
                        <input type="text" id="edit_amount" class="form-control" placeholder="Enter Amount" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_description" class="form-label">description</label>
                        <textarea class="form-control" id="edit_description" rows="3"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="EditBankAndCash">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection @section('script')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        const table = $("#ItemTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('company.bank.and.cash.getall') }}",
            },
            columns: [
                {
                    data: "date",
                },
                {
                    data: "serial_no",
                },
                {
                    data: "particular",
                },
                {
                    data: "amount",
                },
                {
                    data: "payment_take",
                },
                {
                    data: "payment_type",
                },
                {
                    data: "description",
                },
                {
                    data: "action",
                    render: (data, type, row) => {
                        const deleteButton = `<button type="button" class="btn btn-sm btn-danger" onclick="delete(${row.id})">Delete</button>`;
                        const editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editbankcash(${row.id})">Edit</button>`;

                        return `${deleteButton} ${editButton}`;
                    },
                },

            ],
        });

        // Handle form submission via AJAX
        $('#AddItem').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                date: $('#date').val(),
                serial_no: $('#serial_no').val(),
                amount: $('#amount').val(),
                payment_take : $('#payment_take').val(),
                payment_type: $('#payment_type').val(),
                description: $('#description').val(),
                particular: $('#particular').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');
            $.ajax({
                url: '{{ route('company.bank.and.cash.store') }}', // Adjust the route as necessary
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        setFlash("success", response.message);
                        $('#addModal').modal('hide'); // Close the modal
                        $('#addModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                    } else {
                        // Display validation errors
                        if (response.errors) {
                            for (let field in response.errors) {
                                let $field = $(`#${field}`);
                                if ($field.length) {
                                    $field.siblings('.error-text').text(response.errors[field][0]);
                                }
                            }
                        } else {
                            setFlash("error", response.message);
                        }
                    }
                },
                error: function(xhr) {
                    setFlash("error", "An unexpected error occurred.");
                }
            });
        });

        // Define editbankcash function
        function editbankcash(userId) {
            const url = '{{ route("company.bank.and.cash.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#editid').val(data.id);
                    $('#editdate').val(data.date);
                    $('#edit_serial_no').val(data.serial_no);
                    $('#edit_payment_take').val(data.payment_take);
                    $('#edit_payment_type').val(data.payment_type);
                    $('#edit_amount').val(data.amount);
                    $('#edit_description').val(data.description);
                    $('#edit_particular').val(data.particular);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Item found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Item not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditBankAndCash').on('click', function() {
            const userId = $('#editid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('company.bank.and.cash.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: $('#editdate').val(),
                    serial_no: $('#edit_serial_no').val(),
                    payment_take: $('#edit_payment_take').val(),
                    payment_type: $('#edit_payment_type').val(),
                    amount: $('#edit_amount').val(),
                    description: $('#edit_description').val(),
                    particular: $('#edit_particular').val(),
                    id: userId // Ensure userId is in scope or adjust accordingly
                },
                success: function(response) {
                    if (response.success) {
                        // Optionally, refresh the page or update the table with new data
                        //table.ajax.reload();
                        setFlash("success", response.message);
                        $('#editModal').modal('hide'); // Close the modal
                        $('#editModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                    } else {
                        console.error('Error updating Item data:', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Error updating Item data:', xhr);
                }
            });
        });

        // Delete user
        function deleteUser(userId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = '{{ route("company.item.destroy", ":userId") }}'.replace(":userId", userId);
                    $.ajax({
                        type: "DELETE",
                        url,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                setFlash("success", "User deleted successfully.");
                            } else {
                                setFlash("error", "There was an issue deleting the user. Please contact your system administrator.");
                            }
                            table.ajax.reload(); // Reload DataTable
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

        // Expose functions to global scope
        window.deleteUser = deleteUser;
        window.editbankcash = editbankcash;
    });

</script>
@endsection
