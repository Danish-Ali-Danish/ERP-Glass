@extends('layouts.master')

@section('content')
<div class="container py-4">
    <h2>User Roles Management</h2>

    <button class="btn btn-primary mb-3" id="addRoleBtn">Add Role</button>

    <table class="table datatables " id="rolesTable" style="width:100%"> 
        <thead class="table-light">
            <tr>
                <th>SR.</th>
                <th>User Name</th>
                <th>Designation</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="roleForm">
            @csrf
            <input type="hidden" name="id" id="role_id_hidden">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add User Role</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" id="name" class="form-control mb-2" placeholder="User Name">

                    <select name="role_id" id="role_id" class="form-control mb-2">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}"> {{ $role->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" id="status" class="form-control mb-2">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="saveBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
    let table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user-roles.index') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
    { data: 'role' }, // yahan role_id hata ke role use karo
            { data: 'status' },
            { data: 'action', orderable: false, searchable: false ,className: 'text-center'     }
        ]
    });

    // Add Role
    $('#addRoleBtn').click(function() {
        $('#roleForm')[0].reset();
        $('#role_id_hidden').val('');
        $('#modalTitle').text('Add User Role');
        $('#roleModal').modal('show');
    });

    // Submit Form
    $('#roleForm').submit(function(e) {
        e.preventDefault();
        let id = $('#role_id_hidden').val();
        let url = id ? '/user-roles/' + id : "{{ route('user-roles.store') }}";

        let formData = $(this).serialize();
        if(id){
            formData += '&_method=PUT'; // PUT ko simulate karo
        }

        $.ajax({
            url: url,
            type: 'POST', // hamesha POST use karo
            data: formData,
            success: function(res) {
                $('#roleModal').modal('hide');
                table.ajax.reload();
                Swal.fire({ icon: 'success', title: res.success, timer: 2000, showConfirmButton: false });
            }
        });
    });

    // Edit
    $(document).on('click','.editRole',function(){
        let id = $(this).data('id');
        $.get('/user-roles/'+id, function(user){
            $('#role_id_hidden').val(user.id);
            $('#name').val(user.name);
            $('#role_id').val(user.role_id);
            $('#status').val(user.status);
            $('#modalTitle').text('Edit User Role');
            $('#roleModal').modal('show');
        });
    });

    // Delete
    $(document).on('click','.deleteRole',function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/user-roles/'+id,
                    type: 'POST',
                    data: {_method:'DELETE', _token:"{{ csrf_token() }}"},
                    success: function(res){
                        table.ajax.reload();
                        Swal.fire({ icon: 'success', title: res.success, timer: 2000, showConfirmButton: false });
                    }
                });
            }
        });
    });
});
</script>
@endsection