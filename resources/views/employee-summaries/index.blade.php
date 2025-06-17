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
    /* Import Area - Enhanced for Drag & Drop */
    .import-area { 
        border: 2px dashed #ced4da; 
        padding: 30px 20px; 
        text-align: center; 
        margin-bottom: 1.5rem; 
        background-color: #fff; 
        border-radius: 0.3rem; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .import-area:hover {
        border-color: #0d6efd;
        background-color: #f8f9ff;
    }
    .import-area.dragover {
        border-color: #0d6efd;
        background-color: #e7f1ff;
        border-style: solid;
    }
    .import-area.dragging {
        border-color: #198754;
        background-color: #d4edda;
        border-style: solid;
    }
    .upload-icon { width: 40px; height: 40px; background-color: #e9ecef; margin: 0 auto 15px auto; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #495057; }
    .upload-icon::before { content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor' class='bi bi-cloud-arrow-up-fill' viewBox='0 0 16 16'%3E%3Cpath d='M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z'/%3E%3C/svg%3E"); }
    #summaryGrid { width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 0.25rem; }
    .grid-container { min-height: 600px; } /* Ensure container always exists */
    .empty-state { text-align: center; padding-top: 5rem; padding-bottom: 5rem; }
    
    /* Preview Modal Styles */
    .modal-xl { max-width: 1200px; }
    #previewModal .table th { background-color: #f8f9fa; font-weight: 600; font-size: 0.875rem; }
    #previewModal .table td { font-size: 0.875rem; vertical-align: middle; }
    #previewModal .table-responsive { border: 1px solid #dee2e6; border-radius: 0.375rem; }
    #selectionCount { font-size: 0.875rem; }
    
    /* Detail Sidebar Styles */
    #detailSidebar {
        position: fixed;
        top: 0;
        right: -600px;
        width: 600px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        overflow-y: auto;
    }
    #detailSidebar.show {
        right: 0;
    }
    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        position: relative;
    }
    .sidebar-content {
        padding: 0;
    }
    
    /* Tab Styles */
    .sidebar-tabs {
        border-bottom: 1px solid #e9ecef;
    }
    .sidebar-tab-nav {
        display: flex;
        background: #f8f9fa;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .sidebar-tab-nav li {
        flex: 1;
    }
    .sidebar-tab-nav a {
        display: block;
        padding: 1rem;
        text-decoration: none;
        color: #6c757d;
        font-weight: 500;
        text-align: center;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .sidebar-tab-nav a:hover {
        background: #e9ecef;
        color: #495057;
    }
    .sidebar-tab-nav a.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: white;
    }
    
    .tab-content {
        display: none;
        padding: 1.5rem;
    }
    .tab-content.active {
        display: block;
    }
    
    /* Form Styles */
    .form-row {
        margin-bottom: 1rem;
    }
    .form-label {
        font-weight: 600;
        color: #555;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        display: block;
    }
    .form-control-sm {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    .form-control:disabled {
        background-color: #f8f9fa;
        opacity: 1;
    }
    
    /* Display Mode Styles */
    .display-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f8f9fa;
    }
    .display-label {
        font-weight: 600;
        color: #555;
        font-size: 0.875rem;
    }
    .display-value {
        color: #333;
        font-size: 0.875rem;
        text-align: right;
    }
    .display-value.currency {
        color: #198754;
        font-weight: 600;
    }
    
    .duration-badge-sidebar {
        background-color: #e7f3ff;
        color: #0066cc;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Action Buttons */
    .sidebar-actions {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }
    
    .close-sidebar {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        z-index: 10;
    }
    .close-sidebar:hover {
        color: #495057;
    }
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
            { name: 'no', type: 'number' },
            { name: 'employee_id', type: 'string' },
            { name: 'name', type: 'string' },
            { name: 'company_name', type: 'string' },
            { name: 'position', type: 'string' },
            { name: 'age', type: 'number' },
            { name: 'resident_registration_number', type: 'string' },
            { name: 'contact_number', type: 'string' },
            { name: 'date_of_joining', type: 'date' },
            { name: 'employment_duration', type: 'string' },
            { name: 'work_days', type: 'number' },
            { name: 'base_salary', type: 'number' },
            { name: 'qualification_allowance', type: 'number' },
            { name: 'position_allowance', type: 'number' },
            { name: 'duty_allowance', type: 'number' },
            { name: 'overtime_allowance', type: 'number' },
            { name: 'holiday_work_allowance', type: 'number' },
            { name: 'night_shift_allowance', type: 'number' },
            { name: 'bonus', type: 'number' },
            { name: 'adjustment_allowance', type: 'number' },
            { name: 'transportation_allowance', type: 'number' },
            { name: 'meal_allowance', type: 'number' },
            { name: 'labor_day_allowance', type: 'number' },
            { name: 'paid_leave_allowance', type: 'number' },
            { name: 'welfare_allowance', type: 'number' },
            { name: 'other_allowances', type: 'number' },
            { name: 'total_earnings', type: 'number' },
            { name: 'health_insurance', type: 'number' },
            { name: 'long_term_care_insurance', type: 'number' },
            { name: 'employment_insurance', type: 'number' },
            { name: 'national_pension', type: 'number' },
            { name: 'income_tax', type: 'number' },
            { name: 'local_income_tax', type: 'number' },
            { name: 'other_deductions', type: 'number' },
            { name: 'total_deductions', type: 'number' },
            { name: 'net_payment', type: 'number' },
            { name: 'remarks', type: 'string' },
            { name: 'imported_at', type: 'date' }
        ],
        id: 'id', // Important for deleterow to work correctly
        localdata: summaryData
    };
    
    const dataAdapter = new $.jqx.dataAdapter(source);
    
    // Function to initialize or update the grid
    function initializeGrid() {
        const gridExists = $("#summaryGrid").hasClass('jqx-grid');
        
        if ($("#summaryGrid").length > 0 && !gridExists) {
            // Grid initialization - only if element exists and hasn't been initialized
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
                    { text: '{{ __("employee_summary.table.no") }}', datafield: 'no', width: 60, cellsalign: 'center' },
                    { text: '{{ __("employee_summary.table.employee_id") }}', datafield: 'employee_id', width: 100 },
                    { text: '{{ __("employee_summary.table.name") }}', datafield: 'name', width: 120 },
                    { text: '{{ __("employee_summary.table.company") }}', datafield: 'company_name', width: 120 },
                    { text: '{{ __("employee_summary.table.position") }}', datafield: 'position', width: 100 },
                    { text: '{{ __("employee_summary.table.age") }}', datafield: 'age', width: 60, cellsalign: 'center' },
                    { text: '{{ __("employee_summary.table.resident_registration_number") }}', datafield: 'resident_registration_number', width: 150 },
                    { text: '{{ __("employee_summary.table.contact") }}', datafield: 'contact_number', width: 120 },
                    { text: '{{ __("employee_summary.table.join_date") }}', datafield: 'date_of_joining', width: 100, cellsalign: 'center', cellsformat: 'yyyy-MM-dd' },
                    { text: '{{ __("employee_summary.employment_duration") }}', datafield: 'employment_duration', width: 120, cellsalign: 'center' },
                    { text: '{{ __("employee_summary.table.work_days") }}', datafield: 'work_days', width: 80, cellsalign: 'center' },
                    { text: '{{ __("employee_summary.table.base_salary") }}', datafield: 'base_salary', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.qualification_allowance") }}', datafield: 'qualification_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.position_allowance") }}', datafield: 'position_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.duty_allowance") }}', datafield: 'duty_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.overtime_allowance") }}', datafield: 'overtime_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.holiday_work_allowance") }}', datafield: 'holiday_work_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.night_shift_allowance") }}', datafield: 'night_shift_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.bonus") }}', datafield: 'bonus', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.adjustment_allowance") }}', datafield: 'adjustment_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.transportation_allowance") }}', datafield: 'transportation_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.meal_allowance") }}', datafield: 'meal_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.labor_day_allowance") }}', datafield: 'labor_day_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.paid_leave_allowance") }}', datafield: 'paid_leave_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.welfare_allowance") }}', datafield: 'welfare_allowance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.other_allowances") }}', datafield: 'other_allowances', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.total_earnings") }}', datafield: 'total_earnings', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.health_insurance") }}', datafield: 'health_insurance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.long_term_care_insurance") }}', datafield: 'long_term_care_insurance', width: 150, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.employment_insurance") }}', datafield: 'employment_insurance', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.national_pension") }}', datafield: 'national_pension', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.income_tax") }}', datafield: 'income_tax', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.local_income_tax") }}', datafield: 'local_income_tax', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.other_deductions") }}', datafield: 'other_deductions', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.total_deductions") }}', datafield: 'total_deductions', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.net_payment") }}', datafield: 'net_payment', width: 120, cellsalign: 'right', cellsformat: 'n0' },
                    { text: '{{ __("employee_summary.table.remarks") }}', datafield: 'remarks', width: 150 },
                    { text: '{{ __("employee_summary.table.imported_at") }}', datafield: 'imported_at', width: 140, cellsalign: 'center', cellsformat: 'yyyy-MM-dd HH:mm' },
                    { 
                        text: '{{ __("employee_summary.table.actions") }}', 
                        datafield: 'actions', 
                        width: 150, 
                        cellsalign: 'center',
                        editable: false,
                        sortable: false,
                        filterable: false,
                        cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                            // Use rowdata.uid for jqxGrid's internal row id
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewDetail(${rowdata.id})" title="{{ __('employee_summary.view_detail') }}">
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow(${rowdata.id}, '${rowdata.uid}')" title="{{ __('employee_summary.delete_row') }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        } else if ($("#summaryGrid").length > 0 && gridExists) {
            // Update existing grid
            $("#summaryGrid").jqxGrid({ source: dataAdapter });
        }
        
        // Show/hide grid and empty state
        if (summaryData.length > 0) {
            $("#summaryGrid").show();
            $("#emptyState").hide();
        } else {
            $("#summaryGrid").hide();
            $("#emptyState").show();
        }
    }
    
    // Initialize grid on page load
    initializeGrid();
    
    // Drag and Drop functionality for import area
    const importArea = document.getElementById('importArea');
    const importText = document.getElementById('importText');
    const importFileInput = document.getElementById('importFile');
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        importArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        importArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        importArea.addEventListener(eventName, unhighlight, false);
    });
    
    // Handle dropped files
    importArea.addEventListener('drop', handleDrop, false);
    
    // Click to open file dialog
    importArea.addEventListener('click', function() {
        importFileInput.click();
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        importArea.classList.add('dragover');
        importText.textContent = '{{ __("employee_summary.release_to_upload") }}';
    }
    
    function unhighlight(e) {
        importArea.classList.remove('dragover');
        importText.textContent = '{{ __("employee_summary.import_description") }}';
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            if (file.type.includes('sheet') || file.type.includes('csv') || file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv')) {
                importFileInput.files = files;
                // Trigger the change event manually
                $(importFileInput).trigger('change');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.error") }}',
                    text: '{{ __("employee_summary.invalid_file_type") }}'
                });
            }
        }
    }
    
    // Import functionality with preview
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
            url: '{{ route("employee-summaries.preview") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.close();
                    showPreviewModal(response.data, response.total_rows);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || '{{ __("employee_summary.preview_failed") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.error") }}',
                    text: message
                });
            }
        });
        
        // Reset file input
        $(this).val('');
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
    
    // Preview functionality
    let previewData = [];
    
    function showPreviewModal(data, totalRows) {
        previewData = data;
        
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel">{{ __("employee_summary.preview_title") }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">{{ __("employee_summary.preview_description") }}</p>
                            
                            <!-- Selection controls -->
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">{{ __("employee_summary.select_all") }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">{{ __("employee_summary.deselect_all") }}</button>
                                </div>
                                <span id="selectionCount" class="badge bg-info">0 of ${totalRows} selected</span>
                            </div>
                            
                            <!-- Preview table -->
                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="40px">
                                                <input type="checkbox" id="masterCheckbox" class="form-check-input">
                                            </th>
                                            <th>{{ __("employee_summary.table.no") }}</th>
                                            <th>{{ __("employee_summary.table.employee_id") }}</th>
                                            <th>{{ __("employee_summary.table.name") }}</th>
                                            <th>{{ __("employee_summary.table.company") }}</th>
                                            <th>{{ __("employee_summary.table.position") }}</th>
                                            <th>{{ __("employee_summary.table.age") }}</th>
                                            <th>{{ __("employee_summary.table.work_days") }}</th>
                                            <th>{{ __("employee_summary.table.base_salary") }}</th>
                                            <th>{{ __("employee_summary.table.total_earnings") }}</th>
                                            <th>{{ __("employee_summary.table.total_deductions") }}</th>
                                            <th>{{ __("employee_summary.table.net_payment") }}</th>
                                            <th>{{ __("employee_summary.table.contact") }}</th>
                                            <th>{{ __("employee_summary.table.join_date") }}</th>
                                            <th>{{ __("employee_summary.employment_duration") }}</th>
                                            <th>{{ __("employee_summary.table.remarks") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("employee_summary.cancel_import") }}</button>
                            <button type="button" class="btn btn-primary" id="saveSelectedBtn">{{ __("employee_summary.save_selected") }}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#previewModal').remove();
        
        // Add modal to page
        $('body').append(modalHtml);
        
        // Populate table
        populatePreviewTable(data);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
        
        // Bind events
        bindPreviewEvents();
    }
    
    function populatePreviewTable(data) {
        const tbody = $('#previewTableBody');
        tbody.empty();
        
        data.forEach((row, index) => {
            const tr = `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-checkbox" data-index="${index}" checked>
                    </td>
                    <td>${row.no || ''}</td>
                    <td>${row.employee_id || ''}</td>
                    <td>${row.name || ''}</td>
                    <td>${row.company_name || ''}</td>
                    <td>${row.position || ''}</td>
                    <td>${row.age || ''}</td>
                    <td>${row.work_days || ''}</td>
                    <td>${row.base_salary ? Number(row.base_salary).toLocaleString() : ''}</td>
                    <td>${row.total_earnings ? Number(row.total_earnings).toLocaleString() : ''}</td>
                    <td>${row.total_deductions ? Number(row.total_deductions).toLocaleString() : ''}</td>
                    <td>${row.net_payment ? Number(row.net_payment).toLocaleString() : ''}</td>
                    <td>${row.contact_number || ''}</td>
                    <td>${row.date_of_joining || ''}</td>
                    <td>-</td>
                    <td>${row.remarks || ''}</td>
                </tr>
            `;
            tbody.append(tr);
        });
        
        updateSelectionCount();
    }
    
    function bindPreviewEvents() {
        // Master checkbox
        $('#masterCheckbox').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
            updateSelectionCount();
        });
        
        // Individual checkboxes
        $(document).on('change', '.row-checkbox', function() {
            updateMasterCheckbox();
            updateSelectionCount();
        });
        
        // Select all button
        $('#selectAllBtn').on('click', function() {
            $('.row-checkbox').prop('checked', true);
            updateMasterCheckbox();
            updateSelectionCount();
        });
        
        // Deselect all button
        $('#deselectAllBtn').on('click', function() {
            $('.row-checkbox').prop('checked', false);
            updateMasterCheckbox();
            updateSelectionCount();
        });
        
        // Save selected button
        $('#saveSelectedBtn').on('click', function() {
            const selectedData = getSelectedData();
            if (selectedData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("app.warning") }}',
                    text: '{{ __("employee_summary.no_data_selected") }}'
                });
                return;
            }
            
            saveSelectedData(selectedData);
        });
    }
    
    function updateMasterCheckbox() {
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#masterCheckbox').prop('indeterminate', false);
            $('#masterCheckbox').prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#masterCheckbox').prop('indeterminate', false);
            $('#masterCheckbox').prop('checked', true);
        } else {
            $('#masterCheckbox').prop('indeterminate', true);
        }
    }
    
    function updateSelectionCount() {
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#selectionCount').text(`${checkedCheckboxes} of ${totalCheckboxes} selected`);
    }
    
    function getSelectedData() {
        const selectedData = [];
        $('.row-checkbox:checked').each(function() {
            const index = $(this).data('index');
            selectedData.push(previewData[index]);
        });
        return selectedData;
    }
    
    function saveSelectedData(selectedData) {
        Swal.fire({
            title: '{{ __("employee_summary.importing") }}',
            text: '{{ __("employee_summary.please_wait") }}',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '{{ route("employee-summaries.save-preview") }}',
            type: 'POST',
            data: {
                data: selectedData,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("employee_summary.import_successful_title") }}',
                        text: response.message,
                        timer: 3000
                    });
                    
                    // Close modal and refresh grid without page reload
                    $('#previewModal').modal('hide');
                    
                    // Update summaryData with new imported data
                    if (response.data && Array.isArray(response.data)) {
                        summaryData.push(...response.data);
                        source.localdata = summaryData;
                        initializeGrid(); // Re-initialize grid with new data
                    } else {
                        // Fallback: reload page if data structure is unexpected
                        setTimeout(() => location.reload(), 3000);
                    }
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
    }
    
    // START: >>>>> CORRECTED FUNCTIONS <<<<<
    // Attach functions to the window object to make them globally accessible
    
    // Delete row functionality
    window.deleteRow = function(id, rowId) { // Added rowId for jqxGrid
        Swal.fire({
            title: '{{ __("employee_summary.confirm_delete_row") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("app.delete") }}',
            cancelButtonText: '{{ __("app.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(`Deleting row with ID: ${id}, jqxGrid row ID: ${rowId}`);
                $.ajax({
                    url: '/employee-summaries/' + rowId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("employee_summary.row_deleted") }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Use the grid's API to remove the row from the view
                            $("#summaryGrid").jqxGrid('deleterow', rowId);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || '{{ __("employee_summary.delete_row_failed") }}';
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("app.error") }}',
                            text: message
                        });
                    }
                });
            }
        });
    };
    
    // View detail functionality
    window.viewDetail = function(id) {
        // Load employee details via AJAX
        $.ajax({
            url: `/employee-summaries/${id}`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    populateDetailSidebar(response.data);
                    $('#detailSidebar').addClass('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("app.error") }}',
                        text: response.message || 'Failed to load employee details.'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading employee details:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.error") }}',
                    text: 'An unexpected error occurred while loading employee details.'
                });
            }
        });
    };
    
    // Close detail sidebar
    window.closeDetailSidebar = function() {
        $('#detailSidebar').removeClass('show');
        // Reset to view mode when closing
        setEditMode(false);
    };
    
    // Tab switching functionality
    window.switchTab = function(tabId) {
        // Remove active class from all tabs and content
        $('.sidebar-tab-nav a').removeClass('active');
        $('.tab-content').removeClass('active');
        
        // Add active class to clicked tab and corresponding content
        $(`a[href="#${tabId}"]`).addClass('active');
        $(`#${tabId}`).addClass('active');
    };
    
    // Edit mode functionality
    let isEditMode = false;
    let currentEmployeeData = null;
    
    window.toggleEditMode = function() {
        setEditMode(!isEditMode);
    };
    
    function setEditMode(editMode) {
        isEditMode = editMode;
        
        if (editMode) {
            // Switch to edit mode - show form inputs
            $('.display-content').hide();
            $('.edit-content').show();
            $('#editBtn').text('{{ __("app.cancel") }}').removeClass('btn-primary').addClass('btn-secondary');
            $('#saveBtn').show();
        } else {
            // Switch to view mode - show display values
            $('.edit-content').hide();
            $('.display-content').show();
            $('#editBtn').text('{{ __("app.edit") }}').removeClass('btn-secondary').addClass('btn-primary');
            $('#saveBtn').hide();
            
            // Reset form values to original data
            if (currentEmployeeData) {
                populateEditForm(currentEmployeeData);
            }
        }
    }
    
    // Save functionality
    window.saveEmployee = function() {
        const formData = getFormData();
        
        Swal.fire({
            title: '{{ __("app.saving") }}',
            text: '{{ __("app.please_wait") }}',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/employee-summaries/${currentEmployeeData.id}`,
            type: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("app.saved") }}',
                        text: response.message,
                        timer: 2000
                    });
                    
                    // Update current data and refresh display
                    currentEmployeeData = response.data;
                    populateDetailSidebar(currentEmployeeData);
                    setEditMode(false);
                    
                    // Refresh the grid
                    location.reload();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || '{{ __("app.save_failed") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.error") }}',
                    text: message
                });
            }
        });
    };
    
    function getFormData() {
        return {
            employee_id: $('#edit_employee_id').val(),
            name: $('#edit_name').val(),
            company_name: $('#edit_company_name').val(),
            position: $('#edit_position').val(),
            age: $('#edit_age').val(),
            contact_number: $('#edit_contact_number').val(),
            date_of_joining: $('#edit_date_of_joining').val(),
            work_days: $('#edit_work_days').val(),
            base_salary: $('#edit_base_salary').val(),
            qualification_allowance: $('#edit_qualification_allowance').val(),
            position_allowance: $('#edit_position_allowance').val(),
            duty_allowance: $('#edit_duty_allowance').val(),
            overtime_allowance: $('#edit_overtime_allowance').val(),
            holiday_work_allowance: $('#edit_holiday_work_allowance').val(),
            night_shift_allowance: $('#edit_night_shift_allowance').val(),
            bonus: $('#edit_bonus').val(),
            adjustment_allowance: $('#edit_adjustment_allowance').val(),
            transportation_allowance: $('#edit_transportation_allowance').val(),
            meal_allowance: $('#edit_meal_allowance').val(),
            labor_day_allowance: $('#edit_labor_day_allowance').val(),
            paid_leave_allowance: $('#edit_paid_leave_allowance').val(),
            welfare_allowance: $('#edit_welfare_allowance').val(),
            other_allowances: $('#edit_other_allowances').val(),
            remarks: $('#edit_remarks').val()
        };
    }
    
    function populateEditForm(employee) {
        $('#edit_employee_id').val(employee.employee_id);
        $('#edit_name').val(employee.name);
        $('#edit_company_name').val(employee.company_name);
        $('#edit_position').val(employee.position);
        $('#edit_age').val(employee.age);
        $('#edit_contact_number').val(employee.contact_number);
        $('#edit_date_of_joining').val(employee.date_of_joining);
        $('#edit_work_days').val(employee.work_days);
        $('#edit_base_salary').val(employee.base_salary);
        $('#edit_qualification_allowance').val(employee.qualification_allowance);
        $('#edit_position_allowance').val(employee.position_allowance);
        $('#edit_duty_allowance').val(employee.duty_allowance);
        $('#edit_overtime_allowance').val(employee.overtime_allowance);
        $('#edit_holiday_work_allowance').val(employee.holiday_work_allowance);
        $('#edit_night_shift_allowance').val(employee.night_shift_allowance);
        $('#edit_bonus').val(employee.bonus);
        $('#edit_adjustment_allowance').val(employee.adjustment_allowance);
        $('#edit_transportation_allowance').val(employee.transportation_allowance);
        $('#edit_meal_allowance').val(employee.meal_allowance);
        $('#edit_labor_day_allowance').val(employee.labor_day_allowance);
        $('#edit_paid_leave_allowance').val(employee.paid_leave_allowance);
        $('#edit_welfare_allowance').val(employee.welfare_allowance);
        $('#edit_other_allowances').val(employee.other_allowances);
        $('#edit_remarks').val(employee.remarks);
    }

    // END: >>>>> CORRECTED FUNCTIONS <<<<<

    
    // Populate detail sidebar with employee data (this can remain a local function)
    function populateDetailSidebar(employee) {
        currentEmployeeData = employee;
        
        const formatNumber = (num) => num ? Number(num).toLocaleString() : '-';
        
        // Header information
        $('#detailEmployeeName').text(employee.name || '-');
        $('#detailEmployeeId').text(employee.employee_id || '-');
        
        // Basic Info Tab - Display mode
        $('#display_company').text(employee.company_name || '-');
        $('#display_position').text(employee.position || '-');
        $('#display_age').text(employee.age || '-');
        $('#display_contact').text(employee.contact_number || '-');
        $('#display_join_date').text(employee.date_of_joining ? new Date(employee.date_of_joining).toLocaleDateString() : '-');
        $('#display_duration').text(employee.employment_duration || '-');
        
        // Salary Info Tab - Display mode
        $('#display_work_days').text(employee.work_days || '-');
        $('#display_base_salary').text(formatNumber(employee.base_salary));
        $('#display_qualification_allowance').text(formatNumber(employee.qualification_allowance));
        $('#display_position_allowance').text(formatNumber(employee.position_allowance));
        $('#display_duty_allowance').text(formatNumber(employee.duty_allowance));
        $('#display_overtime_allowance').text(formatNumber(employee.overtime_allowance));
        $('#display_holiday_work_allowance').text(formatNumber(employee.holiday_work_allowance));
        $('#display_night_shift_allowance').text(formatNumber(employee.night_shift_allowance));
        $('#display_bonus').text(formatNumber(employee.bonus));
        $('#display_adjustment_allowance').text(formatNumber(employee.adjustment_allowance));
        $('#display_transportation_allowance').text(formatNumber(employee.transportation_allowance));
        $('#display_meal_allowance').text(formatNumber(employee.meal_allowance));
        $('#display_labor_day_allowance').text(formatNumber(employee.labor_day_allowance));
        $('#display_paid_leave_allowance').text(formatNumber(employee.paid_leave_allowance));
        $('#display_welfare_allowance').text(formatNumber(employee.welfare_allowance));
        $('#display_other_allowances').text(formatNumber(employee.other_allowances));
        
        // Other tabs (display only)
        $('#detailTotalEarnings').text(formatNumber(employee.total_earnings));
        $('#detailTotalDeductions').text(formatNumber(employee.total_deductions));
        $('#detailNetPayment').text(formatNumber(employee.net_payment));
        $('#detailHealthInsurance').text(formatNumber(employee.health_insurance));
        $('#detailLongTermCareInsurance').text(formatNumber(employee.long_term_care_insurance));
        $('#detailEmploymentInsurance').text(formatNumber(employee.employment_insurance));
        $('#detailNationalPension').text(formatNumber(employee.national_pension));
        $('#detailIncomeTax').text(formatNumber(employee.income_tax));
        $('#detailLocalIncomeTax').text(formatNumber(employee.local_income_tax));
        $('#detailOtherDeductions').text(formatNumber(employee.other_deductions));
        $('#detailRemarks').text(employee.remarks || '-');
        
        // Populate edit form
        populateEditForm(employee);
        
        // Reset to view mode
        setEditMode(false);
        
        // Show first tab
        switchTab('basicInfoTab');
    }
    
    // Company filter functionality
    $('#companyFilter').on('change', function() {
        const selectedCompany = $(this).val();
        const url = new URL(window.location.href);
        
        if (selectedCompany) {
            url.searchParams.set('company', selectedCompany);
        } else {
            url.searchParams.delete('company');
        }
        
        window.location.href = url.toString();
    });
    
    // Refresh data functionality
    $('#refreshBtn').on('click', function() {
        location.reload();
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

    <!-- Import Area with Drag & Drop -->
    <div class="import-area" id="importArea">
        <div class="upload-icon"></div>
        <p id="importText">{{ __('employee_summary.import_description') }}</p>
        <input type="file" id="importFile" accept=".xlsx,.xls,.csv" style="display: none;" />
        <button type="button" class="btn btn-primary btn-sm mt-2 me-2">
            <i class="bx bx-file-find me-1"></i>{{ __('employee_summary.select_file') }}
        </button>
        <a href="{{ asset('templates/employee_summary_template.xlsx') }}" class="btn btn-outline-secondary btn-sm mt-2" download>
            <i class="bx bx-download me-1"></i>{{ __('employee_summary.download_template') }}
        </a>
        <div class="mt-2 text-muted small">
            <i class="bx bx-info-circle me-1"></i>{{ __('employee_summary.drag_drop_hint') }}
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <div class="actions-bar-left">
            <span class="text-muted">{{ __('employee_summary.showing_records', ['count' => $summaries->count(), 'total' => $totalRecords]) }}</span>
            
            <!-- Company Filter -->
            <div class="ms-3 d-inline-block">
                <select id="companyFilter" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">{{ __('employee_summary.filter.all_companies') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                            {{ $company }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="actions-bar-right">
            @if($totalRecords > 0)
            <button type="button" class="btn btn-danger btn-sm" id="deleteAllBtn">
                <i class="bx bx-trash me-1"></i>{{ __('employee_summary.delete_all') }}
            </button>
            @endif
            <button type="button" class="btn btn-success btn-sm" id="refreshBtn">
                <i class="bx bx-refresh me-1"></i>{{ __('app.refresh') }}
            </button>
        </div>
    </div>

    <!-- Data Grid Container - Always present -->
    <div class="grid-container">
        <div id="summaryGrid" class="mt-3" style="@if($totalRecords == 0) display: none; @endif"></div>
        @if($totalRecords == 0)
            <div id="emptyState" class="empty-state">
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

</div>

<!-- Employee Detail Sidebar -->
<div id="detailSidebar">
    <div class="sidebar-header">
        <button type="button" class="close-sidebar" onclick="closeDetailSidebar()">
            <i class="bx bx-x"></i>
        </button>
        <h5 class="mb-1">{{ __('employee_summary.detail_title') }}</h5>
        <div class="text-muted">
            <span id="detailEmployeeName">-</span> - <span id="detailEmployeeId">-</span>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="sidebar-tabs">
        <ul class="sidebar-tab-nav">
            <li><a href="#basicInfoTab" class="active" onclick="switchTab('basicInfoTab')">{{ __('employee_summary.basic_info') }}</a></li>
            <li><a href="#salaryInfoTab" onclick="switchTab('salaryInfoTab')">{{ __('employee_summary.salary_info') }}</a></li>
            <li><a href="#deductionsTab" onclick="switchTab('deductionsTab')">{{ __('employee_summary.deductions') }}</a></li>
            <li><a href="#summaryTab" onclick="switchTab('summaryTab')">{{ __('employee_summary.summary') }}</a></li>
        </ul>
    </div>
    
    <div class="sidebar-content">
        <!-- Basic Info Tab -->
        <div id="basicInfoTab" class="tab-content active">
            <!-- Display Mode -->
            <div class="display-content">
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.company') }}:</div>
                    <div class="display-value" id="display_company">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.position') }}:</div>
                    <div class="display-value" id="display_position">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.age') }}:</div>
                    <div class="display-value" id="display_age">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.contact') }}:</div>
                    <div class="display-value" id="display_contact">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.join_date') }}:</div>
                    <div class="display-value" id="display_join_date">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.employment_duration') }}:</div>
                    <div class="display-value">
                        <span class="duration-badge-sidebar" id="display_duration">-</span>
                    </div>
                </div>
            </div>
            
            <!-- Edit Mode -->
            <div class="edit-content" style="display: none;">
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.employee_id') }}</label>
                    <input type="text" class="form-control form-control-sm" id="edit_employee_id" readonly>
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.name') }}</label>
                    <input type="text" class="form-control form-control-sm" id="edit_name">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.company') }}</label>
                    <input type="text" class="form-control form-control-sm" id="edit_company_name">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.position') }}</label>
                    <input type="text" class="form-control form-control-sm" id="edit_position">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.age') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_age" min="18" max="100">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.contact') }}</label>
                    <input type="text" class="form-control form-control-sm" id="edit_contact_number">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.join_date') }}</label>
                    <input type="date" class="form-control form-control-sm" id="edit_date_of_joining">
                </div>
            </div>
        </div>
        
        <!-- Salary Info Tab -->
        <div id="salaryInfoTab" class="tab-content">
            <!-- Display Mode -->
            <div class="display-content">
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.work_days') }}:</div>
                    <div class="display-value" id="display_work_days">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.base_salary') }}:</div>
                    <div class="display-value currency" id="display_base_salary">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.qualification_allowance') }}:</div>
                    <div class="display-value currency" id="display_qualification_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.position_allowance') }}:</div>
                    <div class="display-value currency" id="display_position_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.duty_allowance') }}:</div>
                    <div class="display-value currency" id="display_duty_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.overtime_allowance') }}:</div>
                    <div class="display-value currency" id="display_overtime_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.holiday_work_allowance') }}:</div>
                    <div class="display-value currency" id="display_holiday_work_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.night_shift_allowance') }}:</div>
                    <div class="display-value currency" id="display_night_shift_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.bonus') }}:</div>
                    <div class="display-value currency" id="display_bonus">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.adjustment_allowance') }}:</div>
                    <div class="display-value currency" id="display_adjustment_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.transportation_allowance') }}:</div>
                    <div class="display-value currency" id="display_transportation_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.meal_allowance') }}:</div>
                    <div class="display-value currency" id="display_meal_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.labor_day_allowance') }}:</div>
                    <div class="display-value currency" id="display_labor_day_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.paid_leave_allowance') }}:</div>
                    <div class="display-value currency" id="display_paid_leave_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.welfare_allowance') }}:</div>
                    <div class="display-value currency" id="display_welfare_allowance">-</div>
                </div>
                <div class="display-row">
                    <div class="display-label">{{ __('employee_summary.table.other_allowances') }}:</div>
                    <div class="display-value currency" id="display_other_allowances">-</div>
                </div>
            </div>
            
            <!-- Edit Mode -->
            <div class="edit-content" style="display: none;">
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.work_days') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_work_days" min="0" max="31">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.base_salary') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_base_salary" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.qualification_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_qualification_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.position_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_position_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.duty_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_duty_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.overtime_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_overtime_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.holiday_work_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_holiday_work_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.night_shift_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_night_shift_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.bonus') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_bonus" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.adjustment_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_adjustment_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.transportation_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_transportation_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.meal_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_meal_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.labor_day_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_labor_day_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.paid_leave_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_paid_leave_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.welfare_allowance') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_welfare_allowance" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.other_allowances') }}</label>
                    <input type="number" class="form-control form-control-sm" id="edit_other_allowances" min="0" step="1000">
                </div>
                <div class="form-row">
                    <label class="form-label">{{ __('employee_summary.table.remarks') }}</label>
                    <textarea class="form-control form-control-sm" id="edit_remarks" rows="3" placeholder="{{ __('employee_summary.remarks_placeholder') }}"></textarea>
                </div>
            </div>
        </div>
        
        <!-- Deductions Tab -->
        <div id="deductionsTab" class="tab-content">
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.health_insurance') }}:</div>
                <div class="display-value currency" id="detailHealthInsurance">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.long_term_care_insurance') }}:</div>
                <div class="display-value currency" id="detailLongTermCareInsurance">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.employment_insurance') }}:</div>
                <div class="display-value currency" id="detailEmploymentInsurance">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.national_pension') }}:</div>
                <div class="display-value currency" id="detailNationalPension">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.income_tax') }}:</div>
                <div class="display-value currency" id="detailIncomeTax">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.local_income_tax') }}:</div>
                <div class="display-value currency" id="detailLocalIncomeTax">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.other_deductions') }}:</div>
                <div class="display-value currency" id="detailOtherDeductions">-</div>
            </div>
        </div>
        
        <!-- Summary Tab -->
        <div id="summaryTab" class="tab-content">
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.total_earnings') }}:</div>
                <div class="display-value currency" id="detailTotalEarnings">-</div>
            </div>
            <div class="display-row">
                <div class="display-label">{{ __('employee_summary.table.total_deductions') }}:</div>
                <div class="display-value currency" id="detailTotalDeductions">-</div>
            </div>
            <div class="display-row" style="border-top: 2px solid #e9ecef; margin-top: 0.5rem; padding-top: 0.75rem;">
                <div class="display-label"><strong>{{ __('employee_summary.table.net_payment') }}:</strong></div>
                <div class="display-value currency" id="detailNetPayment"><strong>-</strong></div>
            </div>
            
            <div style="margin-top: 2rem;">
                <h6><i class="bx bx-note me-2"></i>{{ __('employee_summary.table.remarks') }}</h6>
                <div class="display-value" id="detailRemarks" style="text-align: left; margin-top: 0.5rem;">-</div>
            </div>
            
            <!-- Placeholder Sections -->
            <div style="margin-top: 2rem;">
                <h6><i class="bx bx-line-chart me-2"></i>{{ __('employee_summary.salary_records') }}</h6>
                <div class="text-muted text-center py-3">
                    {{ __('employee_summary.no_salary_records') }}
                </div>
            </div>

            <div style="margin-top: 2rem;">
                <h6><i class="bx bx-calendar-check me-2"></i>{{ __('employee_summary.attendance_leave') }}</h6>
                <div class="text-muted text-center py-3">
                    {{ __('employee_summary.no_attendance_records') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="sidebar-actions">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeDetailSidebar()">
            <i class="bx bx-x me-1"></i>{{ __('app.close') }}
        </button>
        <button type="button" class="btn btn-primary btn-sm" id="editBtn" onclick="toggleEditMode()">
            <i class="bx bx-edit me-1"></i>{{ __('app.edit') }}
        </button>
        <button type="button" class="btn btn-success btn-sm" id="saveBtn" onclick="saveEmployee()" style="display: none;">
            <i class="bx bx-save me-1"></i>{{ __('app.save') }}
        </button>
    </div>
</div>

@endsection