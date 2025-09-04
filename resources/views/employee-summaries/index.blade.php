@extends('layouts.app')

@section('title', __('employee_summary.title'))

@section('page-style')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- ApexCharts CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.5/dist/apexcharts.css">
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }
    .container-fluid {
        padding: 24px;
        max-width: 99%;
        margin: 0 auto;
    }
    .page-header {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        color: #6c757d;
    }
    .page-header i {
        margin-right: 8px;
    }
    .page-header .breadcrumb-item {
        font-size: 1rem;
        color: #6c757d;
    }
    .page-header .breadcrumb-item.active {
        background-color: #e9ecef;
        padding: 6px 12px;
        border-radius: 6px;
        color: #495057;
        font-weight: 500;
    }
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        background-color: #fff;
        margin-bottom: 24px;
        height: 250px;
    }
    /* Row container */
    .charts-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        width: 100%;
    }
    /* Default chart column (small screens: stack) */
    .chart-col {
        flex: 1 1 100%;
        min-width: 300px;
    }
    /* Pie chart column (smaller on desktop) */
    .charts-row .chart-col:first-child {
        flex: 0 0 30%;
        max-width: 30%;
    }
    /* Position ratio column (larger on desktop) */
    .charts-row .chart-col:nth-child(2) {
        flex: 0 0 40%;
        max-width: 40%;
    }
    /* Empty column (hidden on small, visible on large) */
    .chart-col.empty {
        display: none;
    }
    @media (min-width: 992px) {
        .chart-col.empty {
            display: block;
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    /* Card header */
    .card-header {
        padding: 12px;
        font-weight: 400;
        font-size: 1rem;
    }
    /* Card body */
    .card-body {
        padding: 16px;
        height: calc(100% - 56px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Pie chart adjustments */
    #total-employees-card .card-body {
        padding: 8px;
    }
    #employeePieChart {
        width: 100%;
        height: 100%;
    }
    /* Position ratio body */
    #position-ratio-card .card-body {
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
        padding: 16px 24px;
        gap: 8px;
    }
    /* Ratio layout */
    .position-ratio-container {
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    .ratio-labels-top,
    .ratio-labels-bottom {
        display: flex;
        width: 100%;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .ratio-labels-bottom {
        color: #333;
    }
    .ratio-item {
        flex: 1;
        text-align: center;
        padding: 0 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Progress bars inside ratio card */
    #position-ratio-card .progress {
        display: flex;
        width: 100%;
        height: 30px;
        margin: 4px 0;
        border-radius: 15px;
        overflow: hidden;
        background-color: #e9ecef;
    }
    #position-ratio-card .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        font-weight: 500;
    }
    #position-ratio-card .progress-bar:first-child {
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }
    #position-ratio-card .progress-bar:last-child {
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }
    .filter-actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding: 12px 16px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        gap: 8px;
        flex-wrap: nowrap;
    }
    .filter-actions-bar .btn,
    .filter-actions-bar select {
        font-size: 14px;
        padding: 8px 16px;
        border-radius: 6px;
        height: 40px;
        margin: 0;
        border: 1px solid #ccc;
        background: transparent;
        white-space: nowrap;
    }
    .filter-actions-bar .filter-group {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
    }
    .filter-actions-bar .actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
    .filter-actions-bar #selected-count {
        font-size: 0.9rem;
        color: #555;
    }
    .filter-actions-bar select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        padding-right: 28px;
    }
    .filter-actions-bar select:focus {
        color: #333;
        background-color: #fff;
        border-color: #86b7fe;
        outline: 0;
        box-shadow: none;
    }
    .table-container {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        width: 100%;
        overflow-x: auto;
    }
    table.custom-table {
        width: 100%;
        border-collapse: collapse;
    }
    table.custom-table th,
    table.custom-table td {
        padding: 12px 10px;
        text-align: left;
        vertical-align: middle;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    table.custom-table thead th {
        background-color: #dee2e6;
        font-weight: 600;
        border-bottom: 2px solid #e3e6f0;
    }
    table.custom-table thead tr.search-row th {
        background-color: #f8f9fa;
        padding: 8px 10px;
    }
    table.custom-table thead th input {
        width: 100%;
        padding: 4px;
        font-size: 0.7rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    table.custom-table tbody tr {
        border-bottom: 1px solid #e3e6f0;
    }
    table.custom-table tbody tr:last-child {
        border-bottom: none;
    }
    table.custom-table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .group-header td {
        font-weight: bold;
        background-color: #dee2e6;
        padding: 10px;
        font-size: 0.9rem;
    }
    .status-badge {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        color: #333;
        white-space: nowrap;
        text-align: center;
        display: inline-block;
        line-height: 1.4;
    }
    .status-active, .status-working {
        background-color: #d1fae5;
        color: #067647;
    }
    .status-resigning, .status-resign-soon {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .status-resigned {
        background-color: #fecaca;
        color: #991b1b;
    }
    .status-on-leave {
        background-color: #fef3c7;
        color: #92400e;
    }
    .action-icon {
        color: #6c757d;
        cursor: pointer;
        font-size: 1.1rem;
    }
    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        background-color: #fff;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }
    .btn-close {
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
        background-size: 0.7em;
    }
    /* Employee Info Section */
    .modal-body .p-4 { padding: 1.5rem !important; }
    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #modal_employee_name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 0.25rem 0;
    }
    #modal_employee_position {
        color: #718096;
        font-size: 0.875rem;
        margin: 0 0 0.5rem 0;
    }
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    /* Info Rows */
    .row.g-3 { margin-top: 1.5rem; }
    .row.g-3 > div { margin-bottom: 0.5rem; }
    .text-muted {
        color: #718096 !important;
        font-size: 0.875rem;
    }
    .fw-medium {
        font-weight: 500;
        color: #2d3748;
    }
    /* Tabs */
    .nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        padding: 0 1.5rem;
        margin: 0;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #718096;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
    }
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background: transparent;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        border-bottom-color: #e2e8f0;
    }
    /* Form Elements */
    .tab-content { padding: 1.5rem !important; }
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .form-control {
        height: 40px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5);
    }
    /* Buttons */
    .btn {
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .btn-outline-secondary {
        color: #4b5563;
        border-color: #d1d5db;
    }
    .btn-outline-secondary:hover {
        background-color: #f9fafb;
        color: #1f2937;
    }
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }
    /* Tables */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #4a5568;
    }
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #718096;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #edf2f7;
    }
    /* Empty States */
    .text-muted { color: #a0aec0 !important; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <i class="fas fa-home"></i>
        <div class="breadcrumb-item">Dashboard</div>
        <span class="mx-2">/</span>
        <div class="breadcrumb-item active">Employee Management</div>
    </div>

    <!-- Charts Section -->
    <div class="charts-row">
        <div class="chart-col">
            <div class="card" id="total-employees-card">
                <div class="card-header">Total Employees</div>
                <div class="card-body">
                    <div id="employeePieChart"></div>
                </div>
            </div>
        </div>
        <div class="chart-col">
            <div class="card" id="position-ratio-card">
                <div class="card-header">Position Ratio</div>
                <div class="card-body">
                    <!-- Position ratio progress bar will be rendered here by JS -->
                </div>
            </div>
        </div>
        <div class="chart-col empty">
            <!-- This column is for spacing on larger screens -->
        </div>
    </div>

    <!-- Filter and Actions Bar -->
    <div class="filter-actions-bar">
        <div class="filter-group">
            <button class="btn btn-outline-secondary" style="width: 120px;" id="registerUser">Register a user</button>
            <select id="positionFilter" class="btn btn-outline-secondary">
                <option value="">Position (Filter)</option>
            </select>
            <select id="statusFilter" class="btn btn-outline-secondary">
                <option value="">Employment Status (Filter)</option>
            </select>
            <select id="serviceFilter" class="btn btn-outline-secondary">
                <option value="">Length of Service (Filter)</option>
            </select>
        </div>
        <div class="actions-group">
            <span id="selected-count">0 selected</span>
            <button class="btn btn-outline-danger" id="deleteSelected">Delete selected user</button>
            <button class="btn btn-outline-primary" id="sendSms">Send a sms</button>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Employee ID</th>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>National ID Number</th>
                    <th>Date of Joining</th>
                    <th>Length of Service</th>
                    <th>Contact Number</th>
                    <th>Base Salary</th>
                    <th>Employment Status</th>
                    <th>Actions</th>
                </tr>
                <tr class="search-row">
                    <th></th>
                    <th><input type="text" class="column-search" data-column="1" placeholder="Search ID"></th>
                    <th><input type="text" class="column-search" data-column="2" placeholder="Search Company"></th>
                    <th><input type="text" class="column-search" data-column="3" placeholder="Search Position"></th>
                    <th><input type="text" class="column-search" data-column="4" placeholder="Search Name"></th>
                    <th><input type="text" class="column-search" data-column="5" placeholder="Search Age"></th>
                    <th><input type="text" class="column-search" data-column="6" placeholder="Search ID Number"></th>
                    <th><input type="text" class="column-search" data-column="7" placeholder="Search Join Date"></th>
                    <th><input type="text" class="column-search" data-column="8" placeholder="Search Service"></th>
                    <th><input type="text" class="column-search" data-column="9" placeholder="Search Contact"></th>
                    <th><input type="text" class="column-search" data-column="10" placeholder="Search Salary"></th>
                    <th><input type="text" class="column-search" data-column="11" placeholder="Search Status"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="employeeTable">
                <!-- Employee rows will be populated by your JavaScript -->
            </tbody>
        </table>
        <div id="emptyState" class="empty-state" style="display: none; padding: 40px; text-align: center;">
            <p>No employees found.</p>
        </div>
    </div>
    <!-- Employee Details Modal -->
    <div class="modal fade" id="employeeDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Employee Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Employee Info Header -->
                    <div class="p-4">
                        <div class="d-flex align-items-start">
                            <div class="avatar me-4">
                                <img id="modal_employee_avatar" src="https://ui-avatars.com/api/?name=Employee&background=random" class="rounded-circle" width="80" height="80" alt="Employee Avatar">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 id="modal_employee_name" class="mb-1 fw-bold">John Doe</h5>
                                        <p id="modal_employee_position" class="text-muted mb-2">Senior Developer</p>
                                        <span id="modal_employee_status" class="badge bg-success">Active</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="border-top">
                        <ul class="nav nav-tabs border-0 px-4" id="employeeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-3 py-3 border-0" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info">
                                    <i class="fas fa-user-circle me-2"></i> Basic Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-3 py-3 border-0" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab" aria-controls="salary">
                                    <i class="fas fa-money-bill-wave me-2"></i> Salary Record
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-3 py-3 border-0" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance">
                                    <i class="fas fa-calendar-alt me-2"></i> Attendance & Leave
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content p-4" id="employeeTabsContent">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                            <form id="employeeDetailsForm">
                                <input type="hidden" id="employee_id" name="id">
                                <h6 class="mb-3 fw-bold">Personal Information</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="employee_name" class="form-label small text-muted mb-1">Full Name</label>
                                        <input type="text" class="form-control" id="employee_name" name="name" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_email" class="form-label small text-muted mb-1">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" class="form-control" id="employee_email" name="email" placeholder="Enter email address" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_phone" class="form-label small text-muted mb-1">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                            <input type="tel" class="form-control" id="employee_phone" name="contact_number" placeholder="+1 (555) 123-4567" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_dob" class="form-label small text-muted mb-1">Date of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-calendar-day text-muted"></i></span>
                                            <input type="date" class="form-control" id="employee_dob" name="date_of_birth" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="employee_address" class="form-label small text-muted mb-1">Address</label>
                                        <textarea class="form-control" id="employee_address" name="address" rows="2" placeholder="Enter full address"></textarea>
                                    </div>
                                </div>
                                <h6 class="mb-3 fw-bold">Work Information</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="employee_employee_id" class="form-label small text-muted mb-1">Employee ID</label>
                                        <input type="text" class="form-control" id="employee_employee_id" name="employee_id" placeholder="EMP-001" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_department" class="form-label small text-muted mb-1">Department</label>
                                        <select class="form-select" id="employee_department" name="department" required>
                                            <option value="">Select Department</option>
                                            <option value="IT">IT</option>
                                            <option value="HR">Human Resources</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Operations">Operations</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_position" class="form-label small text-muted mb-1">Position</label>
                                        <input type="text" class="form-control" id="employee_position" name="position" placeholder="Enter position" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employee_joining_date" class="form-label small text-muted mb-1">Joining Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-muted"></i></span>
                                            <input type="date" class="form-control" id="employee_joining_date" name="date_of_joining" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        <!-- Salary Record Tab -->
                        <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                            <p class="text-muted text-center">Salary records will be displayed here.</p>
                        </div>
                        <!-- Attendance & Leave Tab -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                            <p class="text-muted text-center">Attendance and leave records will be displayed here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* Modal Styles */
    .employee-edit-modal .modal-dialog {
        max-width: 1000px;
        width: 95%;
    }
    .employee-edit-modal .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .employee-edit-modal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1.25rem 1.5rem;
        position: relative;
    }
    .employee-edit-modal .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    .employee-edit-modal .modal-body {
        padding: 0;
    }
    
    /* Tabs Styling */
    .employee-edit-modal .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        padding: 0 1.5rem;
        margin: 0;
        display: flex;
        flex-wrap: nowrap;
    }
    .employee-edit-modal .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .employee-edit-modal .nav-tabs .nav-link:hover {
        border-color: #dee2e6;
    }
    .employee-edit-modal .nav-tabs .nav-link.active {
        color: #0d6efd;
        background: none;
        border-color: #0d6efd;
    }
    .employee-edit-modal .nav-tabs .nav-link i {
        margin-right: 0.5rem;
    }
    .employee-edit-modal .tab-content {
        padding: 1.5rem;
    }
    .employee-edit-modal .section-column {
        padding: 1.25rem;
        border-right: 1px solid #e9ecef;
        height: 100%;
    }
    .employee-edit-modal .section-column:last-child {
        border-right: none;
    }
    .employee-edit-modal .section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
    }
    .employee-edit-modal .section-title i {
        margin-right: 0.5rem;
        color: #6c757d;
    }
    .employee-edit-modal .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.3rem;
        color: #495057;
    }
    .employee-edit-modal .form-control, 
    .employee-edit-modal .form-select, 
    .employee-edit-modal .input-group-text {
        font-size: 0.875rem;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
    .employee-edit-modal .form-control:focus, 
    .employee-edit-modal .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .employee-edit-modal .table {
        font-size: 0.8rem;
    }
    .employee-edit-modal .table th {
        font-weight: 500;
        background-color: #f8f9fa;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .employee-edit-modal .table td {
        vertical-align: middle;
    }
    .employee-edit-modal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }
