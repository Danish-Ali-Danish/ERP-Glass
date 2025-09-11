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
    <form id="itemForm">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">Add Items</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label>Item Code</label>
                            <select class="form-control" id="itemCodeSelect">
                                <option value="">Search Item Code...</option>
                            </select>
                            <small class="text-danger d-none" id="errorItemCode"></small>
                        </div>

                        <div class="col-md-4">
                            <label>Description</label>
                            <select class="form-control" id="itemDescSelect">
                                <option value="">Search Description...</option>
                            </select>
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
@endsection

@section('scripts')
<link href="{{ asset('assets/libs/mobius1-selectr/selectr.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/libs/mobius1-selectr/selectr.min.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    let items = [];
    let editIndex = null;

    function clearErrors(){
        document.querySelectorAll('small.text-danger').forEach(el=> { el.classList.add('d-none'); el.textContent=''; });
        document.querySelectorAll('input, select').forEach(el=> el.classList.remove('is-invalid'));
    }

    function renderItemsTable(){
        let tbody = document.querySelector('#itemsTable tbody');
        tbody.innerHTML = '';
        items.forEach((item,index)=>{
            tbody.innerHTML += `
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
            `;
        });
    }

    const itemCodeSelect = new Selectr(document.querySelector('#itemCodeSelect'), { searchable:true, placeholder:'Search Item Code...', allowDeselect:true });
    const itemDescSelect = new Selectr(document.querySelector('#itemDescSelect'), { searchable:true, placeholder:'Search Description...', allowDeselect:true });

    function fetchItems(query, type, callback){
        if(!query) return callback([]);
        $.ajax({
            url: "{{ route('requisitions.items.search') }}",
            type: "GET",
            dataType: 'json',
            data: { q: query, type: type },
            success: function(res){
                let results = res.results.map(i => ({
                    value: i.id,
                    text: type === 'code' ? i.item_code : i.description,
                    item_code: i.item_code,
                    description: i.description,
                    uom: i.uom
                }));
                callback(results);
            }
        });
    }

    // Search events
    itemCodeSelect.on('search', function(searchText){
        fetchItems(searchText,'code', function(results){
            itemCodeSelect.clear();
            results.forEach(i => itemCodeSelect.add(i));
        });
    });

    itemDescSelect.on('search', function(searchText){
        fetchItems(searchText,'desc', function(results){
            itemDescSelect.clear();
            results.forEach(i => itemDescSelect.add(i));
        });
    });

    // Sync dropdowns
    function syncDropdowns(selectedOption){
        if(selectedOption){
            itemCodeSelect.setValue(selectedOption.value);
            itemDescSelect.setValue(selectedOption.value);
            document.querySelector('#itemUom').value = selectedOption.uom;
        }
    }

    itemCodeSelect.on('change', function(){
        let selected = itemCodeSelect.getValue(true);
        if(!selected) return;
        let data = itemCodeSelect.options.find(o => o.value == selected);
        syncDropdowns(data);
    });

    itemDescSelect.on('change', function(){
        let selected = itemDescSelect.getValue(true);
        if(!selected) return;
        let data = itemDescSelect.options.find(o => o.value == selected);
        syncDropdowns(data);
    });

    // Item form submit
    document.querySelector('#itemForm').addEventListener('submit', function(e){
        e.preventDefault(); clearErrors();
        const codeId = itemCodeSelect.getValue(true);
        const descId = itemDescSelect.getValue(true);
        const selectedOption = itemCodeSelect.options.find(o=>o.value==codeId) || itemDescSelect.options.find(o=>o.value==descId);
        const qty = document.querySelector('#itemQty').value;
        let valid = true;
        if(!selectedOption){ document.querySelector('#errorItemCode').classList.remove('d-none'); document.querySelector('#errorItemCode').textContent='Select an item'; valid=false; }
        if(!qty || qty<=0){ document.querySelector('#itemQty').classList.add('is-invalid'); document.querySelector('#errorItemQty').classList.remove('d-none'); document.querySelector('#errorItemQty').textContent='Quantity required'; valid=false; }
        if(!valid) return;

        const newItem = { id:selectedOption.value, item_code:selectedOption.item_code, description:selectedOption.description, uom:selectedOption.uom, quantity:parseFloat(qty) };
        let exists = items.find((x,i)=> x.id==newItem.id && i!==editIndex);
        if(exists){ Swal.fire('Error','This item already exists','error'); return; }

        if(editIndex!==null){ items[editIndex]=newItem; editIndex=null; document.querySelector('#addItemBtn').textContent='Add Item'; }
        else items.push(newItem);

        renderItemsTable();
        this.reset(); itemCodeSelect.setValue(null); itemDescSelect.setValue(null); document.querySelector('#itemUom').value='';
    });

    // Edit/Delete items
    document.addEventListener('click', function(e){
        if(e.target.matches('.editItemBtn')){
            let index = parseInt(e.target.dataset.index);
            let item = items[index];
            document.querySelector('#itemQty').value = item.quantity;
            document.querySelector('#itemUom').value = item.uom;
            itemCodeSelect.setValue(item.id);
            itemDescSelect.setValue(item.id);
            editIndex = index;
            document.querySelector('#addItemBtn').textContent='Update Item';
        }
        if(e.target.matches('.deleteItemBtn')){
            let index = parseInt(e.target.dataset.index);
            Swal.fire({ title:'Are you sure?', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete it!' }).then((result)=>{
                if(result.isConfirmed){ items.splice(index,1); renderItemsTable(); }
            });
        }
    });

    // Save requisition
    document.querySelector('#saveReqBtn').addEventListener('click', function(){
        clearErrors(); let valid=true;
        if(!document.querySelector('#reqNo').value){ document.querySelector('#reqNo').classList.add('is-invalid'); document.querySelector('#errorReqNo').classList.remove('d-none'); document.querySelector('#errorReqNo').textContent='Req No required'; valid=false; }
        if(!document.querySelector('#date').value){ document.querySelector('#date').classList.add('is-invalid'); document.querySelector('#errorDate').classList.remove('d-none'); document.querySelector('#errorDate').textContent='Date required'; valid=false; }
        if(!document.querySelector('#reqDate').value){ document.querySelector('#reqDate').classList.add('is-invalid'); document.querySelector('#errorReqDate').classList.remove('d-none'); document.querySelector('#errorReqDate').textContent='Req Date required'; valid=false; }
        if(!document.querySelector('#requestedBy').value){ document.querySelector('#requestedBy').classList.add('is-invalid'); document.querySelector('#errorRequestedBy').classList.remove('d-none'); document.querySelector('#errorRequestedBy').textContent='Requested By required'; valid=false; }
        if(!document.querySelector('#departmentId').value){ document.querySelector('#departmentId').classList.add('is-invalid'); document.querySelector('#errorDepartment').classList.remove('d-none'); document.querySelector('#errorDepartment').textContent='Department required'; valid=false; }
        if(!document.querySelector('#projectName').value){ document.querySelector('#projectName').classList.add('is-invalid'); document.querySelector('#errorProject').classList.remove('d-none'); document.querySelector('#errorProject').textContent='Project Name required'; valid=false; }
        if(items.length===0){ Swal.fire('Error','Add at least one item','error'); valid=false; }
        if(!valid) return;

        $.ajax({
            url: "{{ route('requisitions.store') }}",
            type:"POST",
            data:{
                _token:'{{ csrf_token() }}',
                req_no:document.querySelector('#reqNo').value,
                date:document.querySelector('#date').value,
                req_date:document.querySelector('#reqDate').value,
                requested_by:document.querySelector('#requestedBy').value,
                department_id:document.querySelector('#departmentId').value,
                project_name:document.querySelector('#projectName').value,
                remarks:document.querySelector('#remarks').value,
                delivery_date:document.querySelector('#deliveryDate').value,
                items:items
            },
            success:function(res){
                Swal.fire('Success',res.message,'success');
                items=[];
                renderItemsTable();
                document.querySelector('#projectForm').reset();
                document.querySelector('#itemForm').reset();
                itemCodeSelect.setValue(null);
                itemDescSelect.setValue(null);
                document.querySelector('#itemUom').value='';
                document.querySelector('#addItemBtn').textContent='Add Item';
            },
            error:function(err){
                console.log(err.responseJSON? err.responseJSON.errors: err);
                Swal.fire('Error','Validation failed','error');
            }
        });

    });

});
</script>
@endsection
