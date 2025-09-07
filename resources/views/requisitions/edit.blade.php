@extends('layouts.master')
@section('content')
<div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Edit Requisitions</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Expert Power Glass Ind</a></li>
                        <li class="breadcrumb-item"><a href="{{route('requisitions.index')}}">Expert Power Glass Ind</a></li>
                        <li class="breadcrumb-item active">Edit Requisitions</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>   
    <!-- Project / General Info Form -->
    <form id="projectForm">
        @csrf
        <input type="hidden" id="req_id" value="{{ $requisition->id }}">
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">General Information</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Req No</label>
                            <input type="text" class="form-control" id="reqNo" value="{{ $requisition->req_no }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" value="{{ $requisition->date }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Req Date</label>
                            <input type="date" class="form-control" id="reqDate" value="{{ $requisition->req_date }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" id="requestedBy" value="{{ $requisition->requested_by }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select id="departmentId" class="form-control">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $requisition->department_id==$dept->id?'selected':'' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" value="{{ $requisition->project_name }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control" id="remarks" value="{{ $requisition->remarks }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Delivery Required Date</label>
                            <input type="date" class="form-control" id="deliveryDate" value="{{ $requisition->delivery_date }}">
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
                    <div class="card-header">Add Items</div>
                    <div class="card-body row g-3">
                        <div class="col-md-2">
                            <label>Item Code</label>
                            <input type="number" class="form-control" id="itemCode">
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
                            <button type="submit" class="btn btn-primary w-100" id="addItemBtn">Add / Update Item</button>
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
                <div class="card-header">Items List</div>
                <div class="card-body">
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

    <button class="btn btn-success mt-3" id="saveReqBtn">Update Requisition</button>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function(){
    let items = @json($requisition->items);
    let editIndex = null;
    let formChanged = false;

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
                        <a class="las la-pen text-secondary fs-18 editItemBtn" data-index="${index}" title="Edit"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="${index}" title="Delete"></a>
                    </td>
                </tr>
            `);
        });
    }
    renderItemsTable();

    // Detect form changes
    $('#projectForm input, #projectForm select, #itemForm input').on('change input', function(){
        formChanged = true;
    });

    // Add / Update Item
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        let newItem = {
            item_code: $('#itemCode').val(),
            description: $('#itemDesc').val(),
            uom: $('#itemUom').val(),
            quantity: parseInt($('#itemQty').val())
        };

        if(editIndex !== null){
            items[editIndex] = newItem;
            editIndex = null;
        } else {
            items.push(newItem);
        }

        formChanged = true;
        $('#itemForm')[0].reset();
        renderItemsTable();
    });

    // Edit Item
    $(document).on('click', '.editItemBtn', function(){
        editIndex = $(this).data('index');
        let item = items[editIndex];
        $('#itemCode').val(item.item_code);
        $('#itemDesc').val(item.description);
        $('#itemUom').val(item.uom);
        $('#itemQty').val(item.quantity);
    });

    // Delete Item
    $(document).on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        items.splice(index,1);
        formChanged = true;
        renderItemsTable();
    });

    // 🚨 Intercept page leave (links, refresh, close)
    window.addEventListener("beforeunload", function (e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = ''; // Standard browser message
        }
    });

    // 🚨 Intercept internal navigation clicks
    $(document).on('click', 'a', function(e){
        if(formChanged && !$(this).is('#saveReqBtn')) {
            e.preventDefault();
            let targetUrl = $(this).attr('href');

            Swal.fire({
                title: 'You have unsaved changes!',
                text: 'Do you want to update before leaving?',
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: ' Update',
                denyButtonText: ' Leave Without Saving',
                cancelButtonText: ' Cancel'
            }).then((result)=>{
                if(result.isConfirmed){
                    $('#saveReqBtn').trigger('click'); // save before leaving
                } else if(result.isDenied){
                    formChanged = false; // bypass warning
                    window.location.href = targetUrl;
                }
            });
        }
    });

    // Update Requisition
    $('#saveReqBtn').on('click', function(e){
        e.preventDefault();

        let data = {
            _token: '{{ csrf_token() }}',
            req_no: $('#reqNo').val(),
            date: $('#date').val(),
            req_date: $('#reqDate').val(),
            requested_by: $('#requestedBy').val(),
            department_id: $('#departmentId').val(),
            project_name: $('#projectName').val(),
            remarks: $('#remarks').val(),
            delivery_date: $('#deliveryDate').val()
        };

        items.forEach((item, index)=>{
            data[`items[${index}][item_code]`] = item.item_code;
            data[`items[${index}][description]`] = item.description;
            data[`items[${index}][uom]`] = item.uom;
            data[`items[${index}][quantity]`] = item.quantity;
        });

        $.ajax({
            url: "/requisitions/"+$('#req_id').val(),
            type: "PUT",
            data: data,
            success: function(res){
                formChanged = false;
                Swal.fire('Updated!', 'Requisition updated successfully.', 'success').then(()=>{
                    window.location.href = "{{ route('requisitions.index') }}";
                });
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let msg = Object.values(errors).map(e=>e.join(', ')).join('<br>');
                    Swal.fire('Validation Error', msg, 'error');
                } else {
                    Swal.fire('Error','Something went wrong','error');
                }
            }
        });
    });
});
</script>
@endsection
