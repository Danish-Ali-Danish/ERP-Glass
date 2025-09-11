@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Items</h4>
                <div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
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
                        <button class="btn btn-primary" id="openItemModal">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
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
                                <th>Remarks</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="itemForm">
            <input type="hidden" id="itemId">
            <div class="mb-3">
                <label class="form-label">Item Code</label>
                <input type="text" class="form-control" id="itemCode" readonly />
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" class="form-control" id="description" required />
            </div>
            <div class="mb-3">
                <label class="form-label">UOM</label>
                <input type="text" class="form-control" id="uom" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <input type="text" class="form-control" id="remarks" />
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
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_code', name: 'item_code' },
            { data: 'description', name: 'description' },
            { data: 'uom', name: 'uom' },
            { data: 'remarks', name: 'remarks' },
            { data: 'id', orderable: false, searchable: false, className: 'text-center',
                render: function (data) {
                    return `
                        <a href="javascript:void(0)" class="las la-pen text-secondary fs-18 editBtn" data-id="${data}"></a>
                        <a href="javascript:void(0)" class="las la-trash-alt text-secondary fs-18 deleteBtn ms-2" data-id="${data}"></a>
                    `;
                }
            }
        ]
    });

    function resetForm() {
        $('#itemId').val('');
        $('#description').val('');
        $('#uom').val('');
        $('#remarks').val('');
        $('#itemCode').val('');
        $('#addItemBtn').removeClass('d-none');
        $('#updateItemBtn').addClass('d-none');
        $('.modal-title').text('Add Item');
    }

    // Open Modal & Get Next Code
    $('#openItemModal').on('click', function () {
        $.get('{{ route("items.get-code") }}', function (res) {
            if (res.success) $('#itemCode').val(res.code);
            $('#itemModal').modal('show');
        });
    });

    // Add Item
    $('#addItemBtn').on('click', function () {
        $.post('{{ route("items.store") }}', {
            description: $('#description').val(),
            uom: $('#uom').val(),
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
                $('#itemId').val(res.data.id);
                $('#itemCode').val(res.data.item_code);
                $('#description').val(res.data.description);
                $('#uom').val(res.data.uom);
                $('#remarks').val(res.data.remarks);
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
