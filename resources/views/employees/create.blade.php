@extends('layouts.app')
@section('title', __('employee.create_title'))
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">{{ __('employee.create_title') }}</h4>
    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('employee.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('employee.email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('employee.position') }}</label>
            <input type="text" name="position" class="form-control" value="{{ old('position') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('employee.department') }}</label>
            <input type="text" name="department" class="form-control" value="{{ old('department') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('employee.phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('employee.address') }}</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
        </div>
        <button type="submit" class="btn btn-primary">{{ __('employee.save') }}</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employee.cancel') }}</a>
    </form>
</div>
@endsection
