@extends('layouts.master')
@section('content') 
<div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">All Local Purchase Order</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All LPOs</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>   
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>All LPOs</h4>
            <a href="{{ route('lpos.create') }}" class="btn btn-primary">
                <i class="las la-plus"></i> New LPO
            </a>
        </div>
        <div class="card-body">
            <table class="table datatables" id="lpoTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>PI No</th>
                        <th>Supplier TRN</th>
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
        <h5 class="modal-title">LPO Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="previewContent">
        
        <div class="text-center mb-4">
            <h4><strong>Expert Power Glass Ind L.L.C</strong></h4>
        </div>

        <div class="details-grid mb-4">
            <div><strong>Supplier Name:</strong> <span id="previewSupplier"></span></div>
            <div><strong>Date:</strong> <span id="previewDate"></span></div>
            <div><strong>Contact Person:</strong> <span id="previewContactPerson"></span></div>
            <div><strong>LPO No:</strong> <span id="previewLpoNo"></span></div>
            <div><strong>Contact No:</strong> <span id="previewContactNo"></span></div>
            <div><strong>PI No:</strong> <span id="previewPiNo"></span></div>
            <div><strong>Supplier TRN:</strong> <span id="previewTrn"></span></div>
            <div style="grid-column: span 2;"><strong>Address:</strong> <span id="previewAddress"></span></div>
        </div>

        <div class="table-responsive">
          <table class="table" id="previewItemsTable">
            <thead class="table-light">
              <tr>
                <th>Sr #</th>
                <th>Description</th>
                <th>Area (SQM)</th>
                <th>Qty</th>
                <th>UOM</th>
                <th>Unit Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <!-- Summary -->
        <div class="row mt-3">
            <div class="col-md-4 offset-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <strong>Sub Total:</strong> <span id="previewSubTotal"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>VAT:</strong> <span id="previewVat"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>NET Total:</strong> <span id="previewNetTotal"></span>
                        </div>
                    </div>
                </div>
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
    // DataTable
    let lpoTable = $('#lpoTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('lpos.index') }}",
        responsive:true,
        pageLength:10,
        order:[[0,'desc']],
        columns:[
            {data:'DT_RowIndex', orderable:false, searchable:false, className:'text-center'}, 
            {data:'supplier_name', name:'supplier_name'},
            {data:'contact_person', name:'contact_person'},
            {data:'pi_no', name:'pi_no'},
            {data:'supplier_trn', name:'supplier_trn'},
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
                    url:'/lpos/'+id,
                    type:'DELETE',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    success:function(res){
                        Swal.fire('Deleted!',res.message,'success');
                        lpoTable.ajax.reload(null,false);
                    },
                    error:()=>Swal.fire('Error','Failed to delete','error')
                });
            }
        });
    });

    // View Preview
    $(document).on('click','.viewBtn', function(){
        let id = $(this).data('id');
        $.get("/lpos/"+id, function(res){
            $('#previewSupplier').text(res.supplier_name);
            $('#previewDate').text(res.date);
            $('#previewContactPerson').text(res.contact_person);
            $('#previewLpoNo').text(res.lpo_no);
            $('#previewContactNo').text(res.contact_no || '-');
            $('#previewPiNo').text(res.pi_no || '-');
            $('#previewTrn').text(res.supplier_trn || '-');
            $('#previewAddress').text(res.address || '-');

            $('#previewSubTotal').text(res.sub_total);
            $('#previewVat').text(res.vat);
            $('#previewNetTotal').text(res.net_total);

            let tbody = $('#previewItemsTable tbody').empty();
            res.items.forEach((item,index)=>{
                tbody.append(`
                    <tr>
                        <td>${index+1}</td>
                        <td>${item.description}</td>
                        <td>${item.area}</td>
                        <td>${item.quantity}</td>
                        <td>${item.uom}</td>
                        <td>${item.unit_price}</td>
                        <td>${item.total}</td>
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
                        .print-footer { position: fixed; bottom: 0; }
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
        window.location.href = "/lpos/"+$(this).data('id')+"/edit";
    });
});
</script>
@endsection
