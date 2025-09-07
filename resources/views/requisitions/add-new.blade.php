@extends('layouts.master')
@section('content')
<div class="container-fluid">


     <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">New Requisitions</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Expert Power Glass Ind</a></li>
                        <li class="breadcrumb-item"><a href="{{route('requisitions.index')}}">All Requisitions</a></li>
                        <li class="breadcrumb-item active">New Requisitions</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>   

    <!-- Project / General Info Form -->
    <form id="projectForm">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header ">General Information</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Req No</label>
                            <input type="text" class="form-control" id="reqNo" required />
                            <small class="text-danger d-none" id="errorReqNo"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" required />
                            <small class="text-danger d-none" id="errorDate"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Req Date</label>
                            <input type="date" class="form-control" id="reqDate" required />
                            <small class="text-danger d-none" id="errorReqDate"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" id="requestedBy" required />
                            <small class="text-danger d-none" id="errorRequestedBy"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="departmentId" class="form-control" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger d-none" id="errorDepartment"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" required />
                            <small class="text-danger d-none" id="errorProject"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Remarks</label>
                            <input type="textarea" class="form-control" id="remarks" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Delivery Required Date</label>
                            <input type="date" class="form-control" id="deliveryDate" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Item Form -->
    <form id="itemForm">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header ">Add Items</div>
                    <div class="card-body row g-3">
                        <div class="col-md-2">
                            <label>Item Code</label>
                            <input type="number" class="form-control" id="itemCode">
                            <small class="text-danger d-none" id="errorItemCode"></small>
                        </div>
                        <div class="col-md-4">
                            <label>Description</label>
                            <input type="text" class="form-control" id="itemDesc">
                            <small class="text-danger d-none" id="errorItemDesc"></small>
                        </div>
                        <div class="col-md-2">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="itemUom">
                            <small class="text-danger d-none" id="errorItemUom"></small>
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="itemQty">
                            <small class="text-danger d-none" id="errorItemQty"></small>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" id="addItemBtn">Add Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Items Table -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header ">Items List</div>
                <div class="card-body ">
                    <table class="table datatables" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th>Quantity</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-success mt-3" id="saveReqBtn">Save Requisition</button>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    let items = [];
    let editIndex = null; // track editing row

    function clearErrors(){
        $('small.text-danger').addClass('d-none').text('');
        $('input, select').removeClass('is-invalid');
    }

    function renderItemsTable(){
        let tbody = $('#itemsTable tbody');
        tbody.html('');
        items.forEach((item,index)=>{
            tbody.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>${item.item_code}</td>
                    <td>${item.description}</td>
                    <td>${item.uom}</td>
                    <td>${item.quantity}</td>
                    <td class="text-center">
                        <a class="las la-pen text-secondary fs-18 me-2 editItemBtn" data-index="${index}"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });
    }

    // Add / Update Item
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        clearErrors();
        let code = $('#itemCode').val();
        let desc = $('#itemDesc').val();
        let uom = $('#itemUom').val();
        let qty = $('#itemQty').val();
        let valid = true;

        if(!code){ $('#itemCode').addClass('is-invalid'); $('#errorItemCode').removeClass('d-none').text('Item Code required'); valid=false; }
        if(!desc){ $('#itemDesc').addClass('is-invalid'); $('#errorItemDesc').removeClass('d-none').text('Description required'); valid=false; }
        if(!uom){ $('#itemUom').addClass('is-invalid'); $('#errorItemUom').removeClass('d-none').text('UOM required'); valid=false; }
        if(!qty){ $('#itemQty').addClass('is-invalid'); $('#errorItemQty').removeClass('d-none').text('Quantity required'); valid=false; }

        if(!valid) return;

        let newItem = {
            item_code: code,
            description: desc,
            uom: uom,
            quantity: parseInt(qty)
        };

        if(editIndex !== null){
            items[editIndex] = newItem; // update
            editIndex = null;
            $('#addItemBtn').text('Add Item');
        } else {
            items.push(newItem); // add new
        }

        renderItemsTable();
        $('#itemForm')[0].reset();
    });

    // Edit Item
    $(document).on('click','.editItemBtn', function(){
        let index = $(this).data('index');
        let item = items[index];
        $('#itemCode').val(item.item_code);
        $('#itemDesc').val(item.description);
        $('#itemUom').val(item.uom);
        $('#itemQty').val(item.quantity);

        editIndex = index;
        $('#addItemBtn').text('Update Item');
    });

    // Delete Item
    $(document).on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                items.splice(index,1);
                renderItemsTable();
            }
        });
    });

    // Save Requisition
    $('#saveReqBtn').on('click', function(){
        clearErrors();
        let valid = true;
        if(!$('#reqNo').val()){ $('#reqNo').addClass('is-invalid'); $('#errorReqNo').removeClass('d-none').text('Req No required'); valid=false; }
        if(!$('#date').val()){ $('#date').addClass('is-invalid'); $('#errorDate').removeClass('d-none').text('Date required'); valid=false; }
        if(!$('#reqDate').val()){ $('#reqDate').addClass('is-invalid'); $('#errorReqDate').removeClass('d-none').text('Req Date required'); valid=false; }
        if(!$('#requestedBy').val()){ $('#requestedBy').addClass('is-invalid'); $('#errorRequestedBy').removeClass('d-none').text('Requested By required'); valid=false; }
        if(!$('#departmentId').val()){ $('#departmentId').addClass('is-invalid'); $('#errorDepartment').removeClass('d-none').text('Department required'); valid=false; }
        if(!$('#projectName').val()){ $('#projectName').addClass('is-invalid'); $('#errorProject').removeClass('d-none').text('Project Name required'); valid=false; }
        if(items.length==0){ Swal.fire('Error','Add at least one item','error'); valid=false; }

        if(!valid) return;

        let data = {
            _token: '{{ csrf_token() }}',
            req_no: $('#reqNo').val(),
            date: $('#date').val(),
            req_date: $('#reqDate').val(),
            requested_by: $('#requestedBy').val(),
            department_id: $('#departmentId').val(),
            project_name: $('#projectName').val(),
            remarks: $('#remarks').val(),
            delivery_date: $('#deliveryDate').val(),
            items: items
        };

        $.ajax({
            url: "{{ route('requisitions.store') }}",
            type: "POST",
            data: data,
            success: function(res){
                Swal.fire('Success', res.message, 'success');
                items = [];
                renderItemsTable();
                $('#projectForm')[0].reset();
                $('#itemForm')[0].reset();
                $('#addItemBtn').text('Add Item');
            },
            error: function(err){
                Swal.fire('Error','Validation failed','error');
            }
        });
    });
});
</script>
@endsection
