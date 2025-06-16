<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('templates/assets/') }}/"
  data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/assets/img/favicon/favicon.ico') }}" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('templates/assets/vendor/fonts/boxicons.css') }}" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('templates/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/css/demo.css') }}" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    @yield('page-style')
    <!-- Page CSS -->
    @yield('page-css')
    <!-- Helpers -->
    <script src="{{ asset('templates/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('templates/assets/js/config.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="container-xxl">
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
              <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  <!-- You can put your SVG logo here -->
                </span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
              </a>
            </div>
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>
            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <div class="navbar-nav align-items-center me-3">
                <div class="nav-item d-flex align-items-center">
                  <a href="{{ route('lang.swap', 'en') }}" class="btn btn-outline-secondary btn-sm me-2 {{ session('locale') === 'en' ? 'active' : '' }}">EN</a>
                  <a href="{{ route('lang.swap', 'kr') }}" class="btn btn-outline-secondary btn-sm {{ session('locale') === 'kr' ? 'active' : '' }}">KR</a>
                </div>
              </div>
              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <span class="avatar-initial rounded-circle bg-label-secondary">{{ substr(Auth::user()->name, 0, 2) }}</span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <span class="avatar-initial rounded-circle bg-label-secondary">{{ substr(Auth::user()->name, 0, 2) }}</span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                      <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">{{ __('My Profile') }}</span>
                      </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                      <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                          <i class="bx bx-power-off me-2"></i>
                          <span class="align-middle">{{ __('Log Out') }}</span>
                        </a>
                      </form>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- / Navbar -->
        <!-- Layout page -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Menu -->
                <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                <div class="container-xxl d-flex h-100">
                    <ul class="menu-inner py-1">
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Dashboard">{{ __('Dashboard') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Employees">{{ __('Employees') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                        <a href="{{ route('payrolls.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-money"></i>
                        <div data-i18n="Payrolls">{{ __('Payrolls') }}</div>
                        </a>
                    </li>
                    </ul>
                </div>
            </aside>
            <!-- / Menu -->
            <!-- / Menu -->
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!--/ Content -->
          </div>
        </div>
        <!--/ Layout page -->
      </div>
    </div>
    <!-- / Layout wrapper -->
    <!-- Core JS -->
    <script src="{{ asset('templates/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('templates/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('templates/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('templates/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('templates/assets/vendor/js/menu.js') }}"></script>
    <!-- SweetAlert2 for CRUD notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Vendors JS -->
    @yield('vendor-script')
    <!-- Main JS -->
    <script src="{{ asset('templates/assets/js/main.js') }}"></script>
    <!-- Page JS -->
    @yield('page-script')
</body>
</html>
