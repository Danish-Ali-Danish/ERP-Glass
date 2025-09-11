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
                            <input type="text" class="form-control" value="REQ-00010002" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Req Date</label>
                            <input type="date" class="form-control" name="req_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" name="requested_by">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control" name="remarks">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Delivery Required Date</label>
                            <input type="date" class="form-control" name="delivery_date">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Items -->
        <div class="card shadow-sm">
            <div class="card-header">Add Items</div>
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Item Code</label>
                    <input type="text" id="itemCodeInput" class="form-control" placeholder="Search Item Code...">
                    <div id="itemCodeDropdown" class="dropdown-menu"></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" id="itemDescInput" class="form-control" placeholder="Search Description...">
                    <div id="itemDescDropdown" class="dropdown-menu"></div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">UOM</label>
                    <input type="text" id="itemUom" class="form-control" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" id="itemQty" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-1">
                    <button type="button" id="addItemBtn" class="btn btn-primary w-100">Add</button>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let itemStore = {};
    let selectedItem = null;
    let itemsList = [];

    // Search Items AJAX
    function searchItems(query, type) {
        if (query.length < 2) return;
        $.ajax({
            url: "{{ route('items.index') }}",
            data: { search: query },
            success: function (res) {
                let list = "";
                res.forEach(item => {
                    itemStore[item.id] = item;
                    list += `<button class="dropdown-item" type="button"
                                data-id="${item.id}"
                                data-code="${item.item_code}"
                                data-desc="${item.description}"
                                data-uom="${item.uom}">
                                ${type === 'code' ? item.item_code : item.description}
                             </button>`;
                });
                if (type === "code") {
                    $("#itemCodeDropdown").html(list).addClass("show");
                } else {
                    $("#itemDescDropdown").html(list).addClass("show");
                }
            }
        });
    }

    // Sync both dropdowns + fill fields
    function handleSelect(el) {
        let id = el.data("id");
        let item = itemStore[id];
        if (!item) return;
        selectedItem = item;

        $("#itemCodeInput").val(item.item_code);
        $("#itemDescInput").val(item.description);
        $("#itemUom").val(item.uom);
    }

    // Event bindings
    $("#itemCodeInput").on("input", function () {
        searchItems($(this).val(), "code");
    });
    $("#itemDescInput").on("input", function () {
        searchItems($(this).val(), "desc");
    });

    $("#itemCodeDropdown, #itemDescDropdown").on("click", ".dropdown-item", function () {
        handleSelect($(this));
        $(".dropdown-menu").removeClass("show");
        $("#itemQty").focus();
    });

    // Enter navigation
    $("#itemCodeInput").on("keydown", function(e){
        if(e.key === "Enter"){ e.preventDefault(); $("#itemDescInput").focus(); }
    });
    $("#itemDescInput").on("keydown", function(e){
        if(e.key === "Enter"){ e.preventDefault(); $("#itemQty").focus(); }
    });
    $("#itemQty").on("keydown", function(e){
        if(e.key === "Enter"){ e.preventDefault(); $("#addItemBtn").click(); }
    });

    // Add Item
    $("#addItemBtn").on("click", function () {
        if (!selectedItem) return alert("Please select an item");
        let qty = parseInt($("#itemQty").val());
        if (isNaN(qty) || qty <= 0) return alert("Enter valid qty");

        let exists = itemsList.find(i => i.id === selectedItem.id);
        if (exists) {
            exists.qty += qty;
        } else {
            itemsList.push({ ...selectedItem, qty });
        }
        renderTable();
        resetInputs();
    });

    // Render Table
    function renderTable() {
        let rows = "";
        itemsList.forEach((item, i) => {
            rows += `<tr>
                <td>${i+1}</td>
                <td>${item.item_code}</td>
                <td>${item.description}</td>
                <td>${item.uom}</td>
                <td>${item.qty}</td>
                <td>
                    <button class="btn btn-sm btn-danger deleteItem" data-id="${item.id}">Del</button>
                </td>
            </tr>`;
        });
        $("#itemsTable tbody").html(rows);
    }

    // Delete
    $("#itemsTable").on("click", ".deleteItem", function () {
        let id = $(this).data("id");
        itemsList = itemsList.filter(i => i.id !== id);
        renderTable();
    });

    // Reset inputs
    function resetInputs() {
        $("#itemCodeInput").val("");
        $("#itemDescInput").val("");
        $("#itemUom").val("");
        $("#itemQty").val(1);
        selectedItem = null;
        $("#itemCodeInput").focus();
    }
});
</script>
@endpush
