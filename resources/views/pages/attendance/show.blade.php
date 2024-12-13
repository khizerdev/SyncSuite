@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @foreach ($collectiveAttendances as $employeeId => $attendance)
                        @if ($attendance)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="col-12">
                                        <h2 class="mb-4">Attendance for Employee: {{ $attendance['employee']->name }}
                                            {{ $attendance['employee']->code }}</h2>
                                    </div>
                                    <x-attendance-table :grouped-attendances="$attendance['groupedAttendances']" :employee="$attendance['employee']" :holidays="$attendance['holidays']" />
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
@endsection
