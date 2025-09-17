@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">New GRN</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('grns.index') }}">All GRNs</a></li>
                    <li class="breadcrumb-item active">New GRN</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- GRN Form -->
    <form id="grnForm">
        @csrf
        <div class="card mb-3 shadow-sm">
            <div class="card-header">GRN Details</div>
            <div class="card-body row g-3">

                <!-- Auto Generated GRN No -->
                <div class="col-md-3">
                    <label class="form-label">GRN No</label>
                    <input type="text" class="form-control" id="grn_no" name="grn_no" value="{{ $grn_no }}" readonly>
                </div>

                <!-- LPO Dropdown -->
                <div class="col-md-3">
                    <label class="form-label">Select LPO <span class="text-danger">*</span></label>
                    <select class="form-control" id="lpoSelect" name="lpo_id" required>
                        <option value="">Select LPO</option>
                        @foreach($lpos as $lpo)
                        <option value="{{ $lpo->id }}">{{ $lpo->lpo_no }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" class="form-control" id="supplierName" name="supplier_name" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">LPO Date</label>
                    <input type="date" class="form-control" id="lpoDate" name="lpo_date">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier Code</label>
                    <input type="text" class="form-control" id="supplierCode" name="supplier_code" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Requested By</label>
                    <input type="text" class="form-control" id="requestedBy" name="requested_by" readonly>
                </div>

                <div class="col-md-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-3">
                    <label class="form-label">Supplier INV/DN No</label>
                    <input type="text" class="form-control" id="invNo" name="inv_no">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier INV/DN Date</label>
                    <input type="date" class="form-control" id="invDate" name="inv_date">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Project Name</label>
                    <input type="text" class="form-control" id="projectName" name="project_name" readonly>
                </div>

            </div>
        </div>

        <!-- Items Table -->
        <div class="card mb-3">
            <div class="card-header">GRN Items</div>
            <div class="card-body">
                <table class="table datatables" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Select LPO to fetch items</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-success" id="saveGrnBtn">Save GRN</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function(){

    let items = [];

    // Fetch LPO details and items
    $('#lpoSelect').change(function(){
        let lpoId = $(this).val();
        if(!lpoId){
            items = [];
            renderItemsTable();
            return;
        }

        $.get("{{ url('grns/lpo-details') }}/" + lpoId, function(res){
            $('#supplierName').val(res.supplier_name || '');
            $('#lpoDate').val(res.lpo_date || '');
            $('#supplierCode').val(res.supplier_code || '');
            $('#requestedBy').val(res.requested_by || '');
            $('#departmentId').val(res.department_id || '');
            $('#departmentName').val(res.department || '');
            $('#projectName').val(res.project_name || '');
            $('#invNo').val(res.inv_no || '');
            $('#invDate').val(res.inv_date || '');

            items = res.items.map(i=>({
                description: i.description,
                uom: i.uom,
                quantity: i.quantity || 0
            }));

            renderItemsTable();
        }).fail(()=>{
            Swal.fire('Error','Failed to fetch LPO details','error');
        });
    });

    function renderItemsTable(){
        let tbody = $('#itemsTable tbody');
        tbody.empty();
        if(items.length === 0){
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No items</td></tr>');
            return;
        }
        items.forEach((item, index)=>{
            tbody.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>
                        ${item.description}
                        <input type="hidden" name="items[${index}][description]" value="${item.description}">
                    </td>
                    <td>
                        ${item.uom}
                        <input type="hidden" name="items[${index}][uom]" value="${item.uom}">
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control" value="${item.quantity}" name="items[${index}][quantity]" required>
                    </td>
                    <td class="text-center">
                        <a class="las la-trash text-secondary fs-18 deleteItemBtn" data-index="${index}"></a>
                    </td>
                </tr>
            `);
        });
    }

    // Delete item from table
    $(document).on('click', '.deleteItemBtn', function(){
        let idx = $(this).data('index');
        items.splice(idx,1);
        renderItemsTable();
    });

    // Save GRN
    $('#grnForm').submit(function(e){
        e.preventDefault();

        if(!$('#lpoSelect').val()){
            Swal.fire('Validation','Please select LPO','error');
            return;
        }
        if(items.length === 0){
            Swal.fire('Validation','Add at least one item','error');
            return;
        }

        $.ajax({
            url: "{{ route('grns.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                Swal.fire('Success', res.message, 'success').then(()=>{
                    window.location.href = "{{ route('grns.index') }}";
                });
            },
            error: function(xhr){
                let msg = 'Validation failed';
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg,'error');
            }
        });
    });

});
</script>
@endsection