    @extends('layouts.master')
    @section('content')
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">New LPO</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('lpos.index')}}">All LPOs</a></li>
                            <li class="breadcrumb-item active">New LPO</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Information -->
        <form id="lpoForm">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">General Information</div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="supplierName" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contactPerson" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">LPO No</label>
                                <input type="text" class="form-control" id="lpoNo" value="{{ $lpoNo }}" readonly />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Contact No</label>
                                <input type="text" class="form-control" id="contactNo" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PI No</label>
                                <input type="text" class="form-control" id="piNo" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier TRN</label>
                                <input type="text" class="form-control" id="supplierTrn" required />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="address"  rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Items Form -->
        <form id="itemForm" class="mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">Add Items</div>
                        <div class="card-body row g-3">

                            <!-- Description with Dropdown -->
                            <div class="col-md-4 position-relative">
                                <label>Description</label>
                                <input type="text" class="form-control" id="itemDescInput" placeholder="Search Description...">
                                <div class="dropdown-menu search-dropdown"></div>
                                <small class="text-danger d-none" id="errorItemDesc"></small>
                            </div>

                            <div class="col-md-2">
                                <label>Area (SQM)</label>
                                <input type="number" step="0.01" class="form-control" id="itemArea">
                            </div>

                            <div class="col-md-2">
                                <label>Quantity in SQM</label>
                                <input type="number" class="form-control" id="itemQty">
                            </div>

                            <div class="col-md-1">
                                <label>UOM</label>
                                <input type="text" class="form-control" id="itemUom" readonly>
                            </div>

                            <div class="col-md-2">
                                <label>Unit Price</label>
                                <input type="number" step="0.01" class="form-control" id="itemUnitPrice">
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
                                    <th>Description</th>
                                    <th>Area (SQM)</th>
                                    <th>Qty</th>
                                    <th>UOM</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="row mt-3">
            <div class="col-md-4 offset-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-1">
                            <strong>Sub Total:</strong>
                            <span id="subTotal">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <strong>VAT (5%):</strong>
                            <span id="vat">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <strong>NET Total:</strong>
                            <span id="netTotal">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-success mt-3" id="saveLpoBtn">Save LPO</button>
    </div>

    <style>
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

        // Function to render items table
        function renderItemsTable(){
            let tbody = $('#itemsTable tbody');
            tbody.html('');
            let subTotal = 0;

            items.forEach((item,index)=>{
                let total = item.quantity * item.unit_price;
                subTotal += total;
                tbody.append(`
                    <tr>
                        <td>${index+1}</td>
                        <td>${item.description}</td>
                        <td>${item.area}</td>
                        <td>${item.quantity}</td>
                        <td>${item.uom}</td>
                        <td>${item.unit_price.toFixed(2)}</td>
                        <td>${total.toFixed(2)}</td>
                        <td class="text-center">
                            <a class="las la-pen text-secondary fs-18 me-2 editItemBtn" data-index="${index}"></a>
                            <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="${index}"></a>
                        </td>
                    </tr>
                `);
            });

            let vat = subTotal * 0.05;
            let netTotal = subTotal + vat;

            $('#subTotal').text(subTotal.toFixed(2));
            $('#vat').text(vat.toFixed(2));
            $('#netTotal').text(netTotal.toFixed(2));
        }

        // --- Validation Functions ---
        function validateGeneralInfo(){
            let valid = true;
            const fields = [
                {selector:'#supplierName', msg:'Supplier Name is required'},
                {selector:'#date', msg:'Date is required'},
                {selector:'#contactPerson', msg:'Contact Person is required'},
                {selector:'#address', msg:'Address is required'}
            ];
            fields.forEach(f=>{
                $(f.selector).removeClass('is-invalid');
                $(f.selector).siblings('.invalid-feedback').remove();
                if(!$(f.selector).val() || $(f.selector).val().trim()===''){
                    $(f.selector).addClass('is-invalid');
                    $(f.selector).after(`<div class="invalid-feedback">${f.msg}</div>`);
                    valid=false;
                }
            });
            if(!valid) Swal.fire({icon:'error',title:'Error',html:'Please fill all required fields',confirmButtonColor:'#3085d6'});
            return valid;
        }

        function validateItemForm(){
            let valid = true;
            $('#itemDescInput,#itemQty,#itemUnitPrice').removeClass('is-invalid');
            $('#itemDescInput,#itemQty,#itemUnitPrice').siblings('.invalid-feedback').remove();

            if(!selectedItem){ 
                $('#itemDescInput').addClass('is-invalid'); 
                $('#itemDescInput').after('<div class="invalid-feedback">Select a valid item</div>'); 
                valid=false; 
            }
            if(!$('#itemQty').val() || parseFloat($('#itemQty').val())<=0){ 
                $('#itemQty').addClass('is-invalid'); 
                $('#itemQty').after('<div class="invalid-feedback">Enter quantity</div>'); 
                valid=false; 
            }
            if(!$('#itemUnitPrice').val() || parseFloat($('#itemUnitPrice').val())<=0){ 
                $('#itemUnitPrice').addClass('is-invalid'); 
                $('#itemUnitPrice').after('<div class="invalid-feedback">Enter unit price</div>'); 
                valid=false; 
            }

            if(!valid) Swal.fire({icon:'error',title:'Error',html:'Please fill all required item fields',confirmButtonColor:'#3085d6'});
            return valid;
        }

        // --- Dropdown Setup ---
        function setupDropdown(inputSelector){
            let input = $(inputSelector);
            let dropdown = input.siblings('.search-dropdown');
            let activeIndex = -1;

            function showResults(query){
                if(!query || query.trim()==='') query='*';
                $.get("{{ route('lpos.items.search') }}",{q:query}, function(res){
                    dropdown.html(''); activeIndex=-1;
                    if(res.results.length){
                        res.results.forEach(r=>{
                            dropdown.append(`<a class="dropdown-item" data-id="${r.id}" data-desc="${r.description}" data-uom="${r.uom}">${r.description}</a>`);
                        });
                        dropdown.addClass('show');
                    } else dropdown.removeClass('show');
                });
            }

            input.on('focus input',()=>showResults(input.val()));

            input.on('keydown',function(e){
                let itemsList = dropdown.find('.dropdown-item');
                if(!itemsList.length) return;

                if(e.key==='ArrowDown'){ e.preventDefault(); activeIndex=(activeIndex+1)%itemsList.length; itemsList.removeClass('active'); $(itemsList[activeIndex]).addClass('active'); }
                else if(e.key==='ArrowUp'){ e.preventDefault(); activeIndex=(activeIndex-1+itemsList.length)%itemsList.length; itemsList.removeClass('active'); $(itemsList[activeIndex]).addClass('active'); }
                else if(e.key==='Enter'){ e.preventDefault(); if(activeIndex>=0){ $(itemsList[activeIndex]).trigger('click'); dropdown.removeClass('show'); } }
            });

            dropdown.on('click','.dropdown-item', function(){
                selectedItem={id:$(this).data('id'), description:$(this).data('desc'), uom:$(this).data('uom')};
                $('#itemDescInput').val(selectedItem.description); $('#itemUom').val(selectedItem.uom); dropdown.removeClass('show');
            });

            $(document).on('click', function(e){
                if(!$(e.target).closest(inputSelector+', .search-dropdown').length) dropdown.removeClass('show');
            });
        }
        setupDropdown('#itemDescInput');

        // --- Add / Update Item ---
        $('#itemForm').submit(function(e){
            e.preventDefault();
            if(!validateItemForm()) return;

            let area=parseFloat($('#itemArea').val())||0;
            let qty=parseFloat($('#itemQty').val());
            let unitPrice=parseFloat($('#itemUnitPrice').val());

            let newItem={...selectedItem, area:area, quantity:qty, unit_price:unitPrice};

            if(editIndex!==null){ items[editIndex]=newItem; editIndex=null; $('#addItemBtn').text('Add Item'); }
            else{ if(items.find(i=>i.id===newItem.id)){ Swal.fire({icon:'error',title:'Error',text:'This item already exists',confirmButtonColor:'#3085d6'}); return; } items.push(newItem); }

            renderItemsTable();
            $('#itemForm')[0].reset(); $('#itemUom').val(''); selectedItem=null;
        });

        // --- Edit / Delete ---
        $('#itemsTable').on('click','.editItemBtn',function(){
            let index=$(this).data('index'); let item=items[index];
            $('#itemDescInput').val(item.description); $('#itemArea').val(item.area); $('#itemQty').val(item.quantity);
            $('#itemUom').val(item.uom); $('#itemUnitPrice').val(item.unit_price); selectedItem=item; editIndex=index; $('#addItemBtn').text('Update Item');
        });

        $('#itemsTable').on('click','.deleteItemBtn',function(){
            let index=$(this).data('index');
            Swal.fire({title:'Are you sure?', icon:'warning', showCancelButton:true, confirmButtonColor:'#3085d6', confirmButtonText:'Yes, delete it!'})
            .then(result=>{ if(result.isConfirmed){ items.splice(index,1); renderItemsTable(); }});
        });

        // --- Save LPO ---
        $('#saveLpoBtn').click(function(){
            if(!validateGeneralInfo()) return;
            if(items.length===0){ Swal.fire({icon:'error',title:'Error',text:'Add at least one item',confirmButtonColor:'#3085d6'}); return; }

            $.post("{{ route('lpos.store') }}",{
                _token:'{{ csrf_token() }}',
                supplier_name:$('#supplierName').val(),
                date:$('#date').val(),
                contact_person:$('#contactPerson').val(),
                contact_no:$('#contactNo').val(),
                pi_no:$('#piNo').val(),
                supplier_trn:$('#supplierTrn').val(),
                address:$('#address').val(),
                items:items,
                sub_total:$('#subTotal').text(),
                vat:$('#vat').text(),
                net_total:$('#netTotal').text(),
            },function(res){
                Swal.fire({icon:'success',title:'Success',text:res.message,confirmButtonColor:'#3085d6'}).then(()=>location.reload());
            }).fail(function(){ Swal.fire({icon:'error',title:'Error',text:'Validation failed',confirmButtonColor:'#3085d6'}); });
        });
    });

    </script>

    @endsection
