@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">New GRN</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Expert Power Glass Ind</a></li>
                        <li class="breadcrumb-item"><a href="{{route('grns.index')}}">All GRNs</a></li>
                        <li class="breadcrumb-item active">New GRN</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- GRN Details -->
    <form id="grnForm">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">GRN Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label">LPO No</label>
                            <input type="text" class="form-control" id="lpoNo" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplierName" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">LPO Date</label>
                            <input type="date" class="form-control" id="lpoDate" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier Code</label>
                            <input type="text" class="form-control" id="supplierCode" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" id="requestedBy" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier INV/DN No</label>
                            <input type="text" class="form-control" id="invNo" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" id="department" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier INV/DN Date</label>
                            <input type="date" class="form-control" id="invDate" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Items Form -->
    <form id="itemForm">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">Add GRN Items</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>Item Code</label>
                            <input type="text" class="form-control" id="itemCode">
                        </div>
                        <div class="col-md-4">
                            <label>Item Description</label>
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
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary" id="addItemBtn">Add</button>
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
                <div class="card-header">GRN Items List</div>
                <div class="card-body">
                    <table class="table" id="itemsTable">
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

    <!-- Footer: Prepared By / Checked By / Approved By -->
    <div class="row mt-3">
        <div class="col-md-4">
            <label>Prepared By</label>
            <input type="text" class="form-control" id="preparedBy">
        </div>
        <div class="col-md-4">
            <label>Checked By</label>
            <input type="text" class="form-control" id="checkedBy">
        </div>
        <div class="col-md-4">
            <label>Approved By</label>
            <input type="text" class="form-control" id="approvedBy">
        </div>
    </div>

    <button class="btn btn-success mt-3" id="saveGrnBtn">Save GRN</button>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    let items = [];
    let editIndex = null;

    function renderItemsTable(){
        let tbody = $('#itemsTable tbody');
        tbody.html('');

        items.forEach((item,index)=>{
            tbody.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>${item.code}</td>
                    <td>${item.description}</td>
                    <td>${item.uom}</td>
                    <td>${item.quantity}</td>
                    <td class="text-center">
                        <a class="las la-pen editItemBtn" data-index="${index}"></a>
                        <a class="las la-trash-alt deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });
    }

    // Add/Update Item
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        let code = $('#itemCode').val();
        let desc = $('#itemDesc').val();
        let qty = parseInt($('#itemQty').val()) || 0;
        let uom = $('#itemUom').val();

        if(!desc || qty<=0 || !uom){
            Swal.fire('Error','Fill all item fields correctly','error');
            return;
        }

        let newItem = { code:code, description:desc, quantity:qty, uom:uom };

        if(editIndex !== null){
            items[editIndex] = newItem;
            editIndex = null;
            $('#addItemBtn').text('Add');
        } else {
            items.push(newItem);
        }

        renderItemsTable();
        $('#itemForm')[0].reset();
    });

    // Edit Item
    $(document).on('click','.editItemBtn', function(){
        let index = $(this).data('index');
        let item = items[index];
        $('#itemCode').val(item.code);
        $('#itemDesc').val(item.description);
        $('#itemQty').val(item.quantity);
        $('#itemUom').val(item.uom);

        editIndex = index;
        $('#addItemBtn').text('Update');
        document.getElementById("addItemBtn").scrollIntoView({ 
            behavior: "smooth", block: "center" 
        });
    });

    // Delete Item
    $(document).on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((res)=>{
            if(res.isConfirmed){
                items.splice(index,1);
                renderItemsTable();
            }
        });
    });

    // Save GRN
    $('#saveGrnBtn').on('click', function(){
        if(items.length==0){
            Swal.fire('Error','Add at least one item','error');
            return;
        }

        let data = {
            _token: '{{ csrf_token() }}',
            lpo_no: $('#lpoNo').val(),
            supplier_name: $('#supplierName').val(),
            lpo_date: $('#lpoDate').val(),
            supplier_code: $('#supplierCode').val(),
            requested_by: $('#requestedBy').val(),
            inv_no: $('#invNo').val(),
            department: $('#department').val(),
            inv_date: $('#invDate').val(),
            project_name: $('#projectName').val(),
            prepared_by: $('#preparedBy').val(),
            checked_by: $('#checkedBy').val(),
            approved_by: $('#approvedBy').val(),
            items: items,
        };

        $.ajax({
            url: "{{ route('grns.store') }}",
            type: "POST",
            data: data,
            success: function(res){
                Swal.fire('Success', res.message, 'success');
                items = [];
                renderItemsTable();
                $('#grnForm')[0].reset();
                $('#itemForm')[0].reset();
                $('#addItemBtn').text('Add');
            },
            error: function(err){
                Swal.fire('Error','Validation failed','error');
            }
        });
    });
});
</script>
@endsection
