@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Items</h4>
                <div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Items</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center m-3">
                    <div class="card-header p-0 border-0 bg-transparent">
                        <h4 class="card-title mb-0">Items Inventory</h4>
                    </div>

                    <div>
                        @if(hasPermission('items.create'))
                        <button class="btn btn-primary" id="openItemModal">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <table id="itemTable" class="table datatables">
                        <thead class="table-light">
                            <tr>
                                <th>Sr No.</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Type</th>
                                <th>Remarks</th>
                                <th class="text-center">
                                    @if(hasPermission('items.edit') || hasPermission('items.destroy'))
                                    Action
                                    @endif
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= Item Modal ================= -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- modal-lg for better layout -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Item Code</label>
                            <input type="text" class="form-control" id="itemCode" readonly />
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" required />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">UOM</label>
                            <select class="form-select" id="uom" required>
                                <option value="">-- Select UOM --</option>
                                <option value="NOS">NOS</option>
                                <option value="SET">SET</option>
                                <option value="PCS">PCS</option>
                                <option value="PKT">PKT</option>
                                <option value="SQM">SQM</option>
                                <option value="PAIR">PAIR</option>
                                <option value="KG">KG</option>
                                <option value="MTR">MTR</option>
                                <option value="LTR">LTR</option>
                                <option value="LM">LM</option>
                                <option value="BOX">BOX</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Size</label>
                            <input type="text" class="form-control" id="size" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control" id="remarks" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success d-none" id="updateItemBtn">Update</button>
                <button type="button" class="btn btn-dark" id="addItemBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const table = $('#itemTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("items.index") }}',
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_code', name: 'item_code' },
            { data: 'description', name: 'description' },
            { data: 'uom', name: 'uom' },
            { data: 'size', name: 'size' },
            { data: 'color', name: 'color' },
            { data: 'type', name: 'type' },
            { data: 'remarks', name: 'remarks' },
            { 
                data: 'id', 
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                render: function (data) {
                    return `
                        @if(hasPermission('items.edit'))
                        <a class="las la-pen text-secondary fs-18 editBtn" data-id="${data}"></a>
                        @endif
                        @if(hasPermission('items.destroy'))
                        <a class="las la-trash-alt text-secondary fs-18 deleteBtn" data-id="${data}"></a>
                        @endif
                    `;
                }
            }
        ]
    });

    function resetForm() {
        $('#itemId, #description, #uom, #remarks, #itemCode, #size, #color, #type').val('');
        $('#addItemBtn').removeClass('d-none');
        $('#updateItemBtn').addClass('d-none');
        $('.modal-title').text('Add Item');
    }

    // Open Modal + Auto Generate Code
    $('#openItemModal').on('click', function () {
        $.get('{{ route("items.index") }}?getCode=1', function (res) {
            if (res.success) $('#itemCode').val(res.code);
            $('#itemModal').modal('show');
        });
    });

    // Add Item
    $('#addItemBtn').on('click', function () {
        $.post('{{ route("items.store") }}', {
            description: $('#description').val(),
            uom: $('#uom').val(),
            size: $('#size').val(),
            color: $('#color').val(),
            type: $('#type').val(),
            remarks: $('#remarks').val()
        }).done((res) => {
            Swal.fire('Success', res.message, 'success');
            $('#itemModal').modal('hide');
            table.ajax.reload();
            resetForm();
        }).fail((xhr) => {
            Swal.fire('Error', xhr.responseJSON.message || 'Failed to save', 'error');
        });
    });

    // Edit Item
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(`/items/${id}`, function (res) {
            if (res.success) {
                const item = res.data;
                $('#itemId').val(item.id);
                $('#itemCode').val(item.item_code);
                $('#description').val(item.description);
                $('#uom').val(item.uom);
                $('#size').val(item.size);
                $('#color').val(item.color);
                $('#type').val(item.type);
                $('#remarks').val(item.remarks);
                $('#addItemBtn').addClass('d-none');
                $('#updateItemBtn').removeClass('d-none');
                $('.modal-title').text('Edit Item');
                $('#itemModal').modal('show');
            }
        });
    });

    // Update Item
    $('#updateItemBtn').on('click', function () {
        const id = $('#itemId').val();
        $.ajax({
            url: `/items/${id}`,
            method: 'PUT',
            data: {
                description: $('#description').val(),
                uom: $('#uom').val(),
                size: $('#size').val(),
                color: $('#color').val(),
                type: $('#type').val(),
                remarks: $('#remarks').val()
            },
            success: function (res) {
                Swal.fire('Success', res.message, 'success');
                $('#itemModal').modal('hide');
                table.ajax.reload();
                resetForm();
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON.message || 'Failed to update', 'error');
            }
        });
    });

    // Delete Item
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this item?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/items/${id}`,
                    method: 'DELETE',
                    success: function (res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to delete', 'error');
                    }
                });
            }
        });
    });

    $('#itemModal').on('hidden.bs.modal', function () {
        resetForm();
    });
});
</script>
@endsection
