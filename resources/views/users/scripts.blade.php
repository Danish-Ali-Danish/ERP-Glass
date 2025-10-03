<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {
    let table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.index') }}",
        columns: [
            { data: 'id' },
            { data: 'image', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'address' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    // Add User
    $('#addUserBtn').click(function() {
        $('#userForm')[0].reset();
        $('#previewImage').html('');
        $('#user_id').val('');
        $('#modalTitle').text('Add User');
        $('#passwordField').show();
        $('#userModal').modal('show');
    });

    // Submit Form
    $('#userForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $('#user_id').val();
        let url = id ? '/users/' + id : "{{ route('users.store') }}";
        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#userModal').modal('hide');
                table.ajax.reload();

                Swal.fire({
                    icon: 'success',
                    title: res.success,
                    position: 'center',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON.message,
                    position: 'center'
                });
            }
        });
    });

    // Edit User
    $(document).on('click','.editUser',function(){
        let id = $(this).data('id');
        $.get('/users/'+id, function(user){
            $('#user_id').val(user.id);
            $('#name').val(user.name);
            $('#email').val(user.email);
            $('#phone').val(user.phone);
            $('#address').val(user.address);
            $('#modalTitle').text('Edit User');
            $('#passwordField').hide();

            if(user.image){
                $('#previewImage').html(`<img src="/uploads/users/${user.image}" width="80" class="rounded mt-2">`);
            } else {
                $('#previewImage').html('');
            }

            $('#userModal').modal('show');
        });
    });

    // Delete User
   // Delete User
$(document).on('click','.deleteUser',function(){
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
                url: '/users/'+id,
                type: 'DELETE',
                data: {_token:"{{ csrf_token() }}"},
                success: function(res){
                    table.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: res.success,
                        position: 'center',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || "Something went wrong!",
                        position: 'center'
                    });
                }
            });
        }
    });
});


    // Reset Password
    $(document).on('click','.resetPassword',function(){
        let id = $(this).data('id');
        $('#reset_user_id').val(id);
        $('#resetPasswordForm')[0].reset();
        $('#resetPasswordModal').modal('show');
    });

    $('#resetPasswordForm').submit(function(e){
        e.preventDefault();
        let id = $('#reset_user_id').val();
        let newPass = $('#new_password').val();
        let confirmPass = $('#confirm_password').val();

        if(newPass !== confirmPass){
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match!',
                position: 'center'
            });
            return;
        }

        $.ajax({
            url: '/users/'+id+'/reset-password',
            type: 'PATCH',
            data: {
                _token: "{{ csrf_token() }}",
                new_password: newPass,
                new_password_confirmation: confirmPass
            },
            success: function(res){
                $('#resetPasswordModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: res.success,
                    position: 'center',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    // Show preview when new image selected
    $('#image').on('change', function() {
        let reader = new FileReader();
        reader.onload = (e) => {
            $('#previewImage').html(`<img src="${e.target.result}" width="80" class="rounded mt-2">`);
        };
        reader.readAsDataURL(this.files[0]);
    });

    // Click on small image in datatable to show modal preview
    $(document).on('click', '.userImage', function(){
        let src = $(this).attr('src');
        $('#previewModalImage').attr('src', src);
        $('#imagePreviewModal').modal('show');
    });
});
</script>
