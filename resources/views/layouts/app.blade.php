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
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ env('ASSET_URL') }}/assets/dist/img/AdminLTELogo.png"
                alt="AdminLTELogo" height="60" width="60">
        </div> --}}

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light hide-on-print d-print-none">

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
        <aside class="main-sidebar sidebar-dark-primary elevation-4 hide-on-print d-print-none">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="{{ env('ASSET_URL') }}/assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">ERP</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar d-print-none">
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
                            <a href="{{ url('/dashboard') }}"
                                class="nav-link {{ $currentRouteName == 'dashboard' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-circle"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>

                        @php
                            $purchaseItems = [
                                [
                                    'title' => 'Vendor',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'vendors.index',
                                    'children' => [['title' => 'View', 'route' => 'vendors.index']],
                                ],
                                [
                                    'title' => 'Product',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'products.index',
                                    'children' => [
                                        ['title' => 'Products', 'route' => 'products.index'],
                                        ['title' => 'Product Type', 'route' => 'product-types.index'],
                                        ['title' => 'Materials', 'route' => 'materials.index'],
                                        ['title' => 'Particulars', 'route' => 'particulars.index'],
                                    ],
                                ],
                                [
                                    'title' => 'Purchases',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'purchases.index',
                                    'children' => [
                                        ['title' => 'Purchase Order', 'route' => 'purchases.index'],
                                        ['title' => 'GRN', 'route' => 'purchase-receipts.index'],
                                        ['title' => 'Purchase Invoice', 'route' => 'purchase-invoice.index'],
                                        ['title' => 'Inventory', 'route' => 'inventory.index'],
                                        ['title' => 'ERP Departments', 'route' => 'erp-departments.index'],
                                        ['title' => 'Sub ERP Departments', 'route' => 'sub-erp-departments.index'],
                                    ],
                                ]
                            ];
                            $erpItems = [
                                [
                                    'title' => 'Branch',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'branches.index',
                                    'children' => [['title' => 'View', 'route' => 'branches.index']],
                                ],
                                 [
                                    'title' => 'Department',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'departments.index',
                                    'children' => [['title' => 'View', 'route' => 'departments.index']],
                                ],
                                [
                                    'title' => 'Customer',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'customers.index',
                                    'children' => [['title' => 'View', 'route' => 'customers.index']],
                                ],
                                
                                [
                                    'title' => 'Manufacturer',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'manufacturers.index',
                                    'children' => [['title' => 'View', 'route' => 'manufacturers.index']],
                                ],
                                [
                                    'title' => 'Machine',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'machines.index',
                                    'children' => [['title' => 'View', 'route' => 'machines.index']],
                                ],
                                
                                
                                [
                                    'title' => 'Inward',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'inward-receipts.index',
                                    'children' => [
                                        ['title' => 'Gate Inward', 'route' => 'inward-general.index'],
                                    ],
                                ],
                                [
                                    'title' => 'Productions',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'raw-materials.index',
                                    'children' => [
                                        ['title' => 'Raw Materials', 'route' => 'raw-materials.index'],
                                        ['title' => 'Sale Orders', 'route' => 'sale-orders.index'],
                                        ['title' => 'Master Design', 'route' => 'fabric-measurements.index'],
                                        ['title' => 'Color Code', 'route' => 'color-codes.index'],
                                        ['title' => 'Production Planning', 'route' => 'production-plannings.index'],
                                        ['title' => 'Daily Production', 'route' => 'daily-productions.index'],
                                        ['title' => 'Product Group', 'route' => 'product-groups.index'],
                                        ['title' => 'Than Issue', 'route' => 'than-issues.index'],
                                        ['title' => 'Than Supply', 'route' => 'than-supplies.index'],
                                        ['title' => 'Receive Supply', 'route' => 'supply-receipts.index'],
                                        ['title' => 'Batch', 'route' => 'batches.index'],
                                    ],
                                ],
                            ];

                            $hrItems = [
                                [
                                    'title' => 'Employee',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'employees.index',
                                    'children' => [['title' => 'View', 'route' => 'employees.index']],
                                ],
                                [
                                    'title' => 'Attendance',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'attendance.index',
                                    'children' => [
                                        ['title' => 'View', 'route' => 'attendance.index'],
                                        ['title' => 'Checkin/Checkout', 'route' => 'attendance.create'],
                                        ['title' => 'Time Correction', 'route' => 'attendance.correction'],
                                        ['title' => 'Miss Scan', 'route' => 'miss-scan.index'],
                                        ['title' => 'Fix Dept Attd', 'route' => 'fixed-dept-attendance'],
                                    ],
                                ],
                               
                                [
                                    'title' => 'Shifts',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'shifts.index',
                                    'children' => [['title' => 'View', 'route' => 'shifts.index']],
                                ],
                                [
                                    'title' => 'Shift Transfer',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'shift-transfers.index',
                                    'children' => [['title' => 'View', 'route' => 'shift-transfers.index']],
                                ],
                                [
                                    'title' => 'Employee Types',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'employee-types.index',
                                    'children' => [['title' => 'View', 'route' => 'employee-types.index']],
                                ],
                                [
                                    'title' => 'Loan',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'loans.index',
                                    'children' => [['title' => 'View', 'route' => 'loans.index']],
                                ],
                                [
                                    'title' => 'Loan Exceptions',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'loan-exception.index',
                                    'children' => [['title' => 'View', 'route' => 'loan-exception.index']],
                                ],
                                [
                                    'title' => 'Advance',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'advance-salaries.index',
                                    'children' => [['title' => 'View', 'route' => 'advance-salaries.index']],
                                ],
                                [
                                    'title' => 'Leaves',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'leaves.index',
                                    'children' => [['title' => 'View', 'route' => 'leaves.index']],
                                ],
                                [
                                    'title' => 'Generate Salary',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'generate-salary',
                                    'children' => [['title' => 'Create', 'route' => 'generate-salary']],
                                ],
                                [
                                    'title' => 'Gazette Holidays',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'gazette-holidays.index',
                                    'children' => [['title' => 'Create', 'route' => 'gazette-holidays.index']],
                                ],
                                [
                                    'title' => 'Salary Records',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'salaries.index',
                                    'children' => [['title' => 'View History', 'route' => 'salaries.index']],
                                ],
                            ];

                            $superItems = [
                                [
                                    'title' => 'Accounts',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'accounts.index',
                                    'children' => [
                                        ['title' => 'All Accounts', 'route' => 'accounts.index'],
                                        ['title' => 'Transfers', 'route' => 'accounts-transfers.index'],
                                        ['title' => 'Vendor Payables', 'route' => 'accounts-vendors-payables.index'],
                                        [
                                            'title' => 'Customer Receivables',
                                            'route' => 'accounts-customersreceivables.index',
                                        ],
                                    ],
                                ],
                                [
                                    'title' => 'Adjustments',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'stock-adjustments.index',
                                    'children' => [
                                        ['title' => 'Stock', 'route' => 'stock-adjustments.index'],
                                        ['title' => 'Account Adjustments', 'route' => 'account-adjustments.index'],
                                        ['title' => 'Vendor Adjustments', 'route' => 'vendor-adjustments.index'],
                                        ['title' => 'Customer Adjustments', 'route' => 'customer-adjustments.index'],
                                    ],
                                ],
                                [
                                    'title' => 'Roles',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'roles.index',
                                    'children' => [['title' => 'View', 'route' => 'roles.index']],
                                ],
                                [
                                    'title' => 'Users',
                                    'icon' => 'fas fa-circle',
                                    'route' => 'users.index',
                                    'children' => [['title' => 'View', 'route' => 'users.index']],
                                ],
                            ];
                        @endphp
                        @php
                            $authRoles = auth()->user()->roles->pluck('name')->toArray();
                            $navItems = [];

                            // Define parent menus
                            $purchaseParent = [
                                'title' => 'Purchase',
                                'icon' => 'fas fa-cogs',
                                'children' => $purchaseItems,
                            ];
                            
                            $erpParent = [
                                'title' => 'ERP',
                                'icon' => 'fas fa-cogs',
                                'children' => $erpItems,
                            ];

                            $hrParent = [
                                'title' => 'HR',
                                'icon' => 'fas fa-users',
                                'children' => $hrItems,
                            ];

                            $superParent = [
                                'title' => 'Super',
                                'icon' => 'fas fa-user-shield',
                                'children' => $superItems,
                            ];

                            // Role-based logic to determine which parent menus to include
                            if (in_array('hr', $authRoles) && in_array('erp', $authRoles) && in_array('purchase', $authRoles)) {
                                $navItems = [$erpParent, $hrParent, $superParent,$purchaseParent];
                            } elseif (in_array('erp', $authRoles)) {
                                $navItems = [$erpParent];
                            } elseif (in_array('hr', $authRoles)) {
                                $navItems = [$hrParent];
                            } elseif (in_array('purchase', $authRoles)) {
                                $navItems = [$purchaseParent];
                            }

                            // If the user has the "super" role, include the Super menu
                            if (in_array('super', $authRoles)) {
                                $navItems[] = $superParent;
                            }
                        @endphp
                        @foreach ($navItems as $parentItem)
                            @php
                                // Check if any child route matches the current route
                                $isParentActive = false;
                                foreach ($parentItem['children'] as $child) {
                                    if (Str::startsWith($currentRouteName, Str::beforeLast($child['route'], '.'))) {
                                        $isParentActive = true;
                                        break;
                                    }
                                    // Check nested children (if any)
                                    if (isset($child['children'])) {
                                        foreach ($child['children'] as $subChild) {
                                            if (
                                                Str::startsWith(
                                                    $currentRouteName,
                                                    Str::beforeLast($subChild['route'], '.'),
                                                )
                                            ) {
                                                $isParentActive = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <li class="nav-item {{ $isParentActive ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ $isParentActive ? 'active' : '' }}">
                                    <i class="nav-icon {{ $parentItem['icon'] }}"></i>
                                    <p>
                                        {{ $parentItem['title'] }}
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($parentItem['children'] as $child)
                                        @php
                                            // Check if the child route matches the current route
                                            $isChildActive = Str::startsWith(
                                                $currentRouteName,
                                                Str::beforeLast($child['route'], '.'),
                                            );
                                            // Check nested children (if any)
                                            if (isset($child['children'])) {
                                                foreach ($child['children'] as $subChild) {
                                                    if (
                                                        Str::startsWith(
                                                            $currentRouteName,
                                                            Str::beforeLast($subChild['route'], '.'),
                                                        )
                                                    ) {
                                                        $isChildActive = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <li class="nav-item">
                                            <a href="{{ route($child['route']) }}"
                                                class="nav-link {{ $isChildActive ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>{{ $child['title'] }}</p>
                                            </a>
                                            @if (isset($child['children']))
                                                <ul class="nav nav-treeview">
                                                    @foreach ($child['children'] as $subChild)
                                                        @php
                                                            $isSubChildActive = Str::startsWith(
                                                                $currentRouteName,
                                                                Str::beforeLast($subChild['route'], '.'),
                                                            );
                                                        @endphp
                                                        <li class="nav-item">
                                                            <a href="{{ route($subChild['route']) }}"
                                                                class="nav-link {{ $isSubChildActive ? 'active' : '' }}">
                                                                <i class="far fa-dot-circle nav-icon"></i>
                                                                <p>{{ $subChild['title'] }}</p>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    @if ($message = Session::get('success'))
        <script>
            toastr.success('{{ $message }}');
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            toastr.error('{{ $message }}');
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            toastr.warning('{{ $message }}');
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            toastr.info('{{ $message }}');
        </script>
    @endif

    {{-- Handle validation errors --}}
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
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
        const baseUrl = "{{ env('APP_URL') }}"

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
                            const message = xhr.responseJSON.error ? xhr.responseJSON.error :
                                'There was an error while deleting'
                            Swal.fire(
                                'Error!',
                                message,
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
