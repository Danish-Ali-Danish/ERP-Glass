@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <!-- General Info -->
    <form id="projectForm">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">General Information</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Req No</label>
                            <input type="text" class="form-control" id="reqNo" required value="{{ $reqNo }}" readonly />
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
                            <input type="text" class="form-control" id="remarks" />
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
    <form id="itemForm" class="mb-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">Add Items</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3 position-relative">
                            <label>Item Code</label>
                            <input type="text" class="form-control" id="itemCodeInput" placeholder="Search Item Code...">
                            <div class="dropdown-menu search-dropdown"></div>
                            <small class="text-danger d-none" id="errorItemCode"></small>
                        </div>

                        <div class="col-md-4 position-relative">
                            <label>Description</label>
                            <input type="text" class="form-control" id="itemDescInput" placeholder="Search Description...">
                            <div class="dropdown-menu search-dropdown"></div>
                            <small class="text-danger d-none" id="errorItemDesc"></small>
                        </div>

                        <div class="col-md-2">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="itemUom" readonly>
                        </div>

                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="itemQty">
                            <small class="text-danger d-none" id="errorItemQty"></small>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
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

    <button class="btn btn-success mt-3" id="saveReqBtn">Save Requisition</button>

</div>

<style>
/* Dropdown menu width aligned with input */
.search-dropdown {
    width: 90%;
    max-height: 200px;
    overflow-y: auto;
}
.position-relative { position: relative; }
.dropdown-item { cursor: pointer; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function(){

    let items = [];
    let editIndex = null;
    let selectedItem = null;

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

    function fetchItems(query, type, callback){
        $.get("{{ route('requisitions.items.search') }}", { q:query, type:type }, function(res){
            callback(res.results || []);
        });
    }

    function setupDropdown(inputSelector, type){
        let input = $(inputSelector);
        let dropdown = input.siblings('.search-dropdown');
        let activeIndex = -1;

        function showResults(query){
            fetchItems(query, type, function(results){
                dropdown.html('');
                activeIndex = -1;
                if(results.length){
                    results.forEach((r,i)=>{
                        dropdown.append(`
                            <a class="dropdown-item" 
                               data-id="${r.id}" 
                               data-code="${r.item_code}" 
                               data-desc="${r.description}" 
                               data-uom="${r.uom}">
                               ${type==='code'?r.item_code:r.description}
                            </a>`);
                    });
                    dropdown.addClass('show');
                } else {
                    dropdown.removeClass('show');
                }
            });
        }

        // focus par bhi dropdown show karo
        input.on('focus', function(){
            let query = $(this).val();
            showResults(query);
        });

        // typing par bhi search karo
        input.on('input', function(){
            let query = $(this).val();
            showResults(query);
        });

        // keyboard navigation
        input.on('keydown', function(e){
            let items = dropdown.find('.dropdown-item');
            if(!items.length) return;

            if(e.key === 'ArrowDown'){
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                items.removeClass('active');
                $(items[activeIndex]).addClass('active');
            } else if(e.key === 'ArrowUp'){
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                items.removeClass('active');
                $(items[activeIndex]).addClass('active');
            } else if(e.key === 'Enter'){
                e.preventDefault();
                if(activeIndex>=0){
                    $(items[activeIndex]).trigger('click');
                    dropdown.removeClass('show');
                }
            }
        });

        // item select
        dropdown.on('click','.dropdown-item', function(){
            selectedItem = {
                id: $(this).data('id'),
                item_code: $(this).data('code'),
                description: $(this).data('desc'),
                uom: $(this).data('uom')
            };
            $('#itemCodeInput').val(selectedItem.item_code);
            $('#itemDescInput').val(selectedItem.description);
            $('#itemUom').val(selectedItem.uom);
            dropdown.removeClass('show');
        });

        // bahar click par dropdown band
        $(document).on('click', function(e){
            if(!$(e.target).closest(inputSelector+', .search-dropdown').length){
                dropdown.removeClass('show');
            }
        });
    }

    setupDropdown('#itemCodeInput','code');
    setupDropdown('#itemDescInput','desc');

    // Add / Update Item
    $('#itemForm').submit(function(e){
        e.preventDefault();
        let qty = parseFloat($('#itemQty').val());
        if(!selectedItem){ Swal.fire('Error','Select an item','error'); return; }
        if(!qty || qty<=0){ Swal.fire('Error','Enter quantity','error'); return; }

        let newItem = {...selectedItem, quantity: qty};

        if(editIndex!==null){
            items[editIndex] = newItem;
            editIndex = null;
            $('#addItemBtn').text('Add Item');
        } else {
            if(items.find(i=>i.id===newItem.id)){ Swal.fire('Error','This item already exists','error'); return; }
            items.push(newItem);
        }

        renderItemsTable();
        $('#itemForm')[0].reset();
        $('#itemUom').val('');
        selectedItem = null;
    });

    // Edit / Delete Item
    $('#itemsTable').on('click','.editItemBtn', function(){
        let index = $(this).data('index');
        let item = items[index];
        $('#itemQty').val(item.quantity);
        $('#itemCodeInput').val(item.item_code);
        $('#itemDescInput').val(item.description);
        $('#itemUom').val(item.uom);
        selectedItem = item;
        editIndex = index;
        $('#addItemBtn').text('Update Item');
    });

    $('#itemsTable').on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        Swal.fire({
            title:'Are you sure?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete it!'
        }).then(result=>{
            if(result.isConfirmed){
                items.splice(index,1);
                renderItemsTable();
            }
        });
    });

    // Save Requisition
    $('#saveReqBtn').click(function(){
        if(items.length===0){ Swal.fire('Error','Add at least one item','error'); return; }

        $.post("{{ route('requisitions.store') }}", {
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
        }, function(res){
            Swal.fire('Success',res.message,'success');
            items=[];
            renderItemsTable();
            $('#projectForm')[0].reset();
            $('#itemForm')[0].reset();
            $('#itemUom').val('');
            $('#addItemBtn').text('Add Item');
        }).fail(function(err){
            Swal.fire('Error','Validation failed','error');
        });
    });

});
</script>
@endsection
