@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">All GRNs</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">All GRNs</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- GRNs Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="grnTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>GRN No</th>
                        <th>LPO No</th>
                        <th>Supplier Name</th>
                        <th>LPO Date</th>
                        <th>Requested By</th>
                        <th>Department</th>
                        <th>Project Name</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- GRN Preview Modal -->
<div class="modal fade" id="grnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">GRN Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="grnPreviewContent">

                <div class="text-center mb-4">
                    <h4><strong>Expert Power Glass Ind L.L.C</strong></h4>
                </div>

                <div class="details-grid mb-4">
                    <div><strong>GRN No:</strong> <span id="modalGrnNo"></span></div>
                    <div><strong>LPO No:</strong> <span id="modalLpoNo"></span></div>
                    <div><strong>LPO Date:</strong> <span id="modalDate"></span></div>
                    <div><strong>Supplier:</strong> <span id="modalSupplier"></span></div>
                    <div><strong>Requested By:</strong> <span id="modalRequestedBy"></span></div>
                    <div><strong>Department:</strong> <span id="modalDepartment"></span></div>
                    <div><strong>Project:</strong> <span id="modalProject"></span></div>
                    <div><strong>Supplier Code:</strong> <span id="modalSupplierCode"></span></div>
                    <div><strong>INV/DN No:</strong> <span id="modalInvNo"></span></div>
                    <div><strong>INV/DN Date:</strong> <span id="modalInvDate"></span></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="modalItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sr #</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printGrnBtn">
                    <i class="las la-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function(){

    // ------------------ DataTable ------------------
    var table = $('#grnTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('grns.index') }}",
        columns: [
            {data:'DT_RowIndex', orderable:false, searchable:false, className:'text-center'},
            {data:'grn_no', name:'grn_no'},
            {data:'lpo_no', name:'lpo_no'},
            {data:'supplier_name', name:'supplier_name'},
            {data:'lpo_date', name:'lpo_date'}, // ✅ Fixed
            {data:'requested_by', name:'requested_by'},
            {data:'department', name:'department'},
            {data:'project_name', name:'project_name'},
            {data:'action', orderable:false, searchable:false, className:'text-center'},
        ]
    });

    // ------------------ Delete GRN ------------------
    $(document).on('click', '.deleteBtn', function(){
        let id = $(this).data('id');
        Swal.fire({
            title:'Are you sure?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete!'
        }).then(result=>{
            if(result.isConfirmed){
                $.ajax({
                    url: "/grns/" + id,
                    type: "DELETE",
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res){
                        Swal.fire('Deleted!', res.message, 'success');
                        table.ajax.reload(null,false);
                    },
                    error: function(){
                        Swal.fire('Error','Failed to delete GRN','error');
                    }
                });
            }
        });
    });

    // ------------------ View / Preview GRN ------------------
    $(document).on('click', '.viewBtn', function(){
        let id = $(this).data('id');
        $.get("/grns/" + id, function(res){
            $('#modalGrnNo').text(res.grn_no || '');
            $('#modalLpoNo').text(res.lpo_no || (res.lpo ? res.lpo.lpo_no : ''));
            $('#modalDate').text(res.lpo_date ? new Date(res.lpo_date).toLocaleDateString() : '');
            $('#modalSupplier').text(res.supplier_name || (res.lpo ? res.lpo.supplier_name : ''));
            $('#modalRequestedBy').text(res.requested_by || '');
            $('#modalDepartment').text(res.department ? res.department.name : '');
            $('#modalProject').text(res.project_name || '');
            $('#modalSupplierCode').text(res.supplier_code || '');
            $('#modalInvNo').text(res.inv_no || '');
            $('#modalInvDate').text(res.inv_date ? new Date(res.inv_date).toLocaleDateString() : '');

            let tbody = $('#modalItemsTable tbody').empty();
            if(!res.items || res.items.length === 0){
                tbody.append('<tr><td colspan="4" class="text-center">No items</td></tr>');
            } else {
                res.items.forEach((item,index)=>{
                    tbody.append(`<tr>
                        <td>${index+1}</td>
                        <td>${item.description}</td>
                        <td>${item.uom}</td>
                        <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    </tr>`);
                });
            }

            $('#grnModal').modal('show');
        }).fail(()=>Swal.fire('Error','Unable to fetch GRN details','error'));
    });

    // ------------------ Edit GRN ------------------
    $(document).on('click','.editBtn', function(){
        window.location.href = "/grns/" + $(this).data('id') + "/edit";
    });

    // ------------------ Print GRN ------------------
    $(document).on('click','#printGrnBtn', function(){
        let modalBody = document.querySelector("#grnPreviewContent").innerHTML;
        let w = window.open('', '', 'width=900,height=650');
        w.document.write(`
            <html>
            <head>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @page { size: A4; margin: 20mm; }
                    @media print {
                        body { -webkit-print-color-adjust: exact; background: #fff !important; }
                        table { border-collapse: collapse !important; width: 100%; }
                        table, th, td { border: 1px solid #ccc !important; }
                        thead th { background: #f2f2f2 !important; }
                    }
                    body { font-size: 13px; font-family: "Times New Roman", serif; background: #fff !important; }
                    .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 13px; }
                    .table th, .table td { padding: 6px 8px !important; vertical-align: middle; }
                </style>
            </head>
            <body>${modalBody}</body>
            </html>
        `);
        w.document.close();
        w.print();
    });

});
</script>
@endsection
