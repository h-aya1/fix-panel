@extends('layouts.app')

@section('title', __('employee_summary.title'))

@section('page-style')
<link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.base.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.bootstrap.css') }}" type="text/css" />
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 0.9rem; background-color: #f4f5f7; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0; }
    .header-main-title { font-size: 1.75rem; font-weight: 600; color: #333; }
    .header-sub-info { display: flex; gap: 1.5rem; font-size: 0.875rem; color: #555; margin-top: 0.3rem; }
    .stats-card { background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .stat-item { text-align: center; }
    .stat-number { font-size: 2rem; font-weight: bold; color: #0d6efd; }
    .stat-label { font-size: 0.875rem; color: #6c757d; margin-top: 0.25rem; }
    .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem; }
    .actions-bar .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.25rem; }
    .import-area { border: 2px dashed #ced4da; padding: 30px 20px; text-align: center; margin-bottom: 1.5rem; background-color: #fff; border-radius: 0.3rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .upload-icon { width: 40px; height: 40px; background-color: #e9ecef; margin: 0 auto 15px auto; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #495057; }
    .upload-icon::before { content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor' class='bi bi-cloud-arrow-up-fill' viewBox='0 0 16 16'%3E%3Cpath d='M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z'/%3E%3C/svg%3E"); }
    #summaryGrid { width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 0.25rem; }
    .empty-state { text-align: center; py-5; }
</style>
@endsection

@section('page-script')
<script src="{{ asset('jqwidgets/jqxcore.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxdata.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxbuttons.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxscrollbar.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxlistbox.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxdropdownlist.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxmenu.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.selection.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.filter.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.sort.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.pager.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize data
    const summaryData = @json($summaries->items());
    
    // Data source configuration
    const source = {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'number' },
            { name: 'employee_id', type: 'string' },
            { name: 'name', type: 'string' },
            { name: 'company_name', type: 'string' },
            { name: 'position', type: 'string' },
            { name: 'age', type: 'number' },
            { name: 'work_days', type: 'number' },
            { name: 'base_salary', type: 'number' },
            { name: 'total_earnings', type: 'number' },
            { name: 'total_deductions', type: 'number' },
            { name: 'net_payment', type: 'number' },
            { name: 'contact_number', type: 'string' },
            { name: 'date_of_joining', type: 'date' },
            { name: 'imported_at', type: 'date' }
        ],
        localdata: summaryData
    };
    
    const dataAdapter = new $.jqx.dataAdapter(source);
    
    // Grid initialization
    $("#summaryGrid").jqxGrid({
        width: '100%',
        source: dataAdapter,
        theme: 'bootstrap',
        pageable: true,
        pagesize: 50,
        pagesizeoptions: ['25', '50', '100'],
        sortable: true,
        altrows: true,
        enabletooltips: true,
        editable: false,
        selectionmode: 'multiplerowsextended',
        filterable: true,
        showfilterrow: true,
        columns: [
            { text: '{{ __("employee_summary.table.employee_id") }}', datafield: 'employee_id', width: 100 },
            { text: '{{ __("employee_summary.table.name") }}', datafield: 'name', width: 120 },
            { text: '{{ __("employee_summary.table.company") }}', datafield: 'company_name', width: 120 },
            { text: '{{ __("employee_summary.table.position") }}', datafield: 'position', width: 100 },
            { text: '{{ __("employee_summary.table.age") }}', datafield: 'age', width: 60, cellsalign: 'center' },
            { text: '{{ __("employee_summary.table.work_days") }}', datafield: 'work_days', width: 80, cellsalign: 'center' },
            { text: '{{ __("employee_summary.table.base_salary") }}', datafield: 'base_salary', width: 120, cellsalign: 'right', cellsformat: 'n0' },
            { text: '{{ __("employee_summary.table.total_earnings") }}', datafield: 'total_earnings', width: 120, cellsalign: 'right', cellsformat: 'n0' },
            { text: '{{ __("employee_summary.table.total_deductions") }}', datafield: 'total_deductions', width: 120, cellsalign: 'right', cellsformat: 'n0' },
            { text: '{{ __("employee_summary.table.net_payment") }}', datafield: 'net_payment', width: 120, cellsalign: 'right', cellsformat: 'n0' },
            { text: '{{ __("employee_summary.table.contact") }}', datafield: 'contact_number', width: 120 },
            { text: '{{ __("employee_summary.table.join_date") }}', datafield: 'date_of_joining', width: 100, cellsalign: 'center', cellsformat: 'yyyy-MM-dd' },
            { text: '{{ __("employee_summary.table.imported_at") }}', datafield: 'imported_at', width: 140, cellsalign: 'center', cellsformat: 'yyyy-MM-dd HH:mm' }
        ]
    });
    
    // Import functionality
    $('#importFile').on('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        Swal.fire({
            title: '{{ __("employee_summary.importing") }}',
            text: '{{ __("employee_summary.please_wait") }}',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '{{ route("employee-summaries.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("employee_summary.import_successful_title") }}',
                        text: response.message,
                        timer: 3000
                    });
                    setTimeout(() => location.reload(), 3000);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || '{{ __("employee_summary.import_failed") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.error") }}',
                    text: message
                });
            }
        });
    });
    
    // Delete all functionality
    $('#deleteAllBtn').on('click', function() {
        Swal.fire({
            title: '{{ __("employee_summary.confirm_delete_all") }}',
            text: '{{ __("employee_summary.delete_all_warning") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("app.delete") }}',
            cancelButtonText: '{{ __("app.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("employee-summaries.delete-all") }}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("employee_summary.deleted_successfully") }}',
                                text: response.message,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 2000);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || '{{ __("employee_summary.delete_failed") }}';
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("app.error") }}',
                            text: message
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="page-header">
        <div>
            <h1 class="header-main-title mb-1">{{ __('employee_summary.title') }}</h1>
            <div class="header-sub-info">
                <span>{{ __('employee_summary.total_records', ['count' => $totalRecords]) }}</span>
                @if($latestImport)
                <span>{{ __('employee_summary.latest_import', ['date' => $latestImport->imported_at->format('Y-m-d H:i')]) }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-card">
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-number">{{ $totalRecords }}</div>
                <div class="stat-label">{{ __('employee_summary.total_employees') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($summaries->sum('base_salary')) }}</div>
                <div class="stat-label">{{ __('employee_summary.total_base_salary') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($summaries->sum('net_payment')) }}</div>
                <div class="stat-label">{{ __('employee_summary.total_net_payment') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $summaries->avg('work_days') ? round($summaries->avg('work_days'), 1) : 0 }}</div>
                <div class="stat-label">{{ __('employee_summary.avg_work_days') }}</div>
            </div>
        </div>
    </div>

    <!-- Import Area -->
    <div class="import-area">
        <div class="upload-icon"></div>
        <p>{{ __('employee_summary.import_description') }}</p>
        <input type="file" id="importFile" accept=".xlsx,.xls,.csv" style="display: none;" />
        <button type="button" class="btn btn-primary btn-sm mt-2 me-2" onclick="document.getElementById('importFile').click();">
            <i class="bx bx-file-find me-1"></i>{{ __('employee_summary.select_file') }}
        </button>
        <a href="{{ asset('templates/employee_summary_template.xlsx') }}" class="btn btn-outline-secondary btn-sm mt-2" download>
            <i class="bx bx-download me-1"></i>{{ __('employee_summary.download_template') }}
        </a>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <div class="actions-bar-left">
            <span class="text-muted">{{ __('employee_summary.showing_records', ['count' => $summaries->count(), 'total' => $totalRecords]) }}</span>
        </div>
        <div class="actions-bar-right">
            @if($totalRecords > 0)
            <button type="button" class="btn btn-danger btn-sm" id="deleteAllBtn">
                <i class="bx bx-trash me-1"></i>{{ __('employee_summary.delete_all') }}
            </button>
            @endif
            <button type="button" class="btn btn-success btn-sm" onclick="location.reload()">
                <i class="bx bx-refresh me-1"></i>{{ __('app.refresh') }}
            </button>
        </div>
    </div>

    <!-- Data Grid -->
    @if($totalRecords > 0)
        <div id="summaryGrid" class="mt-3"></div>
    @else
        <div class="empty-state">
            <div class="mb-4">
                <i class="bx bx-file-blank" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h5 class="text-muted mb-3">{{ __('employee_summary.no_records') }}</h5>
            <p class="text-muted mb-4">{{ __('employee_summary.no_records_description') }}</p>
            <button class="btn btn-primary" onclick="document.getElementById('importFile').click();">
                <i class="bx bx-upload me-2"></i>{{ __('employee_summary.import_first_file') }}
            </button>
        </div>
    @endif

</div>
@endsection
