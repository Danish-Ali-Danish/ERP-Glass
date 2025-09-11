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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>All GRNs</h4>
            <a href="{{ route('grns.create') }}" class="btn btn-primary">
                <i class="las la-plus"></i> New GRN
            </a>
        </div>
        <div class="card-body">
            <table class="table datatables" id="grnTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>LPO No</th>
                        <th>Supplier Name</th>
                        <th>Date</th>
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
<div class="modal fade" id="grnPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">GRN Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="grnPreviewContent">
        <div class="text-center mb-4">
            <h4><strong>Expert Power Glass Ind L.L.C</strong></h4>
        </div>

        <div class="details-grid mb-4" style="display:grid; grid-template-columns:1fr 1fr; gap:8px 16px;">
            <div><strong>LPO No:</strong> <span id="previewLpoNo"></span></div>
            <div><strong>Date:</strong> <span id="previewDate"></span></div>
            <div><strong>Supplier Name:</strong> <span id="previewSupplier"></span></div>
            <div><strong>Requested By:</strong> <span id="previewRequestedBy"></span></div>
            <div><strong>Department:</strong> <span id="previewDepartment"></span></div>
            <div><strong>Project Name:</strong> <span id="previewProject"></span></div>
            <div><strong>Supplier Code:</strong> <span id="previewSupplierCode"></span></div>
            <div><strong>INV/DN No:</strong> <span id="previewInvNo"></span></div>
            <div><strong>INV/DN Date:</strong> <span id="previewInvDate"></span></div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered" id="previewGrnItemsTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
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
        <button type="button" class="btn btn-primary" id="printGrnPreviewBtn">
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
    // 1️⃣ Initialize DataTable
    let grnTable = $('#grnTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('grns.index') }}",
        responsive: true,
        pageLength: 10,
        order: [[0,'desc']],
        columns: [
            {data:'DT_RowIndex', orderable:false, searchable:false, className:'text-center'},
            {data:'lpo_no', name:'lpo_no'},
            {data:'supplier_name', name:'supplier_name'},
            {data:'date', name:'date'},
            {data:'requested_by', name:'requested_by'},
            {data:'department', name:'department'},
            {data:'project_name', name:'project_name'},
            {data:'action', orderable:false, searchable:false, className:'text-center'},
        ]
    });

    // 2️⃣ Delete GRN
    $(document).on('click','.deleteBtn', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if(result.isConfirmed){
                $.ajax({
                    url: '/grns/' + id,
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(res){
                        Swal.fire('Deleted!', res.message, 'success');
                        grnTable.ajax.reload(null,false);
                    },
                    error: function(){
                        Swal.fire('Error','Failed to delete','error');
                    }
                });
            }
        });
    });

    // 3️⃣ Preview GRN
    $(document).on('click','.viewBtn', function(){
        let id = $(this).data('id');
        $.get("/grns/"+id, function(res){
            $('#previewLpoNo').text(res.lpo_no);
            $('#previewDate').text(res.date);
            $('#previewSupplier').text(res.supplier_name);
            $('#previewRequestedBy').text(res.requested_by);
            $('#previewDepartment').text(res.department);
            $('#previewProject').text(res.project_name);
            $('#previewSupplierCode').text(res.supplier_code);
            $('#previewInvNo').text(res.inv_no);
            $('#previewInvDate').text(res.inv_date);

            let tbody = $('#previewGrnItemsTable tbody').empty();
            res.items.forEach((item,index)=>{
                tbody.append(`
                    <tr>
                        <td>${index+1}</td>
                        <td>${item.description}</td>
                        <td>${item.uom}</td>
                        <td>${item.quantity}</td>
                    </tr>
                `);
            });
            $('#grnPreviewModal').modal('show');
        }).fail(()=>Swal.fire('Error','Unable to fetch details','error'));
    });

    // 4️⃣ Print GRN Preview
    $(document).on('click','#printGrnPreviewBtn', function(){
        let modalContent = document.querySelector("#grnPreviewContent").innerHTML;
        let w = window.open('', '', 'width=900,height=650');
        w.document.write(`
            <html>
            <head>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @page { size: A4; margin: 20mm; }
                    body { font-family: "Times New Roman", serif; font-size: 13px; }
                    table { border-collapse: collapse; width: 100%; }
                    table, th, td { border: 1px solid #ccc; }
                    thead th { background: #f2f2f2; }
                    .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 13px; }
                </style>
            </head>
            <body>${modalContent}</body>
            </html>
        `);
        w.document.close();
        w.print();
    });

    // 5️⃣ Edit GRN
    $(document).on('click','.editBtn', function(){
        window.location.href = "/grns/"+$(this).data('id')+"/edit";
    });
});
</script>
@endsection
