@extends('layouts.master')
@section('content') 
<div class="container-fluid">

    <!-- Page Title + Breadcrumb -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Stock Issuance Forms (SIFs)</h4>
                <div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All SIFs</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>   
    
    <!-- All SIFs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>All SIFs</h4>
            <a href="{{ route('sifs.add-new') }}" class="btn btn-primary">
                <i class="las la-plus"></i> New SIF
            </a>
        </div>
        <div class="card-body">
            <table class="table datatables" id="sifsTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>SIF No</th>
                        <th>Date</th>
                        <th>Issued Date</th>
                        <th>Department</th>
                        <th>Project Name</th>
                        <th>Requested By</th>
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
        <h5 class="modal-title">SIF Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="previewContent">
        
        <div class="text-center mb-4">
            <h4><strong>Expert Power Glass Ind L.L.C</strong></h4>
            <h5><u>Stock Issuance Form</u></h5>
        </div>

        <!-- Details -->
        <div class="details-grid mb-4">
            <div><strong>SIF No:</strong> <span id="previewSifNo"></span></div>
            <div><strong>Date:</strong> <span id="previewDate"></span></div>
            <div><strong>Issued Date:</strong> <span id="previewIssuedDate"></span></div>
            <div><strong>Requested By:</strong> <span id="previewRequestedBy"></span></div>
            <div><strong>Department:</strong> <span id="previewDepartment"></span></div>
            <div style="grid-column: span 2;"><strong>Project Name:</strong> <span id="previewProject"></span></div>
            <div style="grid-column: span 2;"><strong>Remarks:</strong> <span id="previewRemarks"></span></div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
          <table class="table" id="previewItemsTable">
            <thead class="table-light">
              <tr>
                <th style="width:5%">Sr #</th>
                <th style="width:15%">Item Code</th>
                <th style="width:40%">Description</th>
                <th style="width:10%">UOM</th>
                <th style="width:10%">Quantity</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <!-- Footer Signatures -->
        <div class="print-footer">
            <div class="row text-center">
                <div class="col-4">________________<br><small>Prepared By</small></div>
                <div class="col-4">________________<br><small>Requested By</small></div>
                <div class="col-4">________________<br><small>Approved By</small></div>
            </div>
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
    // DataTable init
    let sifsTable = $('#sifsTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('sifs.index') }}",
        responsive:true,
        pageLength:10,
        order:[[0,'desc']],
        columns:[
            {data:'DT_RowIndex', orderable:false, searchable:false, className:'text-center'}, 
            {data:'sif_no', name:'sif_no'},
            {data:'date', name:'date'},
            {data:'issued_date', name:'issued_date'},
            {data:'department', name:'department'},
            {data:'project_name', name:'project_name'},
            {data:'requested_by', name:'requested_by'},
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
                    url:'/sifs/'+id,
                    type:'DELETE',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    success:function(res){
                        Swal.fire('Deleted!',res.message,'success');
                        sifsTable.ajax.reload(null,false);
                    },
                    error:()=>Swal.fire('Error','Failed to delete','error')
                });
            }
        });
    });

    // View Preview
    $(document).on('click','.viewBtn', function(){
        let id = $(this).data('id');
        $.get("/sifs/"+id, function(res){
            $('#previewSifNo').text(res.sif_no);
            $('#previewDate').text(res.date);
            $('#previewIssuedDate').text(res.issued_date);
            $('#previewRequestedBy').text(res.requested_by);
            $('#previewDepartment').text(res.department);
            $('#previewProject').text(res.project_name);
            $('#previewRemarks').text(res.remarks || '-');

            let tbody = $('#previewItemsTable tbody').empty();
            res.items.forEach((item,index)=>{
                tbody.append(`
                    <tr>
                        <td>${index+1}</td>
                        <td>${item.item_code}</td>
                        <td>${item.description}</td>
                        <td>${item.uom}</td>
                        <td>${item.quantity}</td>
                    </tr>
                `);
            });
            $('#previewModal').modal('show');
        }).fail(()=>Swal.fire('Error','Unable to fetch details','error'));
    });

    // Print directly from Preview Modal
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
                        .print-footer { position: fixed; bottom: 0; }
                        table { border-collapse: collapse !important; width: 100%; }
                        table, th, td { border: 1px solid #ccc !important; }
                        thead th { background: #f2f2f2 !important; }
                    }
                    body { font-size: 13px; font-family: "Times New Roman", serif; background: #fff !important; }
                    .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 13px; }
                    .table th, .table td { padding: 6px 8px !important; vertical-align: middle; }
                    .print-footer { margin-top: 60px; text-align: center; width: 100%; }
                    .print-footer .row { display: flex; justify-content: space-around; }
                    h4, h5 { background: #f2f2f2; padding: 6px; border-radius: 4px; text-align: center; }
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
        window.location.href = "/sifs/"+$(this).data('id')+"/edit";
    });
});
</script>
@endsection
