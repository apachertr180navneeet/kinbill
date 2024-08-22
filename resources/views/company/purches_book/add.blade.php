@extends('company.layouts.app')

@section('style')
<!-- Add any necessary styles here -->
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Purchase Book</span>
            </h5>
        </div>
    </div>
    <form role="form" action="{{ route('company.purches.book.store') }}" method="post" id="coustomer_add" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Purchase</h5>
                    <div class="card-body">
                        <!-- Purchase details form -->
                        <div class="row">
                            <!-- Date Field -->
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input class="form-control" type="date" id="date" name="date" value="{{ old('date') }}">
                                @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Invoice Field -->
                            <div class="col-md-6 mb-3">
                                <label for="invoice" class="form-label">Invoice</label>
                                <input type="text" class="form-control" id="invoice" name="invoice" value="{{ old('invoice') }}">
                                @error('invoice')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Vendor Field -->
                            <div class="col-md-6 mb-3">
                                <label for="vendor" class="form-label">Vendor</label>
                                <select class="form-select" id="vendor" name="vendor">
                                    <option selected>Select</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor') == $vendor->id ? 'selected' : '' }}>{{ $vendor->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('vendor')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Transport Field -->
                            <div class="col-md-6 mb-3">
                                <label for="transport" class="form-label">Transport</label>
                                <input type="text" class="form-control" id="transport" name="transport" value="{{ old('transport') }}">
                                @error('transport')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Item details form -->
                        <div class="row">
                            <!-- Item Selection -->
                            <div class="col-md-3 mb-3">
                                <label for="item" class="form-label">Item</label>
                                <select class="form-select" id="item">
                                    <option selected>Select</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" data-tax="{{ $item->tax_rate }}" data-variation="{{ $item->variation_name }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <div id="item_error" class="text-danger"></div>
                            </div>
                            <!-- Quantity Field -->
                            <div class="col-md-3 mb-3">
                                <label for="qty" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="qty" min="0">
                                <div id="qty_error" class="text-danger"></div>
                            </div>
                            <!-- Amount per Unit Field -->
                            <div class="col-md-3 mb-3">
                                <label for="amount" class="form-label">Amount per Unit</label>
                                <input type="number" class="form-control" id="amount" min="0">
                                <div id="amount_error" class="text-danger"></div>
                            </div>
                            <!-- Add Item Button -->
                            <div class="col-md-3 mb-3">
                                <button type="button" class="btn btn-info" id="addItem">Add Item</button>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <table class="table table-bordered mt-4" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Variation</th>
                                    <th>Rate</th>
                                    <th>Tax</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamically added rows will appear here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary fields -->
                    <div class="card-body">
                        <div class="row">
                            <!-- Total Tax -->
                            <div class="col-md-3 mb-3"></div>
                            <div class="col-md-3 mb-3">
                                <label for="total_tax" class="form-label text-end">Total Tax</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" class="form-control" id="total_tax" value="0" name="total_tax" min="0" readonly>
                                @error('total_tax')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3"></div>
                        </div>
                        <!-- Other Expenses -->
                        <div class="row">
                            <div class="col-md-3 mb-3"></div>
                            <div class="col-md-6 mb-3">
                                <label for="other_expense" class="form-label text-end">Other Expense(+)</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" class="form-control" id="other_expense" value="0" min="0" name="other_expense">
                                @error('other_expense')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Discount -->
                        <div class="row">
                            <div class="col-md-3 mb-3"></div>
                            <div class="col-md-6 mb-3">
                                <label for="discount" class="form-label text-end">Discount(-)</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" class="form-control" id="discount" name="discount" min="0" value="0">
                                @error('discount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Round Off -->
                        <div class="row">
                            <div class="col-md-3 mb-3"></div>
                            <div class="col-md-6 mb-3">
                                <label for="round_off" class="form-label text-end">Round Off(-/+)</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" class="form-control" id="round_off" name="round_off" value="0" step="any">
                                @error('round_off')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Grand Total -->
                        <div class="row">
                            <div class="col-md-3 mb-3"></div>
                            <div class="col-md-6 mb-3">
                                <label for="grand_total" class="form-label text-end">Grand Total</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="number" class="form-control" id="grand_total" name="grand_total" value="0" min="0" readonly>
                                @error('grand_total')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        let itemCount = 0;
        let totalTax = 0;
        let grandTotal = 0;

        // Function to update the grand total field
        function updateGrandTotal() {
            const otherExpense = parseFloat($('#other_expense').val()) || 0;
            const discount = parseFloat($('#discount').val()) || 0;
            const roundOff = parseFloat($('#round_off').val()) || 0;
            const calculatedTotal = grandTotal + otherExpense - discount + roundOff;
            $('#grand_total').val(calculatedTotal.toFixed(2));
        }

        // Add item to the table
        $('#addItem').on('click', function() {
            const item = $('#item option:selected').text();
            const itemId = $('#item').val();
            const variation = $('#item option:selected').data('variation');
            const taxRate = parseFloat($('#item option:selected').data('tax'));
            const qty = parseInt($('#qty').val());
            const amountPerUnit = parseFloat($('#amount').val());

            if (itemId && !isNaN(qty) && !isNaN(amountPerUnit)) {
                const totalAmount = qty * amountPerUnit;
                const tax = totalAmount * (taxRate / 100);
                const totalWithTax = totalAmount + tax;

                itemCount++;
                totalTax += tax; // Update total tax
                grandTotal += totalWithTax;

                const row = `
                    <tr>
                        <td>${itemCount}</td>
                        <td>${item}<input type="hidden" name="items[]" value="${itemId}"></td>
                        <td>${qty}<input type="hidden" name="quantities[]" value="${qty}"></td>
                        <td>${variation}</td>
                        <td>${totalAmount.toFixed(2)}<input type="hidden" name="rates[]" value="${totalAmount.toFixed(2)}"></td>
                        <td>${tax.toFixed(2)}<input type="hidden" name="taxes[]" value="${tax.toFixed(2)}"></td>
                        <td>${totalWithTax.toFixed(2)}<input type="hidden" name="totalAmounts[]" value="${totalWithTax.toFixed(2)}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeItem">Remove</button></td>
                    </tr>
                `;
                $('#itemsTable tbody').append(row);

                // Update the total tax and grand total fields
                $('#total_tax').val(totalTax.toFixed(2));
                updateGrandTotal();

                // Clear the input fields after adding the item
                $('#item').val('').trigger('change');
                $('#qty').val('');
                $('#amount').val('');
            } else {
                setFlash("error", "Please fill all fields with valid values.");
            }
        });

        // Remove item from the table
        $(document).on('click', '.removeItem', function() {
            const taxToRemove = parseFloat($(this).closest('tr').find('input[name="taxes[]"]').val());
            const amountToRemove = parseFloat($(this).closest('tr').find('input[name="totalAmounts[]"]').val());

            totalTax -= taxToRemove; // Subtract the removed tax from total tax
            grandTotal -= amountToRemove; // Subtract the removed amount from grand total

            $(this).closest('tr').remove();
            itemCount--;
            updateSNo();

            // Update the total tax and grand total fields after removal
            $('#total_tax').val(totalTax.toFixed(2));
            updateGrandTotal();
        });

        // Update S. No. after item removal
        function updateSNo() {
            $('#itemsTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Listen to changes in other expenses, discount, and round off fields to update grand total
        $('#other_expense, #discount, #round_off').on('input', function() {
            updateGrandTotal();
        });

        // Validate before form submission
        $('form').on('submit', function(e) {
            if ($('#itemsTable tbody tr').length === 0) {
                setFlash("error", "Please add at least one item to the purchase.");
                e.preventDefault(); // Prevent form submission
                return;
            }

            let valid = true;
            $('#itemsTable tbody tr').each(function() {
                if ($(this).find('input[name="items[]"]').length === 0 ||
                    $(this).find('input[name="quantities[]"]').length === 0 ||
                    $(this).find('input[name="rates[]"]').length === 0 ||
                    $(this).find('input[name="taxes[]"]').length === 0 ||
                    $(this).find('input[name="totalAmounts[]"]').length === 0) {
                    valid = false;
                }
            });

            if (!valid) {
                setFlash("error", "Please ensure all fields (items, quantities, rates, taxes, total amounts) are filled in each row.");
                e.preventDefault(); // Prevent form submission
            }
        });

        // Flash message function using Toast.fire
        function setFlash(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Ensure only valid numbers are entered in round off field
        $('#round_off').on('input', function() {
            this.value = this.value.replace(/[^0-9.-]/g, '');
        });
    });

</script>
@endsection