</style>
@endpush

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="editEmployeeModalLabel">Employee Details</h5>
                    <p class="text-muted mb-0 small" id="employeePosition">Position</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEmployeeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_employee_id">
                
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs border-0 px-4 pt-2" id="employeeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info">
                            Basic Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab" aria-controls="salary">
                            Salary
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance">
                            Attendance
                        </button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_position" class="form-label">Position</label>
                            <select class="form-select" id="edit_position" name="position">
                                <option value="">Select Position</option>
                                <option value="Manager">Manager</option>
                                <option value="Developer">Developer</option>
                                <option value="Designer">Designer</option>
                                <option value="HR">HR</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_department" class="form-label">Department</label>
                            <select class="form-select" id="edit_department" name="department">
                                <option value="">Select Department</option>
                                <option value="IT">IT</option>
                                <option value="HR">Human Resources</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_hire_date" class="form-label">Hire Date</label>
                            <input type="date" class="form-control" id="edit_hire_date" name="hire_date">
                        </div>
                    </div>
                    
                    <!-- Salary Tab -->
                    <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                        <div class="mb-3">
                            <label for="edit_basic_salary" class="form-label">Basic Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_basic_salary" name="basic_salary" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_housing_allowance" class="form-label">Housing Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_housing_allowance" name="housing_allowance" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_transport_allowance" class="form-label">Transport Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_transport_allowance" name="transport_allowance" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_meal_allowance" class="form-label">Meal Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_meal_allowance" name="meal_allowance" step="0.01">
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Salary:</span>
                                <strong id="total_salary_display">$0.00</strong>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted mb-3">Attendance Summary</h6>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="mb-1 small text-muted">Present</p>
                                                    <h4 class="mb-0 attendance-present">0</h4>
                                                </div>
                                                <div class="bg-soft-primary p-3 rounded">
                                                    <i class="fas fa-calendar-check text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="mb-1 small text-muted">Absent</p>
                                                    <h4 class="mb-0 attendance-absent">0</h4>
                                                </div>
                                                <div class="bg-soft-danger p-3 rounded">
                                                    <i class="fas fa-calendar-times text-danger"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="mb-1 small text-muted">Late</p>
                                                    <h4 class="mb-0 attendance-late">0</h4>
                                                </div>
                                                <div class="bg-soft-warning p-3 rounded">
                                                    <i class="fas fa-clock text-warning"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted mb-3">Leave Balance</h6>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="mb-1 small text-muted">Annual Leave</p>
                                                    <h4 class="mb-0 leave-annual">0</h4>
                                                </div>
                                                <div class="bg-soft-info p-3 rounded">
                                                    <i class="fas fa-umbrella-beach text-info"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="mb-1 small text-muted">Sick Leave</p>
                                                    <h4 class="mb-0 leave-sick">0</h4>
                                                </div>
                                                <div class="bg-soft-success p-3 rounded">
                                                    <i class="fas fa-procedures text-success"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="mb-1 small text-muted">Other Leave</p>
                                                    <h4 class="mb-0 leave-other">0</h4>
                                                </div>
                                                <div class="bg-soft-secondary p-3 rounded">
                                                    <i class="fas fa-calendar-minus text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">Recent Attendance</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Check In</th>
                                                    <th>Check Out</th>
                                                    <th>Status</th>
                                                    <th>Working Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attendanceTableBody">
                                                <!-- Dynamic content will be inserted here by JavaScript -->
                                                <tr id="noRecordsMessage">
                                                    <td colspan="5" class="text-center py-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-calendar-times text-muted mb-2" style="font-size: 2rem;"></i>
                                                            <p class="text-muted mb-0">No attendance records found</p>
                                                            <small class="text-muted">Attendance data will appear here once available</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.5/dist/apexcharts.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    let employeeData = [];
    
    // Calculate total salary when any salary input changes
    function calculateTotalSalary() {
        const basic = parseFloat($('#edit_basic_salary').val()) || 0;
        const housing = parseFloat($('#edit_housing_allowance').val()) || 0;
        const transport = parseFloat($('#edit_transport_allowance').val()) || 0;
        const meal = parseFloat($('#edit_meal_allowance').val()) || 0;
        const total = basic + housing + transport + meal;
        $('#total_salary_display').text('$' + total.toFixed(2));
        return total;
    }

    // Handle salary input changes
    $(document).on('input', '#edit_basic_salary, #edit_housing_allowance, #edit_transport_allowance, #edit_meal_allowance', calculateTotalSalary);

    // Handle edit button click
    $(document).on('click', '.edit-btn', function() {
        const employeeId = $(this).data('id');
        const employee = employeeData.find(emp => emp.id == employeeId);
        
        if (employee) {
            // Set modal title and position
            $('#editEmployeeModalLabel').text(employee.name || 'Employee Details');
            $('#employeePosition').text(employee.position || 'Position not specified');
            
            // Populate form fields
            $('#edit_employee_id').val(employee.id || '');
            $('#edit_name').val(employee.name || '');
            $('#edit_email').val(employee.email || '');
            $('#edit_phone').val(employee.phone || employee.contact_number || '');
            $('#edit_position').val(employee.position || '');
            $('#edit_department').val(employee.department || '');
            $('#edit_hire_date').val(employee.hire_date || employee.date_of_joining || '');
            
            // Set salary information
            $('#edit_basic_salary').val(employee.salary || '');
            $('#edit_housing_allowance').val(employee.housing_allowance || '0');
            $('#edit_transport_allowance').val(employee.transport_allowance || '0');
            $('#edit_meal_allowance').val(employee.meal_allowance || '0');
            
            // Calculate and display total salary
            calculateTotalSalary();
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
            editModal.show();
            
            // Load attendance data (example with mock data - replace with actual API call)
            // This is just for demonstration - replace with your actual data loading logic
            const mockAttendanceData = []; // Empty array for no records
            /*
            // Example with data:
            const mockAttendanceData = [
                { date: '2023-10-01', status: 'Present', checkIn: '08:55 AM', checkOut: '05:10 PM', workingHours: '8h 15m' },
                { date: '2023-10-02', status: 'Present', checkIn: '09:05 AM', checkOut: '05:00 PM', workingHours: '7h 55m' },
                { date: '2023-10-03', status: 'Late', checkIn: '09:15 AM', checkOut: '05:30 PM', workingHours: '8h 15m' }
            ];
            */
            
            // In a real implementation, you would make an AJAX call here:
            /*
            $.get(`/api/employees/${employeeId}/attendance`, function(data) {
                updateAttendanceUI(data);
            }).fail(function() {
                console.error('Failed to load attendance data');
                updateAttendanceUI([]); // Show no records message
            });
            */
            
            // For now, using the mock data
            updateAttendanceUI(mockAttendanceData);
        }
    });

    // Function to update attendance UI with data
    function updateAttendanceUI(attendanceData) {
        const $tableBody = $('#attendanceTableBody');
        const $noRecordsMessage = $('#noRecordsMessage');
        
        // Clear existing rows
        $tableBody.find('tr:not(#noRecordsMessage)').remove();
        
        if (attendanceData && attendanceData.length > 0) {
            // Hide the no records message
            $noRecordsMessage.hide();
            
            // Add attendance rows
            attendanceData.forEach(record => {
                let statusClass = 'bg-success';
                if (record.status === 'Late') statusClass = 'bg-warning';
                if (record.status === 'Absent') statusClass = 'bg-danger';
                
                const row = `
                    <tr>
                        <td>${record.date}</td>
                        <td>${record.checkIn || '-'}</td>
                        <td>${record.checkOut || '-'}</td>
                        <td><span class="badge ${statusClass}">${record.status}</span></td>
                        <td>${record.workingHours || '-'}</td>
                    </tr>
                `;
                $tableBody.append(row);
            });
            
            // Update summary cards if needed
            if (attendanceData.summary) {
                const s = attendanceData.summary;
                $('.attendance-present').text(s.present || 0);
                $('.attendance-absent').text(s.absent || 0);
                $('.attendance-late').text(s.late || 0);
                $('.leave-annual').text(s.annualLeave || '0');
                $('.leave-sick').text(s.sickLeave || '0');
                $('.leave-other').text(s.otherLeave || '0');
            }
        } else {
            // Show no records message
            $noRecordsMessage.show();
            
            // Reset summary cards
            $('.attendance-present, .attendance-absent, .attendance-late').text('0');
            $('.leave-annual, .leave-sick, .leave-other').text('0');
        }
    }
    
    // Handle form submission
    $('#editEmployeeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const employeeId = $('#edit_employee_id').val();
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        // Send AJAX request
        $.ajax({
            url: employeeId ? `/employees/${employeeId}` : '/employees',
            type: employeeId ? 'PUT' : 'POST',
            data: formData,
            success: function(response) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Employee information saved successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Close modal and refresh data
                const modal = bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal'));
                modal.hide();
                
                // Refresh employee data
                fetchEmployeeData();
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving the employee information.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    function fetchEmployeeData() {
        $.ajax({
            url: '{{ route("employees.index") }}',
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                employeeData = response || [];
                initializeTable(employeeData);
                updateCharts(employeeData);
                updatePositionRatio(employeeData);
                populateFilters();
            },
            error: function(xhr) {
                console.error('Error fetching employee data:', xhr.responseText);
                $('#emptyState').show().html('<p>Failed to load employee data.</p>');
                $('.table-container').hide();
                updateCharts([]);
                updatePositionRatio([]);
            }
        });
    }

    function initializeTable(data) {
        const tableBody = $('#employeeTable');
        tableBody.empty();
        if (data.length > 0) {
            tableBody.append(`<tr class="no-results-row" style="display: none;"><td colspan="13" style="text-align: center; padding: 20px; color: #6c757d;">No employees match the current search criteria.</td></tr>`);
            const groupedByPosition = data.reduce((acc, emp) => { (acc[emp.position] = acc[emp.position] || []).push(emp); return acc; }, {});
            for (const position in groupedByPosition) {
                tableBody.append(`<tr class="group-header"><td colspan="13">${position}</td></tr>`);
                groupedByPosition[position].forEach(emp => {
                    const statusBadge = getStatusBadge(emp.employment_status_key, emp.status_details || '');
                    const row = `
                        <tr class="employee-row" data-group="${emp.position}" data-status="${emp.employment_status_key}" data-service="${classifyService(parseDuration(emp.employment_duration))}" data-id="${emp.id}">
                            <td><input type="checkbox" class="rowCheckbox" data-id="${emp.id}"></td>
                            <td>${emp.employee_id || ''}</td>
                            <td>${emp.company || emp.work_location || ''}</td>
                            <td>${emp.position || ''}</td>
                            <td>${emp.name || ''}</td>
                            <td>${emp.age || ''}</td>
                            <td>${emp.resident_registration_number || ''}</td>
                            <td>${emp.date_of_joining || ''}</td>
                            <td>${emp.employment_duration || ''}</td>
                            <td>${emp.contact_number || ''}</td>
                            <td>${emp.base_salary ? Number(emp.base_salary).toLocaleString() : '0'}</td>
                            <td>${statusBadge}</td>
                            <td class="action-buttons">
                                <i class="fas fa-pencil-alt action-icon edit-btn" data-id="${emp.id}" title="Edit"></i>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            }
            $('.table-container').show(); $('#emptyState').hide();
        } else {
            $('.table-container').hide(); $('#emptyState').show();
        }
    }

    function updatePositionRatio(data) {
        const cardBody = $('#position-ratio-card .card-body'); cardBody.empty();
        if (data.length === 0) { cardBody.html('<div style="text-align:center; color:#999;">No data to display</div>'); return; }
        const positionStats = data.reduce((acc, emp) => { acc[emp.position] = (acc[emp.position] || 0) + 1; return acc; }, {});
        const sortedPositions = Object.entries(positionStats).sort(([, a], [, b]) => b - a); const topPositions = sortedPositions.slice(0, 5);
        const totalEmployeesInChart = topPositions.reduce((sum, [, count]) => sum + count, 0);
        const container = $('<div class="position-ratio-container"></div>'); const topLabels = $('<div class="ratio-labels-top"></div>');
        const progressBar = $('<div class="progress"></div>'); const bottomLabels = $('<div class="ratio-labels-bottom"></div>');
        const colors = ['#6c757d', '#dc3545', '#0d6efd', '#198754', '#ffc107'];
        topPositions.forEach(([position, count], index) => {
            const percentage = totalEmployeesInChart > 0 ? (count / totalEmployeesInChart) * 100 : 0;
            topLabels.append(`<div class="ratio-item" style="flex-basis: ${percentage}%">${position}</div>`);
            progressBar.append(`<div class="progress-bar" style="width: ${percentage}%; background-color: ${colors[index % colors.length]};">${percentage.toFixed(1)}%</div>`);
            bottomLabels.append(`<div class="ratio-item" style="flex-basis: ${percentage}%">${position} : ${count}</div>`);
        });
        container.append(topLabels).append(progressBar).append(bottomLabels); cardBody.append(container);
    }

    // ** EDITED AND FIXED **: This function now has a corrected plotOptions configuration.
    function updateCharts(data) {
        const pieChartEl = document.getElementById('employeePieChart');
        if (pieChartEl && pieChartEl.chart) { pieChartEl.chart.destroy(); }
        const totalEmployees = data ? data.length : 0;
        const employeeStats = (data || []).reduce((acc, emp) => { const key = emp.employment_status_key || 'unknown'; acc[key] = (acc[key] || 0) + 1; return acc; }, {});
        const masterStatusList = [
            { key: 'active', alias: 'working', text: 'Working', color: '#22c55e' }, { key: 'resigned', text: 'Resigned', color: '#ef4444' },
            { key: 'on_leave', text: 'On Leave', color: '#3b82f6' }, { key: 'resigning', alias: 'resign-soon', text: 'Sign In Soon', color: '#f97316' }
        ];
        const labels = []; const series = []; const colors = [];
        masterStatusList.forEach(statusInfo => {
            const count = (employeeStats[statusInfo.key] || 0) + (employeeStats[statusInfo.alias] || 0);
            labels.push(statusInfo.text); series.push(count); colors.push(statusInfo.color);
        });
        const chartConfig = {
            chart: { height: '100%', type: 'donut' }, series: series, labels: labels, colors: colors, stroke: { width: 0 }, dataLabels: { enabled: false },
            legend: { show: true, position: 'right', horizontalAlign: 'center', offsetY: 0, fontSize: '14px', formatter: (seriesName, opts) => `${seriesName} : ${opts.w.globals.series[opts.seriesIndex]}`, itemMargin: { vertical: 4 } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '80%',
                        labels: {
                            show: true,
                            // ** EDITED **: Simplified and corrected 'total' object
                            total: {
                                show: true,
                                showAlways: true,
                                label: 'employees',
                                fontSize: '0.9rem', // Adjusted size for the label
                                color: '#6c757d',
                                formatter: () => totalEmployees
                            }
                        }
                    }
                }
            },
            responsive: [{ breakpoint: 1200, options: { legend: { position: 'bottom', horizontalAlign: 'center' } } }]
        };
        const chart = new ApexCharts(pieChartEl, chartConfig); chart.render(); pieChartEl.chart = chart;
    }

    function getStatusBadge(statusKey, details) {
        let badgeClass = ''; let statusText = '';
        const detailsText = details ? ` ${details}` : '';
        switch(statusKey) {
            case 'active': case 'working': badgeClass = 'status-working'; statusText = 'Working'; break;
            case 'resigning': case 'resign-soon': badgeClass = 'status-resign-soon'; statusText = `Resign Soon${detailsText}`; break;
            case 'resigned': badgeClass = 'status-resigned'; statusText = `Resigned${detailsText}`; break;
            case 'on_leave': badgeClass = 'status-on-leave'; statusText = `On Leave${detailsText}`; break;
            default: statusText = 'Unknown';
        }
        return `<span class="status-badge ${badgeClass}">${statusText}</span>`;
    }

    function parseDuration(duration) { if (!duration) return 0; const [years, months] = duration.split('y ').map(part => parseInt(part) || 0); return (years * 12) + months; }
    function classifyService(durationMonths) { return durationMonths < 12 ? 'less_than_1' : durationMonths <= 36 ? '1_to_3' : durationMonths <= 60 ? '3_to_5' : 'over_5'; }

    function populateFilters() {
        const positions = [...new Set(employeeData.map(emp => emp.position).filter(Boolean))];
        const positionFilter = $('#positionFilter'); positionFilter.empty().append('<option value="">Position (Filter)</option>');
        positions.forEach(pos => positionFilter.append(`<option value="${pos}">${pos}</option>`));
        const statusFilter = $('#statusFilter'); statusFilter.empty().append('<option value="">Employment Status (Filter)</option>');
        const statusOptions = [
            { value: 'active', text: 'Working' }, { value: 'resigned', text: 'Resigned' }, { value: 'on_leave', text: 'On Leave' }, { value: 'resigning', text: 'Sign In Soon' }
        ];
        statusOptions.forEach(option => statusFilter.append(`<option value="${option.value}">${option.text}</option>`));
        const serviceFilter = $('#serviceFilter'); serviceFilter.empty().append('<option value="">Length of Service (Filter)</option>');
        ['less_than_1', '1_to_3', '3_to_5', 'over_5'].forEach(cls => serviceFilter.append(`<option value="${cls}">${cls.replace(/_/g, ' ').replace('less than 1', 'Less than 1 Year').replace('to', ' to ').replace('over 5', 'Over 5 Years')}</option>`));
    }

    function applyFilters() {
        const positionFilter = $('#positionFilter').val(); const statusFilter = $('#statusFilter').val(); const serviceFilter = $('#serviceFilter').val();
        const searchValues = {};
        $('.column-search').each(function() {
            const col = $(this).data('column'); const val = $(this).val().toLowerCase();
            if (val) searchValues[col] = val;
        });
        let visibleRowCount = 0;
        let filteredDataForCharts = [];
        $('#employeeTable .employee-row').each(function() {
            const row = $(this);
            const matchesPosition = !positionFilter || row.data('group') === positionFilter;
            let matchesStatus = !statusFilter;
            if (statusFilter) {
                const rowStatus = row.data('status');
                if (statusFilter === 'active') { matchesStatus = (rowStatus === 'active' || rowStatus === 'working'); }
                else if (statusFilter === 'resigning') { matchesStatus = (rowStatus === 'resigning' || rowStatus === 'resign-soon'); }
                else { matchesStatus = rowStatus === statusFilter; }
            }
            const matchesService = !serviceFilter || row.data('service') === serviceFilter;
            let matchesSearch = true;
            for (const col in searchValues) {
                const cellText = row.find('td').eq(col).text().toLowerCase();
                if (!cellText.includes(searchValues[col])) {
                    matchesSearch = false;
                    break;
                }
            }
            if (matchesPosition && matchesStatus && matchesService && matchesSearch) {
                row.show();
                visibleRowCount++;
                const empId = row.find('.rowCheckbox').data('id');
                const emp = employeeData.find(e => e.id === empId);
                if(emp) filteredDataForCharts.push(emp);
            } else {
                row.hide();
            }
        });
        $('#employeeTable .group-header').each(function() {
            const groupName = $(this).find('td').text();
            const groupHasVisibleRows = $(`#employeeTable .employee-row[data-group="${groupName}"]:visible`).length > 0;
            $(this).toggle(groupHasVisibleRows);
        });
        if (visibleRowCount === 0 && employeeData.length > 0) {
            $('#employeeTable .no-results-row').show();
        } else {
            $('#employeeTable .no-results-row').hide();
        }
        updateCharts(filteredDataForCharts);
        updatePositionRatio(filteredDataForCharts);
    }

    fetchEmployeeData();
    
    // Initialize Bootstrap modal and tabs
    var editEmployeeModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
    var employeeTabs = new bootstrap.Tab(document.querySelector('#basic-info-tab'));
    
    // Event Listeners
    $('#employeeTable').on('change', '.rowCheckbox', updateSelectedCount)
        .on('click', '.edit-btn', function() {
            const employeeId = $(this).data('id');
            const employee = employeeData.find(emp => emp.id == employeeId);
            
            if (employee) {
                // Basic Info Tab
                $('#edit_employee_id').val(employee.id);
                $('#edit_name').val(employee.name || '');
                $('#edit_email').val(employee.email || '');
                $('#edit_phone').val(employee.phone || '');
                $('#edit_position').val(employee.position || '');
                $('#edit_department').val(employee.department || '');
                $('#edit_hire_date').val(employee.hire_date || '');
                
                // Salary Tab (View Only)
                $('#view_salary').text(parseFloat(employee.salary || 0).toFixed(2));
                $('#view_housing').text(parseFloat(employee.housing_allowance || 0).toFixed(2));
                $('#view_transport').text(parseFloat(employee.transport_allowance || 0).toFixed(2));
                $('#view_meal').text(parseFloat(employee.meal_allowance || 0).toFixed(2));
                
                // Calculate total salary
                const totalSalary = (parseFloat(employee.salary || 0) + 
                                   parseFloat(employee.housing_allowance || 0) + 
                                   parseFloat(employee.transport_allowance || 0) + 
                                   parseFloat(employee.meal_allowance || 0)).toFixed(2);
                $('#view_total_salary').text(totalSalary);
                
                // Set payment details
                $('#view_payment_frequency').text(employee.payment_frequency || 'Monthly');
                $('#view_account_number').text(employee.account_number || '-');
                
                // Set last updated time
                const now = new Date();
                $('#lastUpdated').text(now.toLocaleString());
                
                // Show the modal and activate first tab
                editEmployeeModal.show();
                employeeTabs.show();
            }
        });
    
    // Save Basic Info
    $('#saveBasicInfo').on('click', function() {
        const formData = $('#editEmployeeForm').serialize();
        const employeeId = $('#edit_employee_id').val();
        const $btn = $(this);
        const originalBtnText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        // Send AJAX request to update employee
        $.ajax({
            url: `/employees/${employeeId}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Employee information updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update last updated time
                const now = new Date();
                $('#lastUpdated').text(now.toLocaleString());
                
                // Refresh the employee data
                fetchEmployeeData();
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the employee.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Load attendance data when attendance tab is shown
    $('#attendance-tab').on('shown.bs.tab', function() {
        const employeeId = $('#edit_employee_id').val();
        if (employeeId) {
            // Simulate loading attendance data
            setTimeout(() => {
                $('#attendance_present').text('22');
                $('#attendance_absent').text('3');
                $('#attendance_late').text('2');
                
                // Simulate leave balance
                $('#leave_annual').text('12 days');
                $('#leave_sick').text('7 days');
                $('#leave_other').text('3 days');
                
                // Simulate attendance history
                const attendanceData = [
                    { date: '2023-10-01', status: 'Present', checkIn: '08:55', checkOut: '17:10', hours: '8:15' },
                    { date: '2023-10-02', status: 'Present', checkIn: '09:05', checkOut: '17:00', hours: '7:55' },
                    { date: '2023-10-03', status: 'Late', checkIn: '09:15', checkOut: '17:30', hours: '8:15' },
                    { date: '2023-10-04', status: 'Present', checkIn: '08:50', checkOut: '17:05', hours: '8:15' },
                    { date: '2023-10-05', status: 'Present', checkIn: '09:00', checkOut: '17:00', hours: '8:00' }
                ];
                
                const tbody = $('#attendanceHistoryBody');
                tbody.empty();
                
                if (attendanceData.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center text-muted py-3">No attendance records found</td></tr>');
                } else {
                    attendanceData.forEach(item => {
                        tbody.append(`
                            <tr>
                                <td>${item.date}</td>
                                <td><span class="badge bg-${item.status === 'Present' ? 'success' : item.status === 'Late' ? 'warning' : 'danger'}">${item.status}</span></td>
                                <td>${item.checkIn}</td>
                                <td>${item.checkOut}</td>
                                <td>${item.hours}</td>
                            </tr>
                        `);
                    });
                }
            }, 500);
        }
    });
    $('#selectAll').on('change', function() { $('#employeeTable .rowCheckbox:visible').prop('checked', $(this).prop('checked')); updateSelectedCount(); });
    function updateSelectedCount() { $('#selected-count').text(`${$('.rowCheckbox:checked').length} selected`); }
    $('#registerUser').on('click', () => window.location.href = '{{ route("employees.create") }}');
    $('#positionFilter, #statusFilter, #serviceFilter').on('change', applyFilters);
    $('.column-search').on('input', applyFilters);

    $('#deleteSelected').on('click', function() {
        const selectedIds = $('.rowCheckbox:checked').map(function() { return $(this).data('id'); }).get();
        if (selectedIds.length > 0) {
            Swal.fire({ title: 'Are you sure?', text: `You are about to delete ${selectedIds.length} employee(s).`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("employees.bulk-delete") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        data: JSON.stringify({ ids: selectedIds }),
                        success: (response) => { 
                            if (response.success) { 
                                Swal.fire('Deleted!', response.message || `${response.count} employees have been deleted.`, 'success'); 
                                fetchEmployeeData(); 
                            } 
                        },
                        error: (xhr) => {
                            console.error('Error deleting employees:', xhr);
                            const errorMessage = xhr.responseJSON?.message || 'Failed to delete employees. Please try again.';
                            Swal.fire('Error', errorMessage, 'error');
                        }
                    });
                }
            });
        } else { Swal.fire('No selection', 'Please select at least one employee.', 'info'); }
    });

    $('#sendSms').on('click', function() {
        const selectedCount = $('.rowCheckbox:checked').length;
        if (selectedCount > 0) { Swal.fire('Send SMS', `This will send an SMS to ${selectedCount} employee(s).`, 'info');
        } else { Swal.fire('No selection', 'Please select at least one employee.', 'info'); }
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.stopPropagation();
        
        const button = $(this);
        const employeeId = button.data('id');
        const employeeName = button.data('name');
        
        Swal.fire({
            title: 'Delete Employee',
            text: `Are you sure you want to delete ${employeeName}? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                $.ajax({
                    url: `/employee-summaries/${employeeId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            response.message || 'Employee has been deleted.',
                            'success'
                        );
                        // Remove the row from the table
                        $(`tr[data-id="${employeeId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            updateCharts(employeeData);
                            updatePositionRatio(employeeData);
                            
                            // If no more rows, show empty state
                            if ($('.employee-row').length === 0) {
                                $('.table-container').hide();
                                $('#emptyState').show();
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error deleting employee:', xhr.responseText);
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Failed to delete employee. Please try again.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    
    // Handle delete selected button click
    $('#deleteSelectedBtn').on('click', function() {
        const selectedCheckboxes = $('.rowCheckbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            Swal.fire({
                title: 'No Selection',
                text: 'Please select at least one employee to delete.',
                icon: 'warning',
                confirmButtonColor: '#6c757d',
            });
            return;
        }
        
        Swal.fire({
            title: 'Delete Selected Employees',
            text: `Are you sure you want to delete ${selectedCheckboxes.length} selected employee(s)? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, delete ${selectedCheckboxes.length} employee(s)`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const employeeIds = [];
                selectedCheckboxes.each(function() {
                    employeeIds.push($(this).data('id'));
                });
                
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                $.ajax({
                    url: '{{ route("employee-summaries.delete-all") }}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ ids: employeeIds }),
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            response.message || 'Selected employees have been deleted.',
                            'success'
                        );
                        // Remove the rows from the table
                        employeeIds.forEach(id => {
                            $(`tr[data-id="${id}"]`).remove();
                        });
                        
                        // Update charts and position ratio
                        employeeData = employeeData.filter(emp => !employeeIds.includes(emp.id));
                        updateCharts(employeeData);
                        updatePositionRatio(employeeData);
                        
                        // If no more rows, show empty state
                        if ($('.employee-row').length === 0) {
                            $('.table-container').hide();
                            $('#emptyState').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error deleting employees:', xhr.responseText);
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Failed to delete selected employees. Please try again.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    
    // Search functionality
    $('#nameSearch').on('keyup', function() {
        const selectedCount = $('.rowCheckbox:checked').length;
        if (selectedCount > 0) { Swal.fire('Send SMS', `This will send an SMS to ${selectedCount} employee(s).`, 'info');
        } else { Swal.fire('No selection', 'Please select at least one employee.', 'info'); }
    });
});
</script>
@endpush