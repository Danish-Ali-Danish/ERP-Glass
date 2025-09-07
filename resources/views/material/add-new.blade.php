@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <h4>Material Requisition - Add New</h4>

    <!-- Requisition Form -->
    <form id="reqForm">
        @csrf
        <div class="row g-2">
            <div class="col-md-3">
                <label>Req No</label>
                <input type="text" class="form-control" id="reqNo" required>
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" class="form-control" id="date" required>
            </div>
            <div class="col-md-3">
                <label>Requested By</label>
                <input type="text" class="form-control" id="requestedBy" required>
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select class="form-control" name="department_id" id="departmentId" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Project Name</label>
                <input type="text" class="form-control" id="projectName" required>
            </div>
            <div class="col-md-6">
                <label>Remarks</label>
                <input type="text" class="form-control" id="remarks">
            </div>
        </div>
    </form>

    <hr>

    <!-- Items Form -->
    <form id="itemForm">
        <div class="row g-2">
            <div class="col-md-2">
                <label>Item Code</label>
                <input type="text" class="form-control" id="itemCode">
            </div>
            <div class="col-md-4">
                <label>Description</label>
                <input type="text" class="form-control" id="itemDesc">
            </div>
            <div class="col-md-2">
                <label>UOM</label>
                <input type="text" class="form-control" id="itemUom">
            </div>
            <div class="col-md-2">
                <label>Quantity</label>
                <input type="number" class="form-control" id="itemQty">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary" id="addItemBtn">Add Item</button>
            </div>
        </div>
    </form>

    <hr>

    <!-- Items Table -->
    <table class="table table-bordered mt-2" id="itemsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button class="btn btn-success" id="saveReqBtn">Save Requisition</button>
</div>
@endsection
