@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Daily Production</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('daily-productions.update', $dailyProduction->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Shift:</strong>
                                            <select name="shift_id" class="form-control" required>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}"
                                                        {{ $dailyProduction->shift_id == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->name }} ({{ $shift->start_time }} -
                                                        {{ $shift->end_time }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date"
                                                value="{{ \Carbon\Carbon::parse($dailyProduction->date)->format('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Machine:</strong>
                                            <select name="machine_id" class="form-control" required>
                                                @foreach ($machines as $machine)
                                                    <option value="{{ $machine->id }}"
                                                        {{ $dailyProduction->machine_id == $machine->id ? 'selected' : '' }}>
                                                        {{ $machine->name }} ({{ $machine->model }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Previous Stitch:</strong>
                                            <input type="number" name="previous_stitch" id="previous_stitch"
                                                value="{{ $dailyProduction->previous_stitch }}" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Current Stitch:</strong>
                                            <input type="number" name="current_stitch" id="current_stitch"
                                                value="{{ $dailyProduction->current_stitch }}" class="form-control"
                                                placeholder="Current Stitch" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Actual Stitch:</strong>
                                            <input type="number" name="actual_stitch" id="actual_stitch"
                                                value="{{ $dailyProduction->actual_stitch }}" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Description:</strong>
                                            <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ $dailyProduction->description }}</textarea>
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
