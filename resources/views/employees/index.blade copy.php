@extends('layouts/app')

@section('title', __('employee.page_title_list')) {{-- Changed --}}

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'); }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/typeahead-js/typeahead.css'); }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css'); }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css'); }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/flatpickr/flatpickr.css'); }}" />
@endsection


@section('vendor-script')
  <script src=" {{ asset('templates/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
  <!-- Flat Picker -->
  <script src=" {{ asset('templates/assets/vendor/libs/moment/moment.js') }}"></script>
  <script src=" {{ asset('templates/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
  <script src=" {{ asset('templates/assets/js/tables-datatables-advanced.js') }}"></script>
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('employee.title') }} </span> / {{ __('employee.breadcrumb_list') }}
      </h4>

      <!-- Column Search -->
      <div class="card">
        <h5 class="card-header">{{ __('employee.card_header_column_search') }}</h5>
        <div class="card-datatable text-nowrap">
          <table class="dt-column-search table table-bordered">
            <thead>
              <tr>
                <th>{{ __('employee.table_no') }}</th>
                <th>{{ __('employee.table_employee_id') }}</th>
                <th>{{ __('employee.table_work_location') }}</th>
                <th>{{ __('employee.table_position') }}</th>
                <th>{{ __('employee.table_name') }}</th>
                <th>{{ __('employee.table_age') }}</th>
                <th>{{ __('employee.table_date_of_joining') }}</th>
                <th>{{ __('employee.table_contract_number') }}</th>
                <th>{{ __('employee.table_base_salary') }}</th>
                <th>{{ __('employee.table_employment_status') }}</th>
                <th>{{ __('employee.table_actions') }}</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>{{ __('employee.table_no') }}</th>
                <th>{{ __('employee.table_employee_id') }}</th>
                <th>{{ __('employee.table_work_location') }}</th>
                <th>{{ __('employee.table_position') }}</th>
                <th>{{ __('employee.table_name') }}</th>
                <th>{{ __('employee.table_age') }}</th>
                <th>{{ __('employee.table_date_of_joining') }}</th>
                <th>{{ __('employee.table_contract_number') }}</th>
                <th>{{ __('employee.table_base_salary') }}</th>
                <th>{{ __('employee.table_employment_status') }}</th>
                <th>{{ __('employee.table_actions') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <!--/ Column Search -->
    </div>
    <!--/ Content -->
@endsection