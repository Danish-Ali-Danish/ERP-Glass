@extends('layouts.master')
@section('content')
<div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Edit LPO</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('lpos.index')}}">All LPOs</a></li>
                        <li class="breadcrumb-item active">Edit LPO</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>   

    <!-- General Information -->
    <form id="lpoForm">
        @csrf
        <input type="hidden" id="lpo_id" value="{{ $lpo->id }}">
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">General Information</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplierName" value="{{ $lpo->supplier_name }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" value="{{ $lpo->date }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contactPerson" value="{{ $lpo->contact_person }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">LPO No</label>
                            <input type="text" class="form-control" id="lpoNo" value="{{ $lpo->lpo_no }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No</label>
                            <input type="text" class="form-control" id="contactNo" value="{{ $lpo->contact_no }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PI No</label>
                            <input type="text" class="form-control" id="piNo" value="{{ $lpo->pi_no }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier TRN</label>
                            <input type="text" class="form-control" id="supplierTrn" value="{{ $lpo->supplier_trn }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="address" rows="2">{{ $lpo->address }}</textarea>
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
                            <label>Description</label>
                            <input type="text" class="form-control" id="itemDesc">
                        </div>
                        <div class="col-md-2">
                            <label>Area (SQM)</label>
                            <input type="number" class="form-control" id="itemArea" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="itemQty">
                        </div>
                        <div class="col-md-2">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="itemUom">
                        </div>
                        <div class="col-md-2">
                            <label>Unit Price</label>
                            <input type="number" class="form-control" id="itemUnitPrice" step="0.01">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-auto" id="addItemBtn">Add / Update</button>
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
                    <table class="table" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Area</th>
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
                    <div class="d-flex justify-content-between">
                        <strong>Sub Total:</strong>
                        <span id="subTotal">{{ $lpo->sub_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>VAT:</strong>
                        <span id="vat">{{ $lpo->vat }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>NET Total:</strong>
                        <span id="netTotal">{{ $lpo->net_total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-success mt-3" id="saveLpoBtn">Update LPO</button>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    let items = @json($lpo->items);
    let editIndex = null;
    let selectedItem = null; // dropdown selected item
    let formChanged = false;

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
                    <td>${item.unit_price}</td>
                    <td>${total.toFixed(2)}</td>
                    <td class="text-center">
                        <a class="las la-pen editItemBtn" data-index="${index}" title="Edit"></a>
                        <a class="las la-trash-alt deleteItemBtn" data-index="${index}" title="Delete"></a>
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
    renderItemsTable();

    // --- Dropdown Setup for Item Description ---
    function setupDropdown(inputSelector){
        let input = $(inputSelector);
        input.wrap('<div class="position-relative"></div>');
        let dropdown = $('<div class="dropdown-menu search-dropdown"></div>');
        input.after(dropdown);

        let activeIndex = -1;

        function showResults(query){
            if(!query) query='*';
            $.get("{{ route('lpos.items.search') }}",{q:query}, function(res){
                dropdown.html(''); activeIndex=-1; selectedItem=null;
                if(res.results.length){
                    res.results.forEach(r=>{
                        dropdown.append(`<a class="dropdown-item" data-id="${r.id}" data-desc="${r.description}" data-uom="${r.uom}">${r.description}</a>`);
                    });
                    dropdown.addClass('show');
                } else dropdown.removeClass('show');
            });
        }

        input.on('input focus', ()=>showResults(input.val()));

        input.on('keydown', function(e){
            let itemsList = dropdown.find('.dropdown-item');
            if(!itemsList.length) return;

            if(e.key==='ArrowDown'){ e.preventDefault(); activeIndex=(activeIndex+1)%itemsList.length; itemsList.removeClass('active'); $(itemsList[activeIndex]).addClass('active'); }
            else if(e.key==='ArrowUp'){ e.preventDefault(); activeIndex=(activeIndex-1+itemsList.length)%itemsList.length; itemsList.removeClass('active'); $(itemsList[activeIndex]).addClass('active'); }
            else if(e.key==='Enter'){ 
                e.preventDefault(); 
                if(activeIndex>=0){ $(itemsList[activeIndex]).trigger('click'); dropdown.removeClass('show'); } 
            }
        });

        dropdown.on('click','.dropdown-item', function(){
            selectedItem={id:$(this).data('id'), description:$(this).data('desc'), uom:$(this).data('uom')};
            $('#itemDesc').val(selectedItem.description);
            $('#itemUom').val(selectedItem.uom);
            dropdown.removeClass('show');
        });

        $(document).on('click', function(e){
            if(!$(e.target).closest(inputSelector+', .search-dropdown').length) dropdown.removeClass('show');
        });
    }

    setupDropdown('#itemDesc');

    // --- Add / Update Item ---
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        if(!selectedItem){
            Swal.fire('Error','Please select a valid item','error');
            return;
        }

        let newItem = {
            id: selectedItem.id,
            description: selectedItem.description,
            area: parseFloat($('#itemArea').val()) || 0,
            quantity: parseInt($('#itemQty').val()),
            uom: selectedItem.uom,
            unit_price: parseFloat($('#itemUnitPrice').val())
        };

        if(editIndex !== null){
            items[editIndex] = newItem;
            editIndex = null;
        } else {
            if(items.find(i=>i.id===newItem.id)){
                Swal.fire('Error','This item already exists','error');
                return;
            }
            items.push(newItem);
        }

        $('#itemForm')[0].reset();
        $('#itemUom').val('');
        selectedItem=null;
        formChanged = true;
        renderItemsTable();
    });

    $(document).on('click','.editItemBtn', function(){
        editIndex = $(this).data('index');
        let item = items[editIndex];
        $('#itemDesc').val(item.description);
        $('#itemArea').val(item.area);
        $('#itemQty').val(item.quantity);
        $('#itemUom').val(item.uom);
        $('#itemUnitPrice').val(item.unit_price);
        selectedItem=item;
    });

    $(document).on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        items.splice(index,1);
        formChanged = true;
        renderItemsTable();
    });

    // --- Update LPO ---
    $('#saveLpoBtn').on('click', function(e){
        e.preventDefault();
        if(items.length===0){
            Swal.fire('Error','Add at least one item','error');
            return;
        }

        let data = {
            _token: '{{ csrf_token() }}',
            supplier_name: $('#supplierName').val(),
            date: $('#date').val(),
            contact_person: $('#contactPerson').val(),
            lpo_no: $('#lpoNo').val(),
            contact_no: $('#contactNo').val(),
            pi_no: $('#piNo').val(),
            supplier_trn: $('#supplierTrn').val(),
            address: $('#address').val(),
            sub_total: $('#subTotal').text(),
            vat: $('#vat').text(),
            net_total: $('#netTotal').text()
        };

        items.forEach((item,index)=>{
            data[`items[${index}][id]`] = item.id;
            data[`items[${index}][description]`] = item.description;
            data[`items[${index}][area]`] = item.area;
            data[`items[${index}][quantity]`] = item.quantity;
            data[`items[${index}][uom]`] = item.uom;
            data[`items[${index}][unit_price]`] = item.unit_price;
            data[`items[${index}][total]`] = item.quantity * item.unit_price;
        });

        $.ajax({
            url: "/lpos/"+$('#lpo_id').val(),
            type: "PUT",
            data: data,
            success: function(res){
                formChanged = false;
                Swal.fire('Updated!','LPO updated successfully.','success').then(()=>{ window.location.href="{{ route('lpos.index') }}"; });
            },
            error: function(xhr){
                Swal.fire('Error','Something went wrong','error');
            }
        });
    });
});

</script>
@endsection
