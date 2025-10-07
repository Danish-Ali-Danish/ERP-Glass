@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <form id="quotationForm">
        @csrf
        <div class="card mb-3 shadow-sm">
            <div class="card-header">Quotation Information</div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label>Quotation No</label>
                    <input type="text" id="quoteNo" class="form-control" value="{{ $quoteNo }}" readonly>
                </div>
                <div class="col-md-3">
                    <label>Date</label>
                    <input type="date" id="date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Company Name</label>
                    <input type="text" id="companyName" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Project Name</label>
                    <input type="text" id="projectName" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Location</label>
                    <input type="text" id="location" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Terms & Conditions</label>
                    <textarea id="terms" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </form>

    <form id="itemForm" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-header">Add Items</div>
            <div class="card-body row g-3">
                <div class="col-md-3 position-relative">
                    <label>Description / Code</label>
                    <input type="text" id="itemDescInput" class="form-control" placeholder="Type to search description or type #code...">
                    <div class="dropdown-menu search-dropdown"></div>
                </div>

                <div class="col-md-2">
                    <label>Item Code</label>
                    <input type="text" id="itemCode" class="form-control" readonly>
                </div>

                <div class="col-md-1">
                    <label>UOM</label>
                    <input type="text" id="itemUnit" class="form-control" readonly>
                </div>

                <div class="col-md-1">
                    <label>Size</label>
                    <input type="text" id="itemSize" class="form-control" readonly>
                </div>

                <div class="col-md-1">
                    <label>Color</label>
                    <input type="text" id="itemColor" class="form-control" readonly>
                </div>

                <div class="col-md-1">
                    <label>Type</label>
                    <input type="text" id="itemType" class="form-control" readonly>
                </div>

                <div class="col-md-2">
                    <label>Remarks</label>
                    <input type="text" id="itemRemarks" class="form-control" readonly>
                </div>

                <div class="col-md-2">
                    <label>Quantity</label>
                    <input type="number" id="itemQty" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input type="number" id="itemPrice" class="form-control" step="0.01">
                </div>

                <div class="col-md-2">
                    <label>Total</label>
                    <input type="number" id="itemTotal" class="form-control" readonly>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button id="addItemBtn" class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Quotation Items</div>
        <div class="card-body">
            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>UOM</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Type</th>
                        <th>Remarks</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="text-end">
                <h5>Subtotal: <span id="subtotal">0.00</span></h5>
                <h5>VAT (5%): <span id="vat">0.00</span></h5>
                <h4>Grand Total: <span id="grandtotal">0.00</span></h4>
            </div>
        </div>
    </div>

    <button id="saveQuotationBtn" class="btn btn-success">Save Quotation</button>
</div>

<style>
.search-dropdown { width: 95%; max-height: 220px; overflow-y:auto; }
.position-relative { position: relative; }
.dropdown-item { cursor: pointer; }
</style>
@endsection

