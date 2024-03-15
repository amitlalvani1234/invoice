@extends('layouts.app')

@section('block')
    <form id="invoice_add" method="POST" action="{{ route('invoice.update', ['id' => $data->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Invoice Date</label>
            <input type="date" name="date" class="form-control" value="{{ $data->invoice_date }}" id="date"
                max="{{ date('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" name="customer_name" class="form-control" value="{{ $data->customer_name }}"
                id="customer_name" required>
        </div>
        <div class="mb-3">
            <label for="customer_no" class="form-label">Customer Number</label>
            <input type="number" name="customer_no" class="form-control" value="{{ $data->customer_mo }}"
                oninput="maxLengthCheck(this)" id="customer_no" maxlength="10" minlength="10" required>
            <div id="errorMessage" style="color: red;"></div>

        </div>
        <div class="form-check form-switch">

            <input class="form-check-input" name="gst_inculsive" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                onchange="calculateSubtotal()" {{ $data->gst_inculsive == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="flexSwitchCheckDefault">Including Gst</label>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Item Description</label>
            </div>
            <div class="col-md-3">
                <label class="form-label">Item Price</label>
            </div>
            <div class="col-md-3">
                <label class="form-label">Item Quantity</label>
            </div>
            <div class="col-md-3">
                <label class="form-label">Total</label>
            </div>
        </div>


        <div class="container">
            <div id="itemsContainer">


                @foreach ($data->invoicesitems as $invoicesitem)
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="item_description[]" class="form-control item-description"
                                placeholder="Item Description" required value="{{ $invoicesitem->item_description }}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="price[]" class="form-control price" placeholder="Price"
                                oninput="calculateTotal(this.closest('.row'))" min="1" required
                                value="{{ $invoicesitem->price }}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="qty[]" class="form-control qty" placeholder="Quantity"
                                oninput="calculateTotal(this.closest('.row'))" min="1" required
                                value="{{ $invoicesitem->qty }}">
                        </div>
                        <div class="col-md-3 d-flex">
                            <input type="number" readonly name="total[]" class="form-control total" placeholder="Total"
                                value="{{ $invoicesitem->total }}">
                            <button type="button" class="btn btn-danger remove-item">Remove</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="addButton" class="btn btn-primary my-2">Add Item</button>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p id="subtotal" class="text-sm-end"><span class="mx-3 fw-bold">SubTotal</span>{{ $data->subtotal }}</p>

            </div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-3">Gst</div>
            <div class="col-md-3">
                <select name="gst" id="gst" class="form-select" onchange="calculateSubtotal()" required>
                    <option value="">Select Gst</option>
                    <option value="5" {{ $data->gst == 5 ? 'selected' : '' }}> 5% </option>
                    <option value="10" {{ $data->gst == 10 ? 'selected' : '' }}> 10% </option>
                    <option value="18" {{ $data->gst == 18 ? 'selected' : '' }}> 18% </option>
                </select>
            </div>
            <div class="col-md-3">
                <p id="gstamount" class="text-sm-end"><span class="mx-3 fw-bold">Gst
                        Amount</span>{{ $data->gst_amount }}</p>
            </div>
        </div>
        <input type="hidden" id="total_amount" name="total_amount" value="{{ $data->subtotal }}">
        <input type="hidden" id="gst_total_amount" name="gst_total_amount" value="{{ $data->gst_amount }}">
        <input type="hidden" id="sub_total_amount" name="sub_total_amount" value="{{ $data->grand_total }}">


        <div class="row">
            <div class="col-md-12">

                <p id="total" class="text-sm-end"><span class="mx-3 fw-bold">Total</span> {{ $data->grand_total }}
                </p>
            </div>
        </div>



        <div id="formErrors" class="text-danger"></div>
        <div class="row my-3">
            <div class="col-md-3 ">
                <input type="submit" value="submit" id="sub" class="btn btn-primary">
            </div>
        </div>




    </form>
@endsection

@section('scripts')
    <script>
        function maxLengthCheck(object) {
            if (object.value.length > 10) {

                document.getElementById('sub').disabled = true;
                document.getElementById('errorMessage').innerHTML = 'Only Ten Digits Allowed';
            } else if (object.value.length < 10) {
                document.getElementById('errorMessage').innerHTML = 'Only Ten Digits Allowed';
                document.getElementById('sub').disabled = true;
            } else {
                document.getElementById('sub').disabled = false;
                document.getElementById('errorMessage').innerHTML = '';
            }
        }
        let itemId

        function calculateTotal(itemDiv) {
            const priceInput = itemDiv.querySelector('.price');
            const qtyInput = itemDiv.querySelector('.qty');
            const totalInput = itemDiv.querySelector('.total');

            const price = parseFloat(priceInput.value);
            const qty = parseFloat(qtyInput.value);


            if (price >= 0 && qty >= 0) {
                const total = price * qty;
                totalInput.value = total.toFixed(2);
            } else {
                totalInput.value = '';
            }

            calculateSubtotal();
        }



        function calculateSubtotal() {

            const totalInputs = document.querySelectorAll('.total');
            const gst = document.getElementById('gst').value
            var checkbox = document.getElementById("flexSwitchCheckDefault")
            let subtotal = 0;
            let total1 = 0;

            totalInputs.forEach(totalInput => {
                const total = parseFloat(totalInput.value);
                if (!isNaN(total)) {
                    subtotal += total;
                }
            });


            if (gst != "") {
                if (checkbox.checked) {
                    total1 = subtotal / (1 + gst / 100);
                    gst_amount = subtotal - total1;
                    document.getElementById('subtotal').innerHTML =
                        '<span class="mx-3 fw-bold">Subtotal</span> <span class="fw-bold">' + total1.toFixed(2) +
                        '</span>';
                    document.getElementById('gstamount').innerHTML =
                        '<span class="mx-3 fw-bold">Gst Amount</span> <span class="fw-bold">' + gst_amount.toFixed(2) +
                        '</span>';
                    document.getElementById('total').innerHTML =
                        '<span class="mx-3 fw-bold">Grand Total</span> <span class="fw-bold">' + subtotal.toFixed(2) +
                        '</span>';
                    document.getElementById('total_amount').value = total1;
                    document.getElementById('gst_total_amount').value = gst_amount;
                    document.getElementById('sub_total_amount').value = subtotal;
                } else {
                    gst_amount = (subtotal * gst) / 100;
                    total1 = subtotal + gst_amount;
                    document.getElementById('subtotal').innerHTML =
                        '<span class="mx-3 fw-bold">Subtotal</span> <span class="fw-bold">' + subtotal.toFixed(2) +
                        '</span>';
                    document.getElementById('gstamount').innerHTML =
                        '<span class="mx-3 fw-bold">Gst Amount</span> <span class="fw-bold">' + gst_amount.toFixed(2) +
                        '</span>';
                    document.getElementById('total').innerHTML =
                        '<span class="mx-3 fw-bold">Grand Total</span> <span class="fw-bold">' + total1.toFixed(2) +
                        '</span>';
                    document.getElementById('total_amount').value = subtotal
                    document.getElementById('gst_total_amount').value = gst_amount;
                    document.getElementById('sub_total_amount').value = total1;
                }
            } else {
                document.getElementById('subtotal').innerHTML =
                    '<span class="mx-3 fw-bold">Subtotal</span> <span class="fw-bold">' + subtotal.toFixed(2) +
                    '</span>';
                document.getElementById('sub_total_amount').value = subtotal;
                document.getElementById('total_amount').value = subtotal
            }





        }



        function addItem() {
            const itemsContainer = document.getElementById('itemsContainer');

            const itemDiv = document.createElement('div');
            itemDiv.classList.add('row');

            itemDiv.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="item_description[]" class="form-control item-description" placeholder="Item Description" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="price[]" class="form-control price" placeholder="Price" oninput="calculateTotal(this.closest('.row'))" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="qty[]" class="form-control qty" placeholder="Quantity" oninput="calculateTotal(this.closest('.row'))" min="1" required>
            </div>
            <div class="col-md-3 d-flex">
                <input type="number" readonly name="total[]" class="form-control total" placeholder="Total">
                <button type="button" class="btn btn-danger remove-item">Remove</button>
            </div>
        `;

            itemsContainer.appendChild(itemDiv);
            itemId++;


            const removeButtons = document.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.addEventListener('click', removeItem);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.addEventListener('click', removeItem);
            });
        });

        function removeItem(event) {
            const itemDiv = event.target.closest('.row');
            if (itemDiv) {
                itemDiv.remove();
                calculateSubtotal();
            }
        }

        document.getElementById('addButton').addEventListener('click', addItem);

        $(document).ready(function() {
            $("#invoice_add").submit(function(e) {
                e.preventDefault();

                console.log('dasd')
                var formData = new FormData($('#invoice_add')[0]);
                var errorHtml = "";
                var sucessdata = "";
                $.ajax({
                    url: "{{ route('custom_validation') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {


                        console.log(console.response);
                        if (response.errors) {
                            errorHtml = '<ul>';
                            $.each(response.errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#formErrors').html(errorHtml);
                        } else if (response.status == 422) {
                            $('#formErrors').html(response.message);
                        } else {
                            $("#invoice_add")[0].submit();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            errorHtml = '<ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#formErrors').html(errorHtml);
                        }
                    }
                });
            });
        });
    </script>
@endsection
