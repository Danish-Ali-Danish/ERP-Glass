@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <!-- General Info -->
    <form id="quotationForm">
        @csrf
        <input type="hidden" id="quotation_id" value="{{ $quotation->id }}">
        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">Quotation Information</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Quotation No</label>
                            <input type="text" class="form-control" id="quoteNo" value="{{ $quotation->quote_no }}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" value="{{ $quotation->date }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="companyName" value="{{ $quotation->company_name }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" value="{{ $quotation->project_name }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" value="{{ $quotation->location }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea class="form-control" id="terms" rows="2">{{ $quotation->terms }}</textarea>
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
                    <div class="card-header">Add / Update Items</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3 position-relative">
                            <label>Description</label>
                            <input type="text" class="form-control" id="itemDescInput" placeholder="Search Description...">
                            <div class="dropdown-menu search-dropdown"></div>
                        </div>

                        <div class="col-md-2">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="itemUnit" readonly>
                        </div>

                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="itemQty">
                        </div>

                        <div class="col-md-2">
                            <label>Unit Price</label>
                            <input type="number" class="form-control" id="itemPrice" step="0.01">
                        </div>

                        <div class="col-md-2">
                            <label>Total</label>
                            <input type="number" class="form-control" id="itemTotal" readonly>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" id="addItemBtn">Add</button>
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
                <div class="card-header">Quotation Items</div>
                <div class="card-body">
                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th>Qty</th>
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

    <button class="btn btn-success mt-3" id="saveQuotationBtn">Update Quotation</button>

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

    let items = @json($quotation->items);
    let editIndex = null;
    let selectedItem = null;

    function renderItemsTable(){
        let tbody = $('#itemsTable tbody');
        tbody.html('');
        items.forEach((item,index)=>{
            tbody.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>${item.description}</td>
                    <td>${item.unit}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unit_price}</td>
                    <td>${parseFloat(item.total).toFixed(2)}</td>
                    <td class="text-center">
                        <a class="las la-pen text-secondary fs-18 me-2 editItemBtn" data-index="${index}"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });
    }
    renderItemsTable();

    // Auto calculate total
    $('#itemQty, #itemPrice').on('input', function(){
        let qty = parseFloat($('#itemQty').val()) || 0;
        let price = parseFloat($('#itemPrice').val()) || 0;
        $('#itemTotal').val((qty*price).toFixed(2));
    });

    // Live search for items
    function fetchItems(query, callback){
        $.get("{{ route('requisitions.items.search') }}", { q:query, type:'desc' }, function(res){
            callback(res.results || []);
        });
    }

    function setupDropdown(inputSelector){
        let input = $(inputSelector);
        let dropdown = input.siblings('.search-dropdown');
        let activeIndex = -1;

        function showResults(query){
            fetchItems(query, function(results){
                dropdown.html('');
                activeIndex = -1;
                if(results.length){
                    results.forEach((r,i)=>{
                        dropdown.append(`
                            <a class="dropdown-item"
                               data-id="${r.id}"
                               data-code="${r.item_code}"
                               data-desc="${r.description}"
                               data-unit="${r.uom}">
                               ${r.description}
                            </a>`);
                    });
                    dropdown.addClass('show');
                } else {
                    dropdown.removeClass('show');
                }
            });
        }

        input.on('focus input', function(){
            showResults($(this).val());
        });

        dropdown.on('click','.dropdown-item', function(){
            selectedItem = {
                id: $(this).data('id'),
                item_code: $(this).data('code'),
                description: $(this).data('desc'),
                unit: $(this).data('unit')
            };
            $('#itemDescInput').val(selectedItem.description);
            $('#itemUnit').val(selectedItem.unit);
            dropdown.removeClass('show');
        });

        $(document).on('click', function(e){
            if(!$(e.target).closest(inputSelector+', .search-dropdown').length){
                dropdown.removeClass('show');
            }
        });
    }
    setupDropdown('#itemDescInput');

    // Add / Update Item
    $('#itemForm').submit(function(e){
        e.preventDefault();
        let qty = parseFloat($('#itemQty').val());
        let price = parseFloat($('#itemPrice').val());
        let total = qty*price;

        if(!selectedItem && !$('#itemDescInput').val()){
            Swal.fire('Error','Select an item description','error');
            return;
        }
        if(qty<=0 || price<=0){
            Swal.fire('Error','Enter valid quantity and price','error');
            return;
        }

        let newItem = {
            description: selectedItem ? selectedItem.description : $('#itemDescInput').val(),
            unit: $('#itemUnit').val(),
            quantity: qty,
            unit_price: price,
            total: total
        };

        if(editIndex!==null){
            items[editIndex] = newItem;
            editIndex = null;
            $('#addItemBtn').text('Add');
        } else {
            items.push(newItem);
        }

        renderItemsTable();
        $('#itemForm')[0].reset();
        $('#itemTotal').val('');
        selectedItem = null;
    });

    // Edit / Delete
    $('#itemsTable').on('click','.editItemBtn', function(){
        let index = $(this).data('index');
        let item = items[index];
        $('#itemDescInput').val(item.description);
        $('#itemUnit').val(item.unit);
        $('#itemQty').val(item.quantity);
        $('#itemPrice').val(item.unit_price);
        $('#itemTotal').val(item.total.toFixed(2));
        selectedItem = item;
        editIndex = index;
        $('#addItemBtn').text('Update');
    });

    $('#itemsTable').on('click','.deleteItemBtn', function(){
        let index = $(this).data('index');
        items.splice(index,1);
        renderItemsTable();
    });

    // Save update
    $('#saveQuotationBtn').click(function(){
        if(items.length===0){ Swal.fire('Error','Add at least one item','error'); return; }

        $.ajax({
            url: "/quotations/"+$('#quotation_id').val(),
            type: "PUT",
            data: {
                _token: '{{ csrf_token() }}',
                quote_no: $('#quoteNo').val(),
                date: $('#date').val(),
                company_name: $('#companyName').val(),
                project_name: $('#projectName').val(),
                location: $('#location').val(),
                terms: $('#terms').val(),
                items: items
            },
            success: function(res){
                Swal.fire('Success',res.message,'success').then(()=>{
                    window.location.href = "{{ route('quotations.index') }}";
                });
            },
            error: function(xhr){
                Swal.fire('Error','Validation failed','error');
            }
        });
    });

});
</script>
@endsection
