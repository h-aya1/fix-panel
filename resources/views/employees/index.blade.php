@extends('layouts.app')

@section('title', __('employee.management.page_title'))

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('page-style')
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.base.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.bootstrap.css') }}" type="text/css" />
  <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 0.9rem;
    }
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #dee2e6;
    }
    .header-main-title { font-size: 1.65rem; font-weight: 600; }
    .header-sub-title { font-size: 0.85rem; color: #6c757d; }

    /* Import Area Styles */
    .import-area {
        border: 2px dashed #adb5bd; 
        padding: 25px 20px; 
        text-align: center;
        margin-bottom: 1.5rem; 
        background-color: #f8f9fa; 
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .import-area:hover {
        border-color: #0d6efd;
        background-color: #f0f8ff;
    }
    .import-area.dragover {
        border-color: #0d6efd;
        background-color: #e3f2fd;
        transform: scale(1.02);
    }
    .upload-icon {
        width: 48px; 
        height: 48px; 
        background-color: #e9ecef; 
        margin: 0 auto 12px auto;
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: #495057;
        font-size: 24px;
    }
    .import-area p { 
        margin-bottom: 0.75rem; 
        color: #495057; 
        font-size: 0.9rem; 
    }
    .import-area .drag-hint {
        color: #6c757d;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    /* Grid and Action Styles */
    .action-buttons-top {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 1rem; 
        flex-wrap: wrap; 
        gap: 0.75rem;
    }
    .action-buttons-top .btn, .page-header .btn {
        font-size: 0.8rem; 
        padding: 0.4rem 0.8rem; 
        border-radius: 0.25rem;
    }
    .action-buttons-top .left-actions .btn {
        border: 1px solid #ced4da; 
        color: #495057; 
        background-color: #fff;
    }
    .action-buttons-top .left-actions .btn:hover { 
        background-color: #e9ecef; 
    }
    .action-buttons-top .left-actions .btn.active {
        background-color: #0d6efd; 
        color: white; 
        border-color: #0d6efd;
    }
    .btn-primary { 
        background-color: #0d6efd; 
        border-color: #0d6efd; 
    }
    .btn-primary:hover { 
        background-color: #0b5ed7; 
        border-color: #0a58ca; 
    }
    .btn-danger { 
        background-color: #dc3545; 
        border-color: #dc3545; 
    }
    .btn-danger:hover { 
        background-color: #c82333; 
        border-color: #bd2130; 
    }

    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }
    .empty-state .empty-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
    .empty-state h4 {
        color: #495057;
        margin-bottom: 1rem;
    }
    .empty-state p {
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Grid Container */
    .grid-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1rem;
    }

    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stats-card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        text-align: center;
    }
    .stats-card .stats-value {
        font-size: 1.75rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 0.5rem;
    }
    .stats-card .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 0.5rem;
    }
    .modal-header {
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }
    .modal-footer {
        border-top: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }

    /* Form Styles */
    .form-control, .form-select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Hidden input */
    #fileInput {
        display: none;
    }

    /* Loading states */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .action-buttons-top {
            flex-direction: column;
            align-items: stretch;
        }
        .action-buttons-top .left-actions,
        .action-buttons-top .right-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .stats-cards {
            grid-template-columns: 1fr;
        }
    }
  </style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="header-main-title">{{ __('employee.management.page_title') }}</h1>
            <p class="header-sub-title mb-0">{{ __('employee.management.excel_upload_prompt') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" id="downloadTemplateBtn">
                <i class="bx bx-download me-1"></i>{{ __('employee.management.download_template_button') }}
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bx bx-plus me-1"></i>{{ __('employee.management.add_employee') }}
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards" id="statsCards" style="display: none;">
        <!-- Statistics will be populated by JavaScript -->
    </div>

    <!-- Import Area -->
    <div class="import-area" id="importArea">
        <div class="upload-icon">
            <i class="bx bx-cloud-upload"></i>
        </div>
        <p class="mb-1"><strong>{{ __('employee.management.import_employees_title') }}</strong></p>
        <p class="drag-hint mb-0">
            <i class="bx bx-info-circle me-1"></i>{{ __('employee.management.drag_drop_hint') }}
        </p>
        <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" />
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons-top mb-3" id="actionButtons" style="display: none;">
        <div class="left-actions">
            <span id="recordCount" class="text-muted">{{ __('employee.management.total_employees_display', ['count' => 0]) }}</span>
            <label for="companyFilter" class="me-2">Filter by Company:</label>
            <select id="companyFilter" class="form-select d-inline-block w-auto">
                <option value="">All Companies</option>
                @foreach($companyNames as $company)
                    <option value="{{ $company }}">{{ $company }}</option>
                @endforeach
            </select>
        </div>
        <div class="right-actions">
            <button type="button" class="btn btn-danger btn-sm" id="deleteAllBtn">
                <i class="bx bx-trash me-1"></i>{{ __('Delete All') }}
            </button>
        </div>
    </div>

    <!-- Grid Container -->
    <div class="grid-container" id="gridContainer" style="display: none;">
        <div id="employeeGrid"></div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="emptyState">
        <div class="empty-icon">
            <i class="bx bx-user-plus"></i>
        </div>
        <h4>{{ __('No Employee Records') }}</h4>
        <p>{{ __('Start by importing your first employee file.') }}</p>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
            <i class="bx bx-upload me-1"></i>{{ __('Import Your First File') }}
        </button>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">{{ __('Preview Import Data') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0">{{ __('Review the data below before importing. You can select which records to import.') }}</p>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="selectAllPreview">{{ __('Select All') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllPreview">{{ __('Deselect All') }}</button>
                    </div>
                </div>
                <div id="previewGrid"></div>
                <div class="mt-3">
                    <span id="selectedCount" class="text-muted">{{ __('0 of 0 selected') }}</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveSelectedBtn" disabled>{{ __('Save Selected') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">{{ __('employee.management.add_employee_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID *</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position *</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="resident_registration_number" class="form-label">Resident Registration Number</label>
                            <input type="text" class="form-control" id="resident_registration_number" name="resident_registration_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_joining" class="form-label">Date of Joining</label>
                            <input type="date" class="form-control" id="date_of_joining" name="date_of_joining">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="work_days" class="form-label">Work Days</label>
                            <input type="number" class="form-control" id="work_days" name="work_days">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="base_salary" class="form-label">Base Salary</label>
                            <input type="number" class="form-control" id="base_salary" name="base_salary" step="0.01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="employment_status_key" class="form-label">Status *</label>
                            <select class="form-select" id="employment_status_key" name="employment_status_key" required>
                                <option value="active">Active</option>
                                <option value="resigning">Resigning</option>
                                <option value="resigned">Resigned</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveEmployeeBtn">{{ __('Save Employee') }}</button>
            </div>
        </div>
    </div>
</div>

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
<script src="{{ asset('jqwidgets/jqxgrid.columnsresize.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.filter.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.sort.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxcheckbox.js') }}"></script>

<script>
$(document).ready(function() {
    let employeeData = [];
    let previewData = [];
    let selectedFile = null;

    // Initialize page
    loadEmployees();

    // Drag and drop functionality
    const importArea = document.getElementById('importArea');
    const fileInput = document.getElementById('fileInput');

    // Drag and drop events
    importArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        importArea.classList.add('dragover');
    });

    importArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        importArea.classList.remove('dragover');
    });

    importArea.addEventListener('drop', (e) => {
        e.preventDefault();
        importArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (isValidFileType(file)) {
                selectedFile = file;
                processFileUpload(file);
            } else {
                showAlert('danger', 'Please select a valid Excel or CSV file.');
            }
        }
    });

    // Click to select file
    importArea.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            if (isValidFileType(file)) {
                selectedFile = file;
                processFileUpload(file);
            } else {
                showAlert('danger', 'Please select a valid Excel or CSV file.');
            }
        }
    });

    function isValidFileType(file) {
        const validTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'text/csv'
        ];
        return validTypes.includes(file.type) || file.name.match(/\.(xlsx|xls|csv)$/i);
    }

    function processFileUpload(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("employees.preview") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                importArea.classList.add('loading');
            },
            success: function(response) {
                console.log('Preview response:', response);
                if (response.success && response.data) {
                    previewData = response.data;
                    console.log('Preview data received:', previewData);
                    
                    if (previewData.rows && previewData.rows.length > 0) {
                        showPreviewModal();
                    } else {
                        showAlert('warning', 'No valid data rows found in the file. Please check your file format.');
                        resetFileInput();
                    }
                } else {
                    showAlert('danger', response.message || 'Failed to process file.');
                    resetFileInput();
                }
            },
            error: function(xhr) {
                console.log('Upload error:', xhr);
                let errorMessage = 'Failed to upload file. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                showAlert('danger', errorMessage);
                resetFileInput();
            },
            complete: function() {
                importArea.classList.remove('loading');
            }
        });
    }

    function resetFileInput() {
        fileInput.value = '';
        selectedFile = null;
        importArea.classList.remove('dragover', 'loading');
    }

    function showPreviewModal() {
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        setupPreviewGrid();
        modal.show();
    }

    function setupPreviewGrid() {
        if (!previewData || !previewData.rows || previewData.rows.length === 0) {
            console.log('No preview data available:', previewData);
            $('#previewGrid').html('<div class="alert alert-warning">No data to preview</div>');
            return;
        }

        console.log('Setting up preview grid with data:', previewData);
        console.log('Preview rows:', previewData.rows);

        const columns = [
            { text: '', datafield: 'selected', columntype: 'checkbox', width: 50 },
            { text: 'Employee ID', datafield: 'employee_id', width: 100 },
            { text: 'Name', datafield: 'name', width: 120 },
            { text: 'Company Name', datafield: 'company_name', width: 120 },
            { text: 'Position', datafield: 'position', width: 100 },
            { text: 'Age', datafield: 'age', width: 80 },
            { text: 'Date of Birth', datafield: 'date_of_birth', width: 120 },
            { text: 'Resident Registration Number', datafield: 'resident_registration_number', width: 180 },
            { text: 'Contact Number', datafield: 'contact_number', width: 120 },
            { text: 'Date of Joining', datafield: 'date_of_joining', width: 120 },
            { text: 'Employment Duration', datafield: 'employment_duration', width: 120 },
            { text: 'Work Days', datafield: 'work_days', width: 100 },
            { text: 'Base Salary', datafield: 'base_salary', width: 100 }
        ];

        // Prepare data with all required fields
        const gridData = previewData.rows.map((row, index) => {
            return {
                selected: row.selected !== false, // Default to true unless explicitly false
                employee_id: row.employee_id || '',
                name: row.name || '',
                company_name: row.company_name || '',
                position: row.position || '',
                age: row.age || '',
                date_of_birth: row.date_of_birth || '',
                resident_registration_number: row.resident_registration_number || '',
                contact_number: row.contact_number || '',
                date_of_joining: row.date_of_joining || '',
                employment_duration: row.employment_duration || '',
                work_days: row.work_days || '',
                base_salary: row.base_salary || '',
                originalIndex: row.row_index !== undefined ? row.row_index : index
            };
        });

        console.log('Grid data prepared:', gridData);

        const dataAdapter = new $.jqx.dataAdapter({
            localdata: gridData,
            datatype: 'array'
        });

        // Clear existing grid
        try {
            $('#previewGrid').jqxGrid('clear');
            $('#previewGrid').jqxGrid('destroy');
        } catch (e) {
            // Grid may not exist yet
            console.log('Grid destroy error (expected on first run):', e);
        }

        // Create the grid
        $('#previewGrid').jqxGrid({
            width: '100%',
            height: 400,
            source: dataAdapter,
            columns: columns,
            columnsresize: true,
            sortable: true,
            filterable: true,
            selectionmode: 'checkbox',
            enabletooltips: true,
            ready: function() {
                console.log('Preview grid ready');
                // Select all rows by default
                $('#previewGrid').jqxGrid('selectallrows');
                updateSelectedCount();
            }
        });

        // Bind selection events
        $('#previewGrid').off('rowselect rowunselect').on('rowselect rowunselect', function() {
            updateSelectedCount();
        });
    }

    function updateSelectedCount() {
        try {
            const grid = $('#previewGrid');
            if (grid.length > 0 && typeof grid.jqxGrid === 'function') {
                const selectedRows = grid.jqxGrid('getselectedrowindexes');
                const total = previewData.rows ? previewData.rows.length : 0;
                $('#selectedCount').text(`${selectedRows.length} of ${total} selected`);
                $('#saveSelectedBtn').prop('disabled', selectedRows.length === 0);
            }
        } catch (e) {
            console.log('Error updating selected count:', e);
        }
    }

    // Preview modal actions
    $('#selectAllPreview').click(function() {
        $('#previewGrid').jqxGrid('selectallrows');
        updateSelectedCount();
    });

    $('#deselectAllPreview').click(function() {
        $('#previewGrid').jqxGrid('clearselection');
        updateSelectedCount();
    });

    $('#saveSelectedBtn').click(function() {
        const selectedIndexes = $('#previewGrid').jqxGrid('getselectedrowindexes');
        
        if (selectedIndexes.length === 0) {
            showAlert('warning', 'Please select at least one record to import.');
            return;
        }

        // Get the original row indexes for the selected rows
        const selectedOriginalIndexes = [];
        selectedIndexes.forEach(function(gridIndex) {
            const rowData = $('#previewGrid').jqxGrid('getrowdata', gridIndex);
            if (rowData && rowData.originalIndex !== undefined) {
                selectedOriginalIndexes.push(rowData.originalIndex);
            } else {
                selectedOriginalIndexes.push(gridIndex); // Fallback to grid index
            }
        });

        console.log('Selected grid indexes:', selectedIndexes);
        console.log('Selected original indexes:', selectedOriginalIndexes);

        const formData = new FormData();
        formData.append('file', selectedFile);
        
        // Append each selected row index
        selectedOriginalIndexes.forEach(function(index) {
            formData.append('selected_rows[]', index);
        });
        
        formData.append('_token', $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("employees.save-preview") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#saveSelectedBtn').prop('disabled', true).text('Importing...');
            },
            success: function(response) {
                console.log('Save response:', response);
                if (response.success) {
                    showAlert('success', `${response.imported_count} employees imported successfully!`);
                    bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
                    resetFileInput();
                    loadEmployees(); // Refresh the grid
                } else {
                    showAlert('danger', response.message || 'Failed to import employees.');
                }
            },
            error: function(xhr) {
                console.log('Save error:', xhr);
                let errorMessage = 'Failed to import employees. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                $('#saveSelectedBtn').prop('disabled', false).text('{{ __("Save Selected") }}');
            }
        });
    });

    // Load employees data
    function loadEmployees() {
        const company = $('#companyFilter').val();
        $.ajax({
            url: '{{ route('employees.index') }}',
            method: 'GET',
            dataType: 'json',
            data: company ? { company: company } : {},
            success: function(data) {
                employeeData = data || [];
                setupGrid();
                updateStats && updateStats();
                toggleEmptyState && toggleEmptyState();
            },
            error: function() {
                employeeData = [];
                setupGrid();
                updateStats && updateStats();
                toggleEmptyState && toggleEmptyState();
            }
        });
    }

    function setupGrid() {
        if (employeeData.length === 0) {
            $('#employeeGrid').empty();
            return;
        }

        const columns = [
            { text: 'Employee ID', datafield: 'employee_id', width: 100 },
            { text: 'Name', datafield: 'name', width: 120 },
            { text: 'Company Name', datafield: 'work_location', width: 120 },
            { text: 'Position', datafield: 'position', width: 100 },
            { text: 'Age', datafield: 'age', width: 120 },
            { text: 'Resident Registration Number', datafield: 'resident_registration_number', width: 180 },
            { text: 'Contact Number', datafield: 'contact_number', width: 120 },
            { text: 'Date of Joining', datafield: 'date_of_joining', width: 120 },
            { text: 'Employment Duration', datafield: 'employment_duration', width: 120 },
            { text: 'Work Days', datafield: 'work_days', width: 100 },
            { text: 'Base Salary', datafield: 'base_salary', width: 100 },
            { 
                text: 'Actions', 
                datafield: 'actions', 
                width: 80, 
                cellsrenderer: function(row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                    return `
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${rowdata.id}" title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${rowdata.id}" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const dataAdapter = new $.jqx.dataAdapter({
            localdata: employeeData
        });

        // Clear and reinitialize grid
        try {
            $('#employeeGrid').jqxGrid('destroy');
        } catch (e) {
            // Grid may not exist yet
        }
        
        $('#employeeGrid').jqxGrid({
            width: '100%',
            height: 400,
            source: dataAdapter,
            columns: columns,
            columnsresize: true,
            sortable: true,
            filterable: true,
            selectionmode: 'none',
            enabletooltips: true,
            ready: function() {
                // Bind action buttons after grid is ready
                bindActionButtons();
            }
        });
    }

    function bindActionButtons() {
        // Edit buttons
        $(document).off('click', '.edit-btn').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const employee = employeeData.find(emp => emp.id == id);
            if (employee) {
                populateEditForm(employee);
                const modal = new bootstrap.Modal(document.getElementById('addEmployeeModal'));
                $('#addEmployeeModalLabel').text('{{ __("employee.management.edit_employee_title") }}');
                $('#addEmployeeForm').attr('data-id', id);
                modal.show();
            }
        });

        // Delete buttons
        $(document).off('click', '.delete-btn').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this employee?')) {
                deleteEmployee(id);
            }
        });
    }

    function populateEditForm(employee) {
        $('#employee_id').val(employee.employee_id);
        $('#name').val(employee.name);
        $('#company_name').val(employee.company_name);
        $('#position').val(employee.position);
        $('#age').val(employee.age);
        $('#resident_registration_number').val(employee.resident_registration_number);
        $('#contact_number').val(employee.contact_number);
        $('#date_of_joining').val(employee.date_of_joining);
        $('#work_days').val(employee.work_days);
        $('#base_salary').val(employee.base_salary);
        $('#employment_status_key').val(employee.employment_status_key || 'active');
    }

    function deleteEmployee(id) {
        $.ajax({
            url: `/employees/${id}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showAlert('success', 'Employee deleted successfully!');
                loadEmployees();
            },
            error: function() {
                showAlert('danger', 'Failed to delete employee.');
            }
        });
    }

    function updateStats() {
        const total = employeeData.length;
        const totalSalary = employeeData.reduce((sum, emp) => sum + (parseFloat(emp.base_salary) || 0), 0);
        const avgWorkDays = employeeData.length > 0 ? 
            employeeData.reduce((sum, emp) => sum + (parseInt(emp.work_days) || 0), 0) / employeeData.length : 0;

        $('#recordCount').text(`{{ __('employee.management.total_employees_display', ['count' => '']) }}${total}`);

        if (total > 0) {
            $('#statsCards').html(`
                <div class="stats-card">
                    <div class="stats-value">${total}</div>
                    <div class="stats-label">{{ __('Total Employees') }}</div>
                </div>
                <div class="stats-card">
                    <div class="stats-value">${totalSalary.toLocaleString()}</div>
                    <div class="stats-label">{{ __('Total Base Salary') }}</div>
                </div>
                <div class="stats-card">
                    <div class="stats-value">${avgWorkDays.toFixed(1)}</div>
                    <div class="stats-label">{{ __('Avg Work Days') }}</div>
                </div>
            `).show();
        } else {
            $('#statsCards').hide();
        }
    }

    function toggleEmptyState() {
        if (employeeData.length === 0) {
            $('#emptyState').show();
            $('#actionButtons, #gridContainer').hide();
        } else {
            $('#emptyState').hide();
            $('#actionButtons, #gridContainer').show();
        }
    }

    // Add/Edit employee modal
    $('#saveEmployeeBtn').click(function() {
        const form = $('#addEmployeeForm');
        const formData = new FormData(form[0]);
        const id = form.attr('data-id');
        
        const url = id ? `/employees/${id}` : '{{ route("employees.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#saveEmployeeBtn').prop('disabled', true);
            },
            success: function(response) {
                showAlert('success', id ? 'Employee updated successfully!' : 'Employee created successfully!');
                bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal')).hide();
                form[0].reset();
                form.removeAttr('data-id');
                $('#addEmployeeModalLabel').text('{{ __("employee.management.add_employee_title") }}');
                loadEmployees();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Please correct the following errors:\n';
                    Object.values(errors).forEach(fieldErrors => {
                        fieldErrors.forEach(error => {
                            errorMessage += `• ${error}\n`;
                        });
                    });
                    showAlert('danger', errorMessage);
                } else {
                    showAlert('danger', 'Failed to save employee.');
                }
            },
            complete: function() {
                $('#saveEmployeeBtn').prop('disabled', false);
            }
        });
    });

    // Delete all employees
    $('#deleteAllBtn').click(function() {
        if (confirm('Are you sure you want to delete all employees? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("employees.delete-all") }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', `${response.deleted_count} employees deleted successfully!`);
                        loadEmployees();
                    } else {
                        showAlert('danger', response.message || 'Failed to delete employees.');
                    }
                },
                error: function() {
                    showAlert('danger', 'Failed to delete employees.');
                }
            });
        }
    });

    // Download template
    $('#downloadTemplateBtn').click(function() {
        window.location.href = '{{ route("employees.template.download") }}';
    });

    // Reset modal when hidden
    $('#addEmployeeModal').on('hidden.bs.modal', function() {
        $('#addEmployeeForm')[0].reset();
        $('#addEmployeeForm').removeAttr('data-id');
        $('#addEmployeeModalLabel').text('{{ __("employee.management.add_employee_title") }}');
    });

    // Reset preview modal when hidden
    $('#previewModal').on('hidden.bs.modal', function() {
        resetFileInput();
        try {
            $('#previewGrid').jqxGrid('clear');
        } catch (e) {
            // Grid may not exist
        }
        previewData = [];
    });

    // Company filter event
    $('#companyFilter').on('change', function() {
        loadEmployees();
    });

    function showAlert(type, message) {
        // Create alert element
        const alertDiv = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('body').append(alertDiv);
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            alertDiv.alert('close');
        }, 5000);
    }
});
</script>
@endsection