@extends('layouts.master')

@section('content')
<div class="container">
    <h2 class="mb-4">User Roles Management</h2>

    <button class="btn btn-primary mb-3" id="createNewRole">Add User Role</button>

    <table class="table table-bordered" id="rolesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th width="150px">Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="roleModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roleForm">
                @csrf
                <input type="hidden" id="role_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="mb-3">
                        <label>User</label>
                        <input type="text" class="form-control" id="userSearchInput" placeholder="Search user by name or email">
                        <div id="userSearchResults" class="list-group mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role_id" id="role_id_select" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="saveBtn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    // DataTable Init
    let table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user.roles.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'user_name', name: 'user_name' },
            { data: 'role_name', name: 'role_name' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable:false, searchable:false },
        ]
    });

    // Open Modal
    $('#createNewRole').click(function () {
        $('#roleForm').trigger("reset");
        $('#role_id').val('');
        $('#user_id').val('');
        $('#userSearchResults').empty();
        $('#modalTitle').text("Add User Role");
        $('#roleModal').modal('show');
    });

    // Save / Update
    $('#roleForm').on('submit', function(e){
        e.preventDefault();

        let id = $('#role_id').val();
        let formData = $(this).serialize();

        let url = id ? `/user-roles/${id}` : "{{ route('user.roles.store') }}";
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function(res){
                $('#roleModal').modal('hide');
                table.ajax.reload();
                alert(res.success);
            },
            error: function(xhr){
                console.log(xhr.responseJSON);
                alert("Validation Error: " + JSON.stringify(xhr.responseJSON.errors));
            }
        });
    });

    // Edit
    $(document).on('click','.editRole',function(){
        let id = $(this).data('id');
        $.get('/user-roles/'+id, function(data){
            $('#role_id').val(data.id);
            $('#user_id').val(data.user_id);
            $('#userSearchInput').val(data.user_name);
            $('#role_id_select').val(data.role_id);
            $('#status').val(data.status);
            $('#modalTitle').text('Edit User Role');
            $('#roleModal').modal('show');
        });
    });

    // Delete
    $(document).on('click','.deleteRole',function(){
        if(!confirm('Are you sure?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: '/user-roles/'+id,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(res){
                table.ajax.reload();
                alert(res.success);
            }
        });
    });

    // Search user
    $('#userSearchInput').on('keyup', function(){
        let query = $(this).val();
        if(query.length < 2){
            $('#userSearchResults').empty();
            return;
        }
        $.get("{{ route('user-role.search') }}", {q: query}, function(res){
            let html = '';
            res.results.forEach(user => {
                html += `<a href="#" class="list-group-item list-group-item-action selectUser" data-id="${user.id}" data-name="${user.text}">${user.text}</a>`;
            });
            $('#userSearchResults').html(html);
        });
    });

    // Select User
    $(document).on('click','.selectUser', function(e){
        e.preventDefault();
        $('#user_id').val($(this).data('id'));
        $('#userSearchInput').val($(this).data('name'));
        $('#userSearchResults').empty();
    });
});
</script>
@endsection
