@extends('layouts.app')

@section('content')
    <style>
        .iframe-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .iframe-responsive {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
            /* 16:9 Aspect Ratio */
        }

        .iframe-responsive iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @php
                $currentMode = session()->get('currentMode') ?? 'erp';
            @endphp
            @if ($currentMode == 'erp')
                <div class="row" id="widgets">
                </div>
            @endif

            <div class="row mb-3">
                @php
                    // $modes = [
                    //     ['slug' => 'erp', 'title' => 'ERP', 'bgClass' => 'bg-black', 'textClass' => 'text-white', 'iconFill' => 'none', 'iconStroke' => '#ffffff'],
                    //     ['slug' => 'hr', 'title' => 'HR', 'bgClass' => 'bg-black', 'textClass' => 'text-white', 'iconFill' => 'none', 'iconStroke' => '#ffffff'],
                    // ];
                    $modes = [];
                @endphp
                @foreach ($modes as $mode)
                    <a class="col-3 cursor-pointer" href="{{ route('switch', $mode['slug']) }}">
                        <div
                            class="card h-100 {{ $currentMode == $mode['slug'] ? $mode['bgClass'] . ' text-white' : 'bg-light' }} ">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="mb-3" style="width: 2rem; height: 2rem;">
                                    @if ($mode['slug'] == 'erp')
                                        <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        <rect width="20" height="14" x="2" y="6" rx="2"></rect>
                                    @elseif($mode['slug'] == 'hr')
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    @endif
                                </svg>
                                <h2 class="card-title {{ $currentMode == $mode['slug'] ? $mode['textClass'] : '' }}">
                                    {{ $mode['title'] }}</h2>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="excel_file" accept=".sql">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
                {{-- <div class="col-12">
                    <a href="{{ route('calculate.hours', ['employeeId' => '1001']) }}" class="btn btn-primary">Calculate Working Hours</a>
                </div> --}}
                <div class="col-12">
                    <button id="createAttendance" class="btn btn-primary">
                        <span id="loader" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        Create Attendance Records
                    </button>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="iframe-container">
                        <!--<h2>Embedded Content</h2>-->
                        <div class="iframe-responsive">
                            <!-- Replace the src with your target URL -->
                            <iframe src="https://ahmedfabrics.com.pk/db_converter/" title="Example Embedded Content"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#createAttendance').on('click', function() {
                // Disable the button and show the loader
                $(this).prop('disabled', true);
                $('#loader').removeClass('d-none');

                $.ajax({
                    url: "{{ route('check.last.record') }}",
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr.success("Records update successfully")
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message)
                    },
                    complete: function() {
                        // Re-enable the button and hide the loader
                        $('#createAttendance').prop('disabled', false);
                        $('#loader').addClass('d-none');
                    },
                });
            });
        });
    </script>
@endsection
