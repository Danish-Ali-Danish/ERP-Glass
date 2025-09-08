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
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Expert Power Glass Ind</a></li>
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
                            <input type="text" class="form-control" id="lpoNo" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No</label>
                            <input type="text" class="form-control" id="contactNo" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PI No</label>
                            <input type="text" class="form-control" id="piNo" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier TRN</label>
                            <input type="text" class="form-control" id="supplierTrn" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="address" rows="2"></textarea>
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
                    <div class="card-header">Add Items</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>Description</label>
                            <input type="text" class="form-control" id="itemDesc">
                        </div>
                        <div class="col-md-2">
                            <label>Area (SQM)</label>
                            <input type="number" step="0.01" class="form-control" id="itemArea">
                        </div>
                        <div class="col-md-2">
                            <label>Quantity in SQM</label>
                            <input type="number" class="form-control" id="itemQty">
                        </div>
                        <div class="col-md-2">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="itemUom">
                        </div>
                        <div class="col-md-2">
                            <label>Unit Price</label>
                            <input type="number" step="0.01" class="form-control" id="itemUnitPrice">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary " id="addItemBtn">Add</button>
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
                    <div class="d-flex justify-content-between">
                        <strong>Sub Total:</strong>
                        <span id="subTotal">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>VAT (5%):</strong>
                        <span id="vat">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>NET Total:</strong>
                        <span id="netTotal">0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-success mt-3" id="saveLpoBtn">Save LPO</button>
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
                        <a class="las la-pen editItemBtn" data-index="${index}"></a>
                        <a class="las la-trash-alt deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });

        // Summary calculation
        let vat = subTotal * 0.05;
        let netTotal = subTotal + vat;

        $('#subTotal').text(subTotal.toFixed(2));
        $('#vat').text(vat.toFixed(2));
        $('#netTotal').text(netTotal.toFixed(2));
    }

    // Add/Update Item
    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        let desc = $('#itemDesc').val();
        let area = parseFloat($('#itemArea').val()) || 0;
        let qty = parseInt($('#itemQty').val()) || 0;
        let uom = $('#itemUom').val();
        let unitPrice = parseFloat($('#itemUnitPrice').val()) || 0;

        if(!desc || qty<=0 || !uom || unitPrice<=0){
            Swal.fire('Error','Fill all item fields correctly','error');
            return;
        }

        let newItem = { description:desc, area:area, quantity:qty, uom:uom, unit_price:unitPrice };

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
        $('#itemDesc').val(item.description);
        $('#itemArea').val(item.area);
        $('#itemQty').val(item.quantity);
        $('#itemUom').val(item.uom);
        $('#itemUnitPrice').val(item.unit_price);

        editIndex = index;
        $('#addItemBtn').text('Update');
            document.getElementById("addItemBtn").scrollIntoView({ 
        behavior: "smooth", 
        block: "center" 
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

    // Save LPO
    $('#saveLpoBtn').on('click', function(){
        if(items.length==0){
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
            items: items,
            sub_total: $('#subTotal').text(),
            vat: $('#vat').text(),
            net_total: $('#netTotal').text(),
        };

        $.ajax({
            url: "{{ route('lpos.store') }}",
            type: "POST",
            data: data,
            success: function(res){
                Swal.fire('Success', res.message, 'success');
                items = [];
                renderItemsTable();
                $('#lpoForm')[0].reset();
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
