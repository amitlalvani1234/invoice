@extends('layouts.app')

@section('block')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered data-table" id="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Invoice no</th>
                                    <th>Invoice Date</th>
                                    <th>Customer Name</th>
                                    <th>Customer No.</th>

                                    <th>Subtotal</th>
                                    <th>Gst Percentage</th>
                                    <th>Gst Aamount</th>
                                    <th>Grand Total</th>
                                    {{-- <th>Item Details</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        var datatable;



        $(document).ready(function() {
            if ($('#data-table').length > 0) {
                datatable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,

                    "pageLength": 1,
                    "iDisplayLength": 1,
                    "responsive": true,
                    "aaSorting": [],
                    "scrollX": true,
                    "scrollY": true,
                    "ajax": {
                        "url": "{{ route('invoice.list') }}",
                        "type": "GET",
                        "dataType": "json",
                        "data": {
                            _token: "{{ csrf_token() }}"
                        }
                    },
                    "columnDefs": [{
                        "orderable": true,
                    }, ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'invoice_date',
                            name: 'invoice_date'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'customer_mo',
                            name: 'customer_mo'
                        },
                        {
                            data: 'subtotal',
                            name: 'subtotal'
                        },
                        {
                            data: 'gst',
                            name: 'gst'
                        },
                        {
                            data: 'gst_amount',
                            name: 'gst_amount'
                        },
                        {
                            data: 'grand_total',
                            name: 'grand_total'
                        },
                        // {
                        //     data: 'invoicesitems',
                        //     name: 'invoicesitems'
                        // },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                        },
                    ]
                });
            }
        });




        function change_status(object) {
            var id = $(object).data("id");

            if (confirm('Are you sure?')) {
                $.ajax({
                    "url": "{!! route('invoice.delete') !!}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response.status);
                        if (response.status == 200) {
                            datatable.ajax.reload();
                            toastr.success('Delete Successfully', 'Success');
                        } else {
                            toastr.error('Failed to delete', 'Error');
                        }
                    }
                });
            }
        }

        @php
            $message = session('message');
        @endphp

        @if ($message)
            toastr.success('{{ $message }}', 'Success');
        @endif
    </script>
@endsection
