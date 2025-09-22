@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Edit Work Order</h4>
                <div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('workorders.index')}}">All Work Orders</a></li>
                        <li class="breadcrumb-item active">Edit Work Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- General Information -->
    <form id="updateWorkOrderForm">
        @csrf
        @method('PUT')
        <input type="hidden" id="workOrderId" value="{{ $workorder->id }}">

        <div class="row g-3">
            <div class="col-12">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">General Information</div>
                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" value="{{ $workorder->customer_name }}" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mobile No</label>
                            <input type="text" class="form-control" id="customerMobile" value="{{ $workorder->customer_mobile }}" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" value="{{ $workorder->date->format('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Work Order No</label>
                            <input type="text" class="form-control" id="workOrderNo" value="{{ $workorder->work_order_no }}" readonly />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Work Order Type</label>
                            <input type="text" class="form-control" id="workOrderType" value="{{ $workorder->work_order_type }}" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Customer Ref</label>
                            <input type="text" class="form-control" id="customerRef" value="{{ $workorder->customer_ref }}" />
                        </div>

                        <!-- Processes -->
                        <div class="col-12">
                            <label class="form-label d-block">Processes</label>
                            <div class="row">
                                @php
                                    $processes = [
                                        "CUTTING","GRINDING & SEAMING","POLISHING & BEVELING",
                                        "DRILLING & SANDBLASTING","TEMPERING & BENDING",
                                        "DOUBLE GLAZING","LAMINATION","SMART FILM","PACKING"
                                    ];
                                @endphp
                                @foreach($processes as $process)
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input processes"
                                                value="{{ $process }}" id="process_{{ $loop->index }}"
                                                {{ in_array($process, $workorder->processes ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="process_{{ $loop->index }}">{{ $process }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- End Processes -->

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
                    <div class="card-header">Add / Edit Items</div>
                    <div class="card-body row g-3">

                        <div class="col-md-2">
                            <label>Outer W (mm)</label>
                            <input type="number" step="0.01" class="form-control" id="outerW">
                        </div>
                        <div class="col-md-2">
                            <label>Outer H (mm)</label>
                            <input type="number" step="0.01" class="form-control" id="outerH">
                        </div>
                        <div class="col-md-2">
                            <label>Inner W (mm)</label>
                            <input type="number" step="0.01" class="form-control" id="innerW">
                        </div>
                        <div class="col-md-2">
                            <label>Inner H (mm)</label>
                            <input type="number" step="0.01" class="form-control" id="innerH">
                        </div>
                        <div class="col-md-1">
                            <label>Qty</label>
                            <input type="number" class="form-control" id="qty">
                        </div>
                        <div class="col-md-1">
                            <label>SQM</label>
                            <input type="number" step="0.01" class="form-control" id="sqm" readonly>
                        </div>
                        <div class="col-md-1">
                            <label>LM</label>
                            <input type="number" step="0.01" class="form-control" id="lm" readonly>
                        </div>
                        <div class="col-md-1">
                            <label>Chargeable SQM</label>
                            <input type="number" step="0.01" class="form-control" id="chargeableSqm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Instructions</label>
                            <input type="text" class="form-control" id="instructions">
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
                <div class="card-header">Items List</div>
                <div class="card-body">
                    <table class="table datatables" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Outer W</th>
                                <th>Outer H</th>
                                <th>Inner W</th>
                                <th>Inner H</th>
                                <th>Qty</th>
                                <th>SQM</th>
                                <th>LM</th>
                                <th>Chargeable SQM</th>
                                <th>Amount</th>
                                <th>Instructions</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workorder->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->outer_w }}</td>
                                <td>{{ $item->outer_h }}</td>
                                <td>{{ $item->inner_w }}</td>
                                <td>{{ $item->inner_h }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->sqm }}</td>
                                <td>{{ $item->lm }}</td>
                                <td>{{ $item->chargeable_sqm }}</td>
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->instructions }}</td>
                                <td class="text-center">
                                    <a class="las la-pen text-secondary fs-18 me-2 editItemBtn" data-index="{{ $loop->index }}"></a>
                                    <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="{{ $loop->index }}"></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-success mt-3" id="updateWorkOrderBtn">Update Work Order</button>
    <a href="{{ route('workorders.index') }}" class="btn btn-secondary mt-3">Cancel</a>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
    let items = @json($workorder->items);
    let editIndex = null;

    // --- Auto Calculate fields ---
    function calculateFields(){
        let outerW = parseFloat($('#outerW').val()) || 0;
        let outerH = parseFloat($('#outerH').val()) || 0;
        let qty    = parseFloat($('#qty').val()) || 0;

        let sqm = ((outerW * outerH) / 1000000) * qty;
        let lm  = ((2 * (outerW + outerH)) / 1000) * qty;
        let chargeableSqm = sqm < 1 && sqm > 0 ? 1 : sqm;
        let amount = chargeableSqm * 100; // demo rate

        $('#sqm').val(sqm.toFixed(2));
        $('#lm').val(lm.toFixed(2));
        $('#chargeableSqm').val(chargeableSqm.toFixed(2));
        $('#amount').val(amount.toFixed(2));
    }
    $('#outerW,#outerH,#qty').on('input', calculateFields);

    // --- Render Items Table ---
    function renderItemsTable(){
        let tbody = $('#itemsTable tbody');
        tbody.html('');
        items.forEach((item,index)=>{
            tbody.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>${item.outer_w}</td>
                    <td>${item.outer_h}</td>
                    <td>${item.inner_w}</td>
                    <td>${item.inner_h}</td>
                    <td>${item.qty}</td>
                    <td>${item.sqm}</td>
                    <td>${item.lm}</td>
                    <td>${item.chargeable_sqm}</td>
                    <td>${item.amount}</td>
                    <td>${item.instructions}</td>
                    <td class="text-center">
                        <a class="las la-pen text-secondary fs-18 me-2 editItemBtn" data-index="${index}"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });
    }
    renderItemsTable();

    // --- Add / Update Item ---
    $('#itemForm').submit(function(e){
        e.preventDefault();
        let newItem = {
            outer_w: $('#outerW').val(),
            outer_h: $('#outerH').val(),
            inner_w: $('#innerW').val(),
            inner_h: $('#innerH').val(),
            qty: $('#qty').val(),
            sqm: $('#sqm').val(),
            lm: $('#lm').val(),
            chargeable_sqm: $('#chargeableSqm').val(),
            amount: $('#amount').val(),
            instructions: $('#instructions').val(),
        };

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

    // --- Edit Item ---
    $('#itemsTable').on('click','.editItemBtn',function(){
        let index = $(this).data('index');
        let item = items[index];
        $('#outerW').val(item.outer_w);
        $('#outerH').val(item.outer_h);
        $('#innerW').val(item.inner_w);
        $('#innerH').val(item.inner_h);
        $('#qty').val(item.qty);
        $('#sqm').val(item.sqm);
        $('#lm').val(item.lm);
        $('#chargeableSqm').val(item.chargeable_sqm);
        $('#amount').val(item.amount);
        $('#instructions').val(item.instructions);
        editIndex = index;
        $('#addItemBtn').text('Update');
    });

    // --- Delete Item ---
    $('#itemsTable').on('click','.deleteItemBtn',function(){
        let index = $(this).data('index');
        Swal.fire({
            title:'Are you sure?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#3085d6',
            confirmButtonText:'Yes, delete it!'
        }).then(result=>{
            if(result.isConfirmed){
                items.splice(index,1);
                renderItemsTable();
            }
        });
    });

    // --- Update Work Order ---
    $('#updateWorkOrderBtn').click(function(){
        if(items.length === 0){
            Swal.fire({icon:'error',title:'Error',text:'Add at least one item'});
            return;
        }

        let processes = [];
        $('.processes:checked').each(function(){
            processes.push($(this).val());
        });

        let data = {
            _token:'{{ csrf_token() }}',
            _method:'PUT',
            customer_name:$('#customerName').val(),
            customer_mobile:$('#customerMobile').val(),
            date:$('#date').val(),
            work_order_no:$('#workOrderNo').val(),
            work_order_type:$('#workOrderType').val(),
            customer_ref:$('#customerRef').val(),
            processes:processes,
        };

        items.forEach((item,index)=>{
            data[`items[${index}][outer_w]`] = item.outer_w;
            data[`items[${index}][outer_h]`] = item.outer_h;
            data[`items[${index}][inner_w]`] = item.inner_w;
            data[`items[${index}][inner_h]`] = item.inner_h;
            data[`items[${index}][qty]`] = item.qty;
            data[`items[${index}][sqm]`] = item.sqm;
            data[`items[${index}][lm]`] = item.lm;
            data[`items[${index}][chargeable_sqm]`] = item.chargeable_sqm;
            data[`items[${index}][amount]`] = item.amount;
            data[`items[${index}][instructions]`] = item.instructions;
        });

        $.ajax({
            url: "/workorders/"+$('#workOrderId').val(),
            type: "POST",
            data: data,
            success: function(res){
                Swal.fire({icon:'success',title:'Success',text:res.message})
                .then(()=>window.location.href="{{ route('workorders.index') }}");
            },
            error: function(err){
                Swal.fire({icon:'error',title:'Error',text:'Validation failed'});
            }
        });
    });
});
</script>
@endsection
