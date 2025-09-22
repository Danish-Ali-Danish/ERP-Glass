@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">All Work Orders</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Work Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>All Work Orders</h4>
            <a href="{{ route('workorders.create') }}" class="btn btn-primary">
                <i class="las la-plus"></i> New Work Order
            </a>
        </div>
        <div class="card-body">
            <table class="table datatables" id="workOrderTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Work Order No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Work Order Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">

                <div class="text-center mb-4">
                    <h4><strong>Expert Power Glass Ind L.L.C</strong></h4>
                </div>

                <div class="details-grid mb-4">
                    <div><strong>Customer Name:</strong> <span id="previewCustomer"></span></div>
                    <div><strong>Date:</strong> <span id="previewDate"></span></div>
                    <div><strong>Mobile No:</strong> <span id="previewMobile"></span></div>
                    <div><strong>Work Order No:</strong> <span id="previewWoNo"></span></div>
                    <div><strong>Work Order Type:</strong> <span id="previewWoType"></span></div>
                    <div><strong>Customer Ref:</strong> <span id="previewRef"></span></div>
                    <div style="grid-column: span 2;"><strong>Processes:</strong> <span id="previewProcesses"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="previewItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sr #</th>
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
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printPreviewBtn">
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
    // DataTable
    let woTable = $('#workOrderTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('workorders.index') }}",
        responsive:true,
        pageLength:10,
        order:[[0,'desc']],
        columns:[
            {data:'DT_RowIndex', orderable:false, searchable:false, className:'text-center'}, 
            {data:'work_order_no', name:'work_order_no'},
            {data:'customer_name', name:'customer_name'},
            {data:'date', name:'date'},
            {data:'work_order_type', name:'work_order_type'},
            {data:'action', orderable:false, searchable:false, className:'text-center'}, 
        ]
    });

    // Delete
    $(document).on('click','.deleteBtn',function(){
        let id=$(this).data('id');
        Swal.fire({
            title:'Are you sure?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete it!'
        }).then(result=>{
            if(result.isConfirmed){
                $.ajax({
                    url:'/workorders/'+id,
                    type:'DELETE',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    success:function(res){
                        Swal.fire('Deleted!',res.message,'success');
                        woTable.ajax.reload(null,false);
                    },
                    error:()=>Swal.fire('Error','Failed to delete','error')
                });
            }
        });
    });

    // View Preview
    $(document).on('click','.viewBtn', function(){
        let id = $(this).data('id');
        $.get("/workorders/"+id, function(res){
            $('#previewCustomer').text(res.customer_name);
$('#previewDate').text(new Date(res.date).toLocaleDateString('en-GB'));
            $('#previewMobile').text(res.customer_mobile || '-');
            $('#previewWoNo').text(res.work_order_no);
            $('#previewWoType').text(res.work_order_type || '-');
            $('#previewRef').text(res.customer_ref || '-');
            $('#previewProcesses').text(res.processes ? res.processes.join(', ') : '-');

            let tbody = $('#previewItemsTable tbody').empty();
            res.items.forEach((item,index)=>{
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
                        <td>${item.instructions || ''}</td>
                    </tr>
                `);
            });
            $('#previewModal').modal('show');
        }).fail(()=>Swal.fire('Error','Unable to fetch details','error'));
    });

    // Print
    $(document).on('click','#printPreviewBtn', function(){
        let modalBody = document.querySelector("#previewContent").innerHTML;

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

    // Edit
    $(document).on('click','.editBtn', function(){
        window.location.href = "/workorders/"+$(this).data('id')+"/edit";
    });
});
</script>
@endsection