@extends('layouts.app')

@section('title', __('employee.management.import_employees_title'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('employee.management.import_employees_title') }}</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">{{ __('employee.management.select_excel_file') }}</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('employee_upload_template.csv') }}" class="btn btn-outline-secondary btn-sm" download>
                                <i class="bx bx-download me-1"></i>{{ __('employee.management.download_template_button') }}
                            </a>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('employees.index') }}" class="btn btn-label-secondary me-2">{{ __('app.cancel_button') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('employee.management.import_data_button') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
