@extends('layouts.app')

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css"
        integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
        .select2-container {
            width: 100% !important
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">

                                <h3 class="card-title">Employee Type</h3>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModal">
                                    Add New Type
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Holidays</th>
                                        <th>Holiday Ratio</th>
                                        <th>Overtime</th>
                                        <th>Overtime Ratio</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Employee Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="employe-type-form" action="{{ route('employee-types.store') }}" method="POST"
                        data-method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="holidays" class="form-label">Holidays</label>
                            <select multiple class="holidays js-example-basic-single" id="holidays" name="holidays[]"
                                required>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="No Holiday">No Holiday</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="holiday_ratio" class="form-label">Holiday Ratio</label>
                            <input type="number" class="form-control" id="holiday_ratio" name="holiday_ratio"
                                step="0.01" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overtime</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="overtime" id="overtime_yes"
                                    value="yes">
                                <label class="form-check-label" for="overtime_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="overtime" id="overtime_no"
                                    value="no">
                                <label class="form-check-label" for="overtime_no">No</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="overtime_ratio" class="form-label">Overtime Ratio</label>
                            <input type="number" class="form-control" id="overtime_ratio" name="overtime_ratio"
                                step="0.01" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adjust Hours</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adjust_hours" id="adjust_yes"
                                    value="yes" required>
                                <label class="form-check-label" for="adjust_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adjust_hours" id="adjust_no"
                                    value="no" required>
                                <label class="form-check-label" for="adjust_no">No</label>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary float-right">Create</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.holidays').select2({
                dropdownParent: $('#exampleModal'),
                multiple: true
            });
        });
    </script>

    <script type="text/javascript">
        function deleteRecord(id) {
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
                        url: '/employee-types/' + id,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'The data has been deleted.',
                                'success'
                            );
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the data.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            var dataTable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('employee-types.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'holidays',
                        name: 'holidays'
                    },
                    {
                        data: 'holiday_ratio',
                        name: 'holiday_ratio'
                    },
                    {
                        data: 'overtime',
                        name: 'overtime'
                    },
                    {
                        data: 'overtime_ratio',
                        name: 'overtime_ratio'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });
    </script>
@endsection
