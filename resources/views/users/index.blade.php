@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 1200px;">

    <!-- ================= HEADER ================= -->
    <header class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-2 mb-md-0">
                <h1 class="display-6 fw-bold text-dark">User Management</h1>
                <p class="text-muted mb-0">Manage system users and their information</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </header>

    <!-- ================= MAIN CARD ================= -->
    <div class="card shadow-sm">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <span class="fw-bold">Users List</span>
            <button class="btn btn-primary btn-sm" id="addUserBtn">
                <i class="las la-plus"></i> Add User
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table  datatables mb-0 " id="usersTable" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ========== Include User Form ========== -->
@include('users.form')
@endsection

@section('scripts')
@include('users.scripts')
@endsection
