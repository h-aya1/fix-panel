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
        width: 100%;
        height: 250px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
        padding: 16px;
        font-weight: 600;
        font-size: 1rem;
    }

    .card-body {
        padding: 16px;
        height: calc(100% - 56px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #total-employees-card .card-body {
       padding: 8px;
    }
    #employeePieChart {
        width: 100%;
        height: 100%;
    }
    
    #position-ratio-card .card-body {
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
        padding: 16px 24px;
        gap: 8px;
    }
    .position-ratio-container {
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    .ratio-labels-top, .ratio-labels-bottom {
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
    .filter-actions-bar .btn, .filter-actions-bar select {
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
    table.custom-table th, table.custom-table td {
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
    .status-active, .status-working { background-color: #d1fae5; color: #067647; }
    .status-resigning, .status-resign-soon { background-color: #fee2e2; color: #b91c1c; }
    .status-resigned { background-color: #fecaca; color: #991b1b; }
    .status-on-leave { background-color: #fef3c7; color: #92400e; }

    .action-icon {
        color: #6c757d;
        cursor: pointer;
        font-size: 1.1rem;
    }

    .charts-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }
    .chart-col {
        flex: 1 1 calc(33.333% - 16px);
        min-width: 300px;
    }
    .chart-col.empty {
        display: none;
    }
    @media (min-width: 992px) {
        .chart-col.empty {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <i class="fas fa-home"></i>
        <div class="breadcrumb-item">Dashboard</div>
        <span class="mx-2">/</span>
        <div class="breadcrumb-item active">employee-managment</div>
    </div>

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
                </div>
            </div>
        </div>
        <div class="chart-col empty">
        </div>
    </div>
    
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

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Employee ID</th><th>Company</th><th>Position</th><th>Name</th><th>Age</th><th>National ID Number</th><th>Date of Joining</th><th>Length of Service</th><th>Contact Number</th><th>Base Salary</th><th>Employment Status</th><th>Actions</th>
                </tr>
                <tr class="search-row">
                    <th></th>
                    <th><input type="text" class="column-search" data-column="1"></th><th><input type="text" class="column-search" data-column="2"></th><th><input type="text" class="column-search" data-column="3"></th><th><input type="text" class="column-search" data-column="4"></th><th><input type="text" class="column-search" data-column="5"></th><th><input type="text" class="column-search" data-column="6"></th><th><input type="text" class="column-search" data-column="7"></th><th><input type="text" class="column-search" data-column="8"></th><th><input type="text" class="column-search" data-column="9"></th><th><input type="text" class="column-search" data-column="10"></th><th><input type="text" class="column-search" data-column="11"></th><th></th>
                </tr>
            </thead>
            <tbody id="employeeTable">
            </tbody>
        </table>
        <div id="emptyState" class="empty-state" style="display: none; padding: 40px; text-align: center;">
            <p>No employees found.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.5/dist/apexcharts.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let employeeData = [];
    
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
                        <tr class="employee-row" data-group="${emp.position}" data-status="${emp.employment_status_key}" data-service="${classifyService(parseDuration(emp.employment_duration))}">
                            <td><input type="checkbox" class="rowCheckbox" data-id="${emp.id}"></td><td>${emp.employee_id || ''}</td><td>${emp.work_location || ''}</td><td>${emp.position || ''}</td><td>${emp.name || ''}</td><td>${emp.age || ''}</td><td>${emp.resident_registration_number || ''}</td><td>${emp.date_of_joining || ''}</td><td>${emp.employment_duration || ''}</td><td>${emp.contact_number || ''}</td><td>${emp.base_salary ? Number(emp.base_salary).toLocaleString() : '0'}</td><td>${statusBadge}</td><td><i class="fas fa-pencil-alt action-icon edit-btn" data-id="${emp.id}"></i></td>
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
    
    // Event Listeners
    $('#employeeTable').on('change', '.rowCheckbox', updateSelectedCount).on('click', '.edit-btn', function() { window.location.href = `/employees/${$(this).data('id')}/edit`; });
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
                        url: '{{ route("employees.delete-all") }}', method: 'DELETE', data: { ids: selectedIds, _token: '{{ csrf_token() }}' },
                        success: (response) => { if (response.success) { Swal.fire('Deleted!', `${response.deleted_count} employees have been deleted.`, 'success'); fetchEmployeeData(); } },
                        error: () => Swal.fire('Error', 'Failed to delete employees.', 'error')
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
});
</script>
@endpush