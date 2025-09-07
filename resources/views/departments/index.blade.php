@extends('layouts.master')

@section('content')
<div class="container-fluid">
     <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Departments</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Expert Power Glass Ind</a></li>
                        <li class="breadcrumb-item active">Depart inventory</li>
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
        <h4 class="card-title mb-0">Depart inventory</h4>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal">
            <i class="fas fa-plus me-1"></i> Add Department
        </button>
    </div>
</div>

                <div class="card-body">
                    <table id="deptTable" class="table datatables">
                        <thead class="table-light">
                            <tr>
                                <th>Sr No.</th>
                                <th>Department Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="departform">
            <input type="hidden" id="deptId">
            <div class="mb-3">
                <label class="form-label">Department name</label>
                <input type="text" class="form-control" id="reqNo" required />
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelEditBtn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success d-none" id="updateItemBtn">Update</button>
        <button type="button" class="btn btn-dark" id="addItemBtn">Save</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // DataTable load
    const deptTable = $('#deptTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("departments.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { 
                data: 'id', 
                name: 'action',
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                render: function (data) {
                    return `
                        <a class="las la-pen text-secondary fs-18 editBtn" data-id="${data}"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteBtn" data-id="${data}"></a>
                    `;
                }
            }
        ]
    });

    function showAlert(message, type = 'success') {
        Swal.fire({
            icon: type,
            title: type.charAt(0).toUpperCase() + type.slice(1),
            html: message,
            timer: 3000,
            toast: true,
            position: 'center',
            showConfirmButton: false,
        });
    }

    function resetForm() {
        $('#reqNo').val('');
        $('#deptId').val('');
        $('#addItemBtn').removeClass('d-none');
        $('#updateItemBtn').addClass('d-none');
        $('.modal-title').text('Add Department');
    }

    // Add Department
    $('#addItemBtn').on('click', function (e) {
        e.preventDefault();
        const name = $('#reqNo').val().trim();
        if (!name) {
            showAlert('Department name is required.', 'warning');
            return;
        }

        $.post('{{ route("departments.store") }}', { name })
        .done(() => {
            showAlert('Department added successfully!');
            resetForm();
            $('#departmentModal').modal('hide');
            deptTable.ajax.reload();
        })
        .fail(xhr => {
            const errorMessage = xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || xhr.statusText;
            showAlert('Error: ' + errorMessage, 'error');
        });
    });

    // Edit Department
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(`/departments/${id}`, function (res) {
            if (res.success) {
                $('#reqNo').val(res.data.name);
                $('#deptId').val(res.data.id);
                $('#addItemBtn').addClass('d-none');
                $('#updateItemBtn').removeClass('d-none');
                $('.modal-title').text('Edit Department');
                $('#departmentModal').modal('show');
            } else {
                showAlert(res.message, 'error');
            }
        }).fail(() => {
            showAlert('Error fetching department.', 'error');
        });
    });

    // Update Department
    $('#updateItemBtn').on('click', function () {
        const id = $('#deptId').val();
        const name = $('#reqNo').val().trim();
        if (!name) {
            showAlert('Department name is required.', 'warning');
            return;
        }

        $.ajax({
            url: `/departments/${id}`,
            method: 'PUT',
            data: { name },
            success: function () {
                showAlert('Department updated successfully!');
                resetForm();
                $('#departmentModal').modal('hide');
                deptTable.ajax.reload();
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || xhr.statusText;
                showAlert('Error: ' + errorMessage, 'error');
            }
        });
    });

    // Delete Department
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: `Delete this department?`,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/departments/${id}`,
                    method: 'DELETE',
                    success: function () {
                        showAlert('Department deleted successfully!');
                        deptTable.ajax.reload();
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to delete department.';
                        showAlert(msg, 'error');
                    }
                });
            }
        });
    });

    // Reset modal when closed
    $('#departmentModal').on('hidden.bs.modal', function () {
        resetForm();
    });
});
</script>
@endsection
