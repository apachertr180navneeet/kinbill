@extends('company.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Item</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Bank
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
                                    <th>Name</th>
                                    <th>Bank Name</th>
                                    <th>Account No.</th>
                                    <th>IFSC Code</th>
                                    <th>Branch</th>
                                    <th>Status</th>
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
                <h5 class="modal-title" id="exampleModalLabel1">Bank Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" id="bank_name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="acc_no" class="form-label">Account No.</label>
                        <input type="text" id="acc_no" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="ifsc_code" class="form-label">IFSC Code</label>
                        <input type="text" id="ifsc_code" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="branch" class="form-label">Branch</label>
                        <input type="text" id="branch" class="form-control" placeholder="Enter Name" />
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
                    <div class="col-md-12 mb-3">
                        <input type="hidden" id="compid">
                        <label for="editname" class="form-label">Name</label>
                        <input type="text" id="editname" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_bank_name" class="form-label">Bank Name</label>
                        <input type="text" id="edit_bank_name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_acc_no" class="form-label">Account No.</label>
                        <input type="text" id="editacc_no" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_ifsc_code" class="form-label">IFSC Code</label>
                        <input type="text" id="edit_ifsc_code" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_branch" class="form-label">Branch</label>
                        <input type="text" id="edit_branch" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="EditComapany">Save</button>
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
                url: "{{ route('company.bank.getall') }}",
            },
            columns: [
                {
                    data: "name",
                },
                {
                    data: "bank_name",
                },
                {
                    data: "account_number",
                },
                {
                    data: "ifsc_code",
                },
                {
                    data: "branch_name",
                },
                {
                    data: "status",
                    render: (data, type, row) => {
                        const statusBadge = row.status === "active" ?
                            '<span class="badge bg-label-success me-1">Active</span>' :
                            '<span class="badge bg-label-danger me-1">Inactive</span>';
                        return statusBadge;
                    },
                },
                {
                    data: "action",
                    render: (data, type, row) => {
                        const statusButton = row.status === "inactive"
                            ? `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${row.id}, 'active')">Activate</button>`
                            : `<button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus(${row.id}, 'inactive')">Deactivate</button>`;

                        const deleteButton = `<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">Delete</button>`;
                        const editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editUser(${row.id})">Edit</button>`;

                        return `${statusButton} ${deleteButton} ${editButton}`;
                    },
                },

            ],
        });

        // Handle form submission via AJAX
        $('#AddItem').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                name: $('#name').val(),
                bank_name: $('#bank_name').val(),
                acc_no: $('#acc_no').val(),
                ifsc_code : $('#ifsc_code').val(),
                branch: $('#branch').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('company.bank.store') }}', // Adjust the route as necessary
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

        // Define editUser function
        function editUser(userId) {
            const url = '{{ route("company.bank.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#compid').val(data.id);
                    $('#editname').val(data.name);
                    $('#edit_bank_name').val(data.bank_name);
                    $('#editacc_no').val(data.account_number);
                    $('#edit_ifsc_code').val(data.ifsc_code);
                    $('#edit_branch').val(data.branch_name);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Bank found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Bank not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditComapany').on('click', function() {
            const userId = $('#compid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('company.bank.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: $('#editname').val(),
                    bank_name: $('#edit_bank_name').val(),
                    account_number: $('#editacc_no').val(),
                    ifsc_code: $('#edit_ifsc_code').val(),
                    branch_name: $('#edit_branch').val(),
                    id: userId // Ensure userId is in scope or adjust accordingly
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
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

        // Update user status
        function updateUserStatus(userId, status) {
            const message = status === "active" ? "Bank will be able to log in after activation." : "Bank will not be able to log in after deactivation.";

            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Okay",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('company.bank.status') }}",
                        data: { userId, status, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                const successMessage = status === "active" ? "Bank activated successfully." : "Bank deactivated successfully.";
                                setFlash("success", successMessage);
                            } else {
                                setFlash("error", "There was an issue changing the status. Please contact your system administrator.");
                            }
                            table.ajax.reload(); // Reload DataTable
                        },
                        error: function () {
                            setFlash("error", "There was an issue processing your request. Please try again later.");
                        },
                    });
                } else {
                    table.ajax.reload(); // Reload DataTable
                }
            });
        };

        // Delete user
        function deleteUser(userId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Bank?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = '{{ route("company.bank.destroy", ":userId") }}'.replace(":userId", userId);
                    $.ajax({
                        type: "DELETE",
                        url,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                setFlash("success", "Bank deleted successfully.");
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
        window.updateUserStatus = updateUserStatus;
        window.deleteUser = deleteUser;
        window.editUser = editUser;
    });

</script>
@endsection