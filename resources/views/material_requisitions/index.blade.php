@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Material Requisitions</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reqModal">
                <i class="fas fa-plus"></i> Add Requisition
            </button>
        </div>
        <div class="card-body">
            <table id="reqTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Req No</th>
                        <th>Req Date</th>
                        <th>Department</th>
                        <th>Project</th>
                        <th>Requested By</th>
                        <th>Delivery Date</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="reqForm">
            @csrf
            <input type="hidden" id="req_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Requisition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Req No</label>
                        <input type="text" name="req_no" id="req_no" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Req Date</label>
                        <input type="date" name="req_date" id="req_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Project Name</label>
                        <input type="text" name="project_name" id="project_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Requested By</label>
                        <input type="text" name="requested_by" id="requested_by" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="saveBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    let table = $('#reqTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('requisitions.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'req_no', name: 'req_no' },
            { data: 'req_date', name: 'req_date' },
            { data: 'department', name: 'department' },
            { data: 'project_name', name: 'project_name' },
            { data: 'requested_by', name: 'requested_by' },
            { data: 'delivery_date', name: 'delivery_date' },
            { data: 'remarks', name: 'remarks' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Save
    $('#reqForm').on('submit', function(e){
        e.preventDefault();
        let id = $('#req_id').val();
        let url = id ? "/requisitions/"+id : "{{ route('requisitions.store') }}";
        let type = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res){
                $('#reqModal').modal('hide');
                $('#reqForm')[0].reset();
                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function(){
        let id = $(this).data('id');
        $.get("/requisitions/"+id+"/edit", function(data){
            $('#req_id').val(data.id);
            $('#req_no').val(data.req_no);
            $('#req_date').val(data.req_date);
            $('#department_id').val(data.department_id);
            $('#project_name').val(data.project_name);
            $('#requested_by').val(data.requested_by);
            $('#delivery_date').val(data.delivery_date);
            $('#remarks').val(data.remarks);
            $('#reqModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "This will be deleted permanently!",
            icon: "warning",
            showCancelButton: true,
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/requisitions/"+id,
                    type: "DELETE",
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res){
                        table.ajax.reload();
                        Swal.fire('Deleted', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
