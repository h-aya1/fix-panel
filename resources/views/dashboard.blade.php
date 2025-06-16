@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h3 class="header-main-title">{{ __('Dashboard') }}</h3>
            <p class="header-sub-title">{{ __('Welcome back,') }} {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">{{ __('Employees') }}</span>
                    <h3 class="card-title mb-2">-</h3>
                    <small class="text-success fw-semibold">{{ __('Total employees in system') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-money"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">{{ __('Payrolls') }}</span>
                    <h3 class="card-title mb-2">-</h3>
                    <small class="text-info fw-semibold">{{ __('Monthly payroll records') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-stats"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">{{ __('Active Users') }}</span>
                    <h3 class="card-title mb-2">{{ \App\Models\User::count() }}</h3>
                    <small class="text-muted fw-semibold">{{ __('System users') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 mb-3">
                            <a href="{{ route('employees.index') }}" class="btn btn-primary w-100">
                                <i class="bx bx-user me-2"></i>
                                {{ __('Manage Employees') }}
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 mb-3">
                            <a href="{{ route('payrolls.index') }}" class="btn btn-success w-100">
                                <i class="bx bx-money me-2"></i>
                                {{ __('Manage Payrolls') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('System Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">{{ __('Application Name') }}</td>
                                    <td>{{ config('app.name') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Environment') }}</td>
                                    <td>
                                        <span class="badge bg-{{ config('app.env') === 'production' ? 'success' : 'warning' }}">
                                            {{ strtoupper(config('app.env')) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('Laravel Version') }}</td>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">{{ __('PHP Version') }}</td>
                                    <td>{{ PHP_VERSION }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
