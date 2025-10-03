<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="userForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="user_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add User</h5>
        </div>
        <div class="modal-body">
          <input type="text" name="name" id="name" class="form-control mb-2" placeholder="Name">
          <input type="email" name="email" id="email" class="form-control mb-2" placeholder="Email">
          <input type="text" name="phone" id="phone" class="form-control mb-2" placeholder="Phone">
          <input type="text" name="address" id="address" class="form-control mb-2" placeholder="Address">

          <!-- PASSWORD ONLY FOR ADD, NOT EDIT -->
          <div id="passwordField">
            <input type="password" name="password" id="password" class="form-control mb-2" placeholder="Password">
          </div>

          <input type="file" name="image" id="image" class="form-control mb-2">
          <div id="previewImage" class="mt-2"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="saveBtn">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0 shadow-none">
      <div class="modal-body text-center">
        <img id="previewModalImage" src="" class="img-fluid  rounded shadow" height="300px" alt="User Image">
      </div>
    </div>
  </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="resetPasswordForm">
      @csrf
      <input type="hidden" id="reset_user_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reset Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label for="new_password" class="pb-2">New Password</label>
          <input type="password" id="new_password" name="new_password" class="form-control mb-2" placeholder="New Password">
          <label for="confirm_password" class="pb-2">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control mb-2" placeholder="Confirm Password">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Reset</button>
        </div>
      </div>
    </form>
  </div>
</div>
