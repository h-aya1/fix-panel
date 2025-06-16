@extends('layouts.app')

@section('title', __('employee.management.edit_employee_title'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('employee.management.edit_employee_title') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">{{ __('employee.table.header.employee_id') }}*</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('employee.table.header.name') }}*</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="age" class="form-label">{{ __('employee.table.header.age') }}</label>
                                <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $employee->age) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ssn" class="form-label">{{ __('employee.table.header.ssn') }}</label>
                                <input type="text" class="form-control" id="ssn" name="ssn" value="{{ old('ssn', $employee->ssn) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">{{ __('employee.table.header.contact') }}</label>
                            <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact', $employee->contact) }}">
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="work_location" class="form-label">{{ __('employee.table.header.work_location') }}*</label>
                            <input type="text" class="form-control" id="work_location" name="work_location" value="{{ old('work_location', $employee->work_location) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">{{ __('employee.table.header.position') }}*</label>
                            <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="join_date" class="form-label">{{ __('employee.table.header.join_date') }}*</label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_period" class="form-label">{{ __('employee.table.header.service_period') }}</label>
                                <input type="text" class="form-control" id="service_period" name="service_period" value="{{ old('service_period', $employee->service_period) }}" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="base_salary" class="form-label">{{ __('employee.table.header.base_salary') }}</label>
                            <input type="number" class="form-control" id="base_salary" name="base_salary" value="{{ old('base_salary', $employee->base_salary) }}">
                        </div>
                        <div class="mb-3">
                            <label for="employment_status_key" class="form-label">{{ __('employee.table.header.employment_status') }}*</label>
                            <select class="form-select" id="employment_status_key" name="employment_status_key" required>
                                <option value="working" @if(old('employment_status_key', $employee->employment_status_key)==='working') selected @endif>{{ __('employee.status.working_plain') }}</option>
                                <option value="resigning" @if(old('employment_status_key', $employee->employment_status_key)==='resigning') selected @endif>{{ __('employee.status.resigning_plain') }}</option>
                                <option value="resigned" @if(old('employment_status_key', $employee->employment_status_key)==='resigned') selected @endif>{{ __('employee.status.resigned_plain') }}</option>
                                <option value="on_leave" @if(old('employment_status_key', $employee->employment_status_key)==='on_leave') selected @endif>{{ __('employee.status.on_leave_plain') }}</option>
                            </select>
                        </div>
                        <div class="mb-3" id="employment-status-subtext-group">
                            <label for="employment_status_subtext" class="form-label">{{ __('employee.table.header.employment_status_subtext') }}</label>
                            <input type="text" class="form-control" id="employment_status_subtext" name="employment_status_subtext" value="{{ old('employment_status_subtext', $employee->employment_status_subtext) }}">
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('employees.index') }}" class="btn btn-label-secondary me-2">{{ __('app.cancel_button') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('app.update_button') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
