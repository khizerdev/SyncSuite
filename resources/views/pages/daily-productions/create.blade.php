@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            < <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Daily Production</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('daily-productions.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Shift:</strong>
                                            <select name="shift_id" class="form-control" required>
                                                <option value="">Select Shift</option>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}">{{ $shift->name }}
                                                        ({{ $shift->start_time }} - {{ $shift->end_time }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Machine:</strong>
                                            <select name="machine_id" class="form-control" required>
                                                <option value="">Select Machine</option>
                                                @foreach ($machines as $machine)
                                                    <option value="{{ $machine->id }}">{{ $machine->name }}
                                                        ({{ $machine->model }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Previous Stitch:</strong>
                                            <input type="number" id="previous_stitch" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Current Stitch:</strong>
                                            <input type="number" name="current_stitch" id="current_stitch"
                                                class="form-control" placeholder="Current Stitch" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Actual Stitch:</strong>
                                            <input type="number" id="actual_stitch" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Description:</strong>
                                            <textarea class="form-control" style="height:150px" name="description" placeholder="Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
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
            // When machine is selected, fetch the previous stitch
            $('select[name="machine_id"]').change(function() {
                var machineId = $(this).val();
                if (machineId) {
                    $.get('/api/get-previous-stitch/' + machineId, function(data) {
                        $('#previous_stitch').val(data.previous_stitch || 0);
                        calculateActualStitch();
                    });
                } else {
                    $('#previous_stitch').val(0);
                    calculateActualStitch();
                }
            });

            // Calculate actual stitch when current stitch changes
            $('#current_stitch').on('input', function() {
                calculateActualStitch();
            });

            function calculateActualStitch() {
                var previous = parseInt($('#previous_stitch').val()) || 0;
                var current = parseInt($('#current_stitch').val()) || 0;
                var actual = previous - current;
                $('#actual_stitch').val(actual);
            }
        });
    </script>
@endsection
