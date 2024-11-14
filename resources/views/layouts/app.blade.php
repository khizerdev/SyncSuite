<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ERP | Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Tempusdominus Bootstrap 4 -->
    {{-- <link rel="stylesheet"
        href="{{ env('ASSET_URL') }}/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"> --}}
    <!-- iCheck -->
    {{-- <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css"> --}}
    <!-- JQVMap -->
    {{-- <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/jqvmap/jqvmap.min.css"> --}}
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    {{-- <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/daterangepicker/daterangepicker.css"> --}}
    <!-- summernote -->
    {{-- <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/summernote/summernote-bs4.min.css"> --}}

    <link rel="stylesheet" href="{{ env('ASSET_URL') }}/assets/plugins/toastr/toastr.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ env('ASSET_URL') }}/assets/dist/img/AdminLTELogo.png"
                alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-block btn-danger">Logout</button>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                        role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="{{ env('ASSET_URL') }}/assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">ERP</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ env('ASSET_URL') }}/assets/dist/img/user2-160x160.jpg"
                            class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu"
                        data-accordion="false">
                        
                        @php
                         $currentRouteName = Route::currentRouteName();
                        @endphp
                        <li class="nav-item">
                            <a href="{{ url('/dashboard') }}" class="nav-link {{ $currentRouteName == "dashboard" ? 'active' : '' }}">
                                <i class="nav-icon fas fa-circle"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>

                        @php
                        $erpItems = [
                            [
                                'title' => 'Branch',
                                'icon' => 'fas fa-circle',
                                'route' => 'branches.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'branches.index']
                                ]
                            ],
                            [
                                'title' => 'Customer',
                                'icon' => 'fas fa-circle',
                                'route' => 'customers.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'customers.index']
                                ]
                            ],
                            [
                                'title' => 'Vendor',
                                'icon' => 'fas fa-circle',
                                'route' => 'vendors.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'vendors.index']
                                ]
                            ],
                            [
                                'title' => 'Manufacturer',
                                'icon' => 'fas fa-circle',
                                'route' => 'manufacturers.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'manufacturers.index']
                                ]
                            ],
                            [
                                'title' => 'Machine',
                                'icon' => 'fas fa-circle',
                                'route' => 'machines.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'machines.index']
                                ]
                            ],
                            [
                                'title' => 'Product',
                                'icon' => 'fas fa-circle',
                                'route' => 'products.index',
                                'children' => [
                                    ['title' => 'Products', 'route' => 'products.index'],
                                    ['title' => 'Product Type', 'route' => 'product-types.index'],
                                    ['title' => 'Materials', 'route' => 'materials.index'],
                                    ['title' => 'Particulars', 'route' => 'particulars.index']
                                ]
                            ],
                            [
                                'title' => 'Purchases',
                                'icon' => 'fas fa-circle',
                                'route' => 'purchases.index',
                                'children' => [
                                    ['title' => 'Purchase Order', 'route' => 'purchases.index'],
                                    ['title' => 'Purchase Receipts', 'route' => 'purchase-receipts.index'],
                                    ['title' => 'Purchase Invoice', 'route' => 'purchase-invoice.index'],
                                ]
                            ],
                            [
                                'title' => 'Inward',
                                'icon' => 'fas fa-circle',
                                'route' => 'inward-receipts.index',
                                'children' => [
                                    ['title' => 'Inward Receipt', 'route' => 'inward-receipts.index'],
                                    ['title' => 'Inward General', 'route' => 'inward-general.index']
                                ]
                            ]
                        ];

                        $hrItems = [
                            [
                                'title' => 'Employee',
                                'icon' => 'fas fa-circle',
                                'route' => 'employees.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'employees.index']
                                ]
                            ],
                            [
                                'title' => 'Attendance',
                                'icon' => 'fas fa-circle',
                                'route' => 'attendance.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'attendance.index']
                                ]
                            ],
                            [
                                'title' => 'Department',
                                'icon' => 'fas fa-circle',
                                'route' => 'departments.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'departments.index']
                                ]
                            ],
                            [
                                'title' => 'Shifts',
                                'icon' => 'fas fa-circle',
                                'route' => 'shifts.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'shifts.index']
                                ]
                            ],
                            [
                                'title' => 'Employee Types',
                                'icon' => 'fas fa-circle',
                                'route' => 'employee-types.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'employee-types.index']
                                ]
                            ],
                            [
                                'title' => 'Loan',
                                'icon' => 'fas fa-circle',
                                'route' => 'loans.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'loans.index']
                                ]
                            ],
                            [
                                'title' => 'Advance',
                                'icon' => 'fas fa-circle',
                                'route' => 'advance-salaries.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'advance-salaries.index']
                                ]
                            ],
                            [
                                'title' => 'Leaves',
                                'icon' => 'fas fa-circle',
                                'route' => 'leaves.index',
                                'children' => [
                                    ['title' => 'View', 'route' => 'leaves.index']
                                ]
                            ],
                        ]

                        @endphp
                        @php
                        $currentMode = session()->get('currentMode') ?? 'erp';
                        $navItems = [];

                        if($currentMode == 'erp'){
                            //if(Auth::check() && Auth::user()->email === 'test@example.com'){
                                $navItems = $erpItems;
                            //}
                        } else {
                            //if(Auth::check() && Auth::user()->email === 'hr@gmail.com'){
                                $navItems = $hrItems;
                            //}
                        }
                        @endphp

                        @foreach ($navItems as $item)
                        @php
                            $isActive = false;
                            foreach ($item['children'] as $child) {
                                if (Str::startsWith($currentRouteName, Str::beforeLast($child['route'], '.'))) {
                                    $isActive = true;
                                    break;
                                }
                            }
                            @endphp
                            <li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
                                <a href="{{ route($item['route']) }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                                    <i class="nav-icon {{ $item['icon'] }}"></i>
                                    <p>
                                        {{ $item['title'] }}
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($item['children'] as $child)
                                        @php
                                        $isChildActive = Str::startsWith($currentRouteName, Str::beforeLast($child['route'], '.'));
                                        @endphp
                                        <li class="nav-item">
                                            <a href="{{ route($child['route']) }}" class="nav-link {{ $isChildActive ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>{{ $child['title'] }}</p>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach


                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            @yield('content')

        </div>
        <!-- /.content-wrapper -->
        
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/chart.js/Chart.min.js"></script> --}}
    <!-- Sparkline -->
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/sparklines/sparkline.js"></script> --}}
    <!-- JQVMap -->
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/jqvmap/jquery.vmap.min.js"></script> --}}
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script> --}}
    <!-- jQuery Knob Chart -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/moment/moment.min.js"></script>
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/daterangepicker/daterangepicker.js"></script> --}}
    <!-- Tempusdominus Bootstrap 4 -->
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"> --}}
    </script>
    <!-- Summernote -->
    {{-- <script src="{{ env('ASSET_URL') }}/assets/plugins/summernote/summernote-bs4.min.js"></script> --}}
    <!-- overlayScrollbars -->
    <script src="{{ env('ASSET_URL') }}/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ env('ASSET_URL') }}/assets/dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ env('ASSET_URL') }}/assets/dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ env('ASSET_URL') }}/assets/dist/js/pages/dashboard.js"></script>

    <script src="{{ env('ASSET_URL') }}/assets/plugins/toastr/toastr.min.js"></script>

    <!-- Datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if ($message = Session::get('success'))
              <script>
                toastr.success(' {{$message}} ');
              </script>
            @endif

            @if ($message = Session::get('error'))
                <script>
                toastr.error(' {{$message}} ');
              </script>
            @endif

            @if ($message = Session::get('warning'))
              <script>
                toastr.warning(' {{$message}} ');
              </script>
            @endif
            
    
            @if ($message = Session::get('info'))
              <script>
                toastr.info(' {{$message}} ');
              </script>
              @endif

    <script>
        $(document).ready(function() {
            $('#form').on('submit', function(event) {
                event.preventDefault();

                const formData = $(this).serialize();
                var url = $(this).attr('action');
                const method = $(this).data('method');
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.message)
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Validation error:\n';
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessage += `${key}: ${errors[key].join(', ')}\n`;
                                }
                            }
                            toastr.error(errorMessage)

                        } else {
                            console.error('Error creating branch:', xhr.responseText);
                            toastr.error('Error creating branch. Please try again.')

                        }
                    }
                });
            });
        });
    </script>

    <script>
        const baseUrl = "{{env('APP_URL')}}"
        function deleteData(id, url, type) {
            console.log(baseUrl + url + id)
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
              url: baseUrl + url + id,
              type: type,
              data: {
                  "_token": "{{ csrf_token() }}",
              },
              success: function(response) {
                  Swal.fire(
                      'Deleted!',
                      'The record has been deleted.',
                      'success'
                  );
                  $('#table').DataTable().ajax.reload();
              },
              error: function(xhr) {
                  Swal.fire(
                      'Error!',
                      'There was an error dwhile deleting',
                      'error'
                  );
              }
          })
                }
            });
        }
    </script>


    @yield('script')

</body>

</html>
