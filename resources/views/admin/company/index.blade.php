@extends('admin.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Users</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Comapny
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="companyTable">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Location</th>
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
                <h5 class="modal-title" id="exampleModalLabel1">Company Add</h5>
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
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" placeholder="xxxx@xxx.xx" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" class="form-control" placeholder="" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" rows="3"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" class="form-control" placeholder="" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="type" class="form-select form-select">
                            <option value="">select</option>
                            <option value="type1">type1</option>
                            <option value="type2">type2</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddComapany">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Company Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input type="hidden" id="compid">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="editname" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="editemail" class="form-control" placeholder="xxxx@xxx.xx" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="editphone" class="form-control" placeholder="" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="editaddress" rows="3"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="editcity" class="form-control" placeholder="" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="edittype" class="form-select form-select">
                            <option value="">select</option>
                            <option value="type1">type1</option>
                            <option value="type2">type2</option>
                        </select>
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
        const table = $("#companyTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.company.allcompany') }}",
            },
            columns: [
                {
                    data: "name",
                    render: (data, type, row) => {
                        const url = '{{ route("admin.company.show", ":userId") }}'.replace(":userId", row.id);
                        return `<a href="${url}">${row.name}</a>`;
                    },
                },
                {
                    data: "city",
                    render: (data, type, row) => {
                        return `<a href="#">${row.city}</a>`;
                    },
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
        $('#AddCompany').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                type: $('#type').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };

            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('admin.company.store') }}', // Adjust the route as necessary
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
            const url = '{{ route("admin.company.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#compid').val(data.id);
                    $('#editname').val(data.name);
                    $('#editemail').val(data.email);
                    $('#editphone').val(data.phone);
                    $('#editaddress').val(data.address);
                    $('#editcity').val(data.city);
                    $('#edittype').val(data.type);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Company found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Company not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditComapany').on('click', function() {
            const userId = $('#compid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('admin.company.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: $('#editname').val(),
                    email: $('#editemail').val(),
                    phone: $('#editphone').val(),
                    address: $('#editaddress').val(),
                    city: $('#editcity').val(),
                    type: $('#edittype').val(),
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
                        console.error('Error updating company data:', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Error updating company data:', xhr);
                }
            });
        });

        // Update user status
        function updateUserStatus(userId, status) {
            const message = status === "active" ? "Company will be able to log in after activation." : "Company will not be able to log in after deactivation.";

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
                        url: "{{ route('admin.company.status') }}",
                        data: { userId, status, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                const successMessage = status === "active" ? "Company activated successfully." : "Company deactivated successfully.";
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
                text: "Do you want to delete this Company?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = '{{ route("admin.company.destroy", ":userId") }}'.replace(":userId", userId);
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

        // Flash message function
        function setFlash(type, message) {
            const flashContainer = document.createElement("div");
            flashContainer.className = `alert alert-${type}`;
            flashContainer.textContent = message;

            // Assuming you have a container to append flash messages
            const flashMessageContainer = document.getElementById("flash-message-container");
            if (flashMessageContainer) {
                flashMessageContainer.appendChild(flashContainer);

                // Automatically remove the flash message after 3 seconds
                setTimeout(() => {
                    flashContainer.remove();
                }, 3000);
            } else {
                console.warn("Flash message container not found!");
            }
        }

        // Expose functions to global scope
        window.updateUserStatus = updateUserStatus;
        window.deleteUser = deleteUser;
        window.editUser = editUser;
    });

</script>
@endsection