@section('scripts')
<script>
$(function(){
    let items = [];
    let editIndex = null;
    let selectedItem = null;

    function recalcTotals(){
        let st = 0;
        items.forEach(i => st += parseFloat(i.total || 0));
        let vat = st * 0.05;
        let gt = st + vat;
        $('#subtotal').text(st.toFixed(2));
        $('#vat').text(vat.toFixed(2));
        $('#grandtotal').text(gt.toFixed(2));
    }

    function renderItemsTable(){
        let tbody = $('#itemsTable tbody').empty();
        items.forEach((it, idx) => {
            tbody.append(`
                <tr>
                    <td>${idx+1}</td>
                    <td>${it.item_code || ''}</td>
                    <td>${it.description || ''}</td>
                    <td>${it.uom || ''}</td>
                    <td>${it.size || ''}</td>
                    <td>${it.color || ''}</td>
                    <td>${it.type || ''}</td>
                    <td>${it.remarks || ''}</td>
                    <td>${it.quantity}</td>
                    <td>${it.unit_price}</td>
                    <td>${parseFloat(it.total).toFixed(2)}</td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="editItemBtn" data-index="${idx}">Edit</a>
                        &nbsp;
                        <a href="javascript:void(0)" class="deleteItemBtn" data-index="${idx}">Delete</a>
                    </td>
                </tr>
            `);
        });
        recalcTotals();
    }

    function resetItemForm(){
        $('#itemForm')[0].reset();
        $('#itemTotal').val('');
        selectedItem = null;
        editIndex = null;
        $('#addItemBtn').text('Add');
    }

    $('#itemQty, #itemPrice').on('input', function(){
        let qty = parseFloat($('#itemQty').val()) || 0;
        let price = parseFloat($('#itemPrice').val()) || 0;
        $('#itemTotal').val((qty * price).toFixed(2));
    });

    // Live search helper: supports searching by code when user types starts with '#' e.g. "#GLS-00010"
    function fetchItems(query, callback){
        if (!query || query.trim().length < 1) return callback([]);
        let type = 'description';
        if (query.startsWith('#')) {
            query = query.substring(1);
            type = 'code';
        }
        $.get("{{ route('items.search') }}", { q: query, type: type })
            .done(function(res){
                callback(res.results || []);
            })
            .fail(function(){ callback([]); });
    }

    function setupDropdown(){
        let $input = $('#itemDescInput');
        let $dropdown = $input.siblings('.search-dropdown');
        let active = -1;

        function show(q){
            fetchItems(q, function(results){
                $dropdown.html('');
                active = -1;
                if (!results.length) { $dropdown.removeClass('show'); return; }
                results.forEach((r,i) => {
                    $dropdown.append(`<a class="dropdown-item" data-index="${i}"
                        data-id="${r.id}"
                        data-code="${r.item_code||''}"
                        data-desc="${r.description||''}"
                        data-uom="${r.uom||''}"
                        data-size="${r.size||''}"
                        data-color="${r.color||''}"
                        data-type="${r.type||''}"
                        data-remarks="${r.remarks||''}"
                        >${r.item_code ? '['+r.item_code+'] ' : ''}${r.description}</a>`);
                });
                $dropdown.addClass('show');
            });
        }

        $input.on('input focus', function(){ show($(this).val()); });

        $input.on('keydown', function(e){
            let opts = $dropdown.find('.dropdown-item');
            if (!opts.length) return;
            if (e.key === 'ArrowDown'){ e.preventDefault(); active = (active+1) % opts.length; opts.removeClass('active'); $(opts[active]).addClass('active'); }
            else if (e.key === 'ArrowUp'){ e.preventDefault(); active = (active-1+opts.length) % opts.length; opts.removeClass('active'); $(opts[active]).addClass('active'); }
            else if (e.key === 'Enter'){ e.preventDefault(); if (active >= 0) $(opts[active]).trigger('click'); }
        });

        $dropdown.on('click', '.dropdown-item', function(){
            selectedItem = {
                id: $(this).data('id'),
                item_code: $(this).data('code'),
                description: $(this).data('desc'),
                uom: $(this).data('uom'),
                size: $(this).data('size'),
                color: $(this).data('color'),
                type: $(this).data('type'),
                remarks: $(this).data('remarks')
            };
            $('#itemCode').val(selectedItem.item_code);
            $('#itemDescInput').val(selectedItem.description);
            $('#itemUnit').val(selectedItem.uom);
            $('#itemSize').val(selectedItem.size);
            $('#itemColor').val(selectedItem.color);
            $('#itemType').val(selectedItem.type);
            $('#itemRemarks').val(selectedItem.remarks);
            $dropdown.removeClass('show');
        });

        $(document).on('click', function(e){
            if (!$(e.target).closest('#itemDescInput, .search-dropdown').length) {
                $dropdown.removeClass('show');
            }
        });
    }

    setupDropdown();

    $('#itemForm').on('submit', function(e){
        e.preventDefault();
        let qty = parseFloat($('#itemQty').val()) || 0;
        let price = parseFloat($('#itemPrice').val()) || 0;
        if (!selectedItem || qty <= 0 || price < 0) {
            Swal.fire('Error','Please select an item and enter qty & price','error'); return;
        }

        let row = {
            item_id: selectedItem.id || null,
            item_code: $('#itemCode').val() || selectedItem.item_code,
            description: $('#itemDescInput').val(),
            uom: $('#itemUnit').val(),
            size: $('#itemSize').val(),
            color: $('#itemColor').val(),
            type: $('#itemType').val(),
            remarks: $('#itemRemarks').val(),
            quantity: qty,
            unit_price: price,
            total: parseFloat((qty * price).toFixed(2))
        };

        if (editIndex !== null) {
            items[editIndex] = row;
            editIndex = null;
            $('#addItemBtn').text('Add');
        } else {
            items.push(row);
        }

        renderItemsTable();
        resetItemForm();
    });

    $('#itemsTable').on('click', '.editItemBtn', function(){
        let idx = $(this).data('index');
        let it = items[idx];
        $('#itemCode').val(it.item_code);
        $('#itemDescInput').val(it.description);
        $('#itemUnit').val(it.uom);
        $('#itemSize').val(it.size);
        $('#itemColor').val(it.color);
        $('#itemType').val(it.type);
        $('#itemRemarks').val(it.remarks);
        $('#itemQty').val(it.quantity);
        $('#itemPrice').val(it.unit_price);
        $('#itemTotal').val(it.total.toFixed(2));
        selectedItem = it;
        editIndex = idx;
        $('#addItemBtn').text('Update');
    });

    $('#itemsTable').on('click', '.deleteItemBtn', function(){
        let idx = $(this).data('index');
        items.splice(idx,1);
        renderItemsTable();
    });

    $('#saveQuotationBtn').on('click', function(){
        if (!items.length) { Swal.fire('Error','Add at least 1 item','error'); return; }

        $.post("{{ route('quotations.store') }}", {
            _token: '{{ csrf_token() }}',
            date: $('#date').val(),
            company_name: $('#companyName').val(),
            project_name: $('#projectName').val(),
            location: $('#location').val(),
            terms: $('#terms').val(),
            items: items
        }).done(function(res){
            Swal.fire('Success', res.message, 'success');
            // reset whole form
            items = []; renderItemsTable(); $('#quotationForm')[0].reset(); resetItemForm();
            $('#subtotal,#vat,#grandtotal').text('0.00');
        }).fail(function(xhr){
            if (xhr.status === 422) {
                let err = xhr.responseJSON.errors;
                let msgs = Object.values(err).map(v => v.join(', ')).join('<br>');
                Swal.fire('Validation Error', msgs, 'error');
            } else {
                Swal.fire('Error','Something went wrong','error');
            }
        });
    });

});
</script>
@endsection
