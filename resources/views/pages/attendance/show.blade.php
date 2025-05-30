@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @foreach ($collectiveAttendances as $employeeId => $attendance)
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="col-12">
                                    <h2 class="mb-4">Attendance for Employee: {{ $attendance['employee']->name ?? 'N/A' }}
                                        {{ $attendance['employee']->code ?? '' }}</h2>
                                </div>
                                <div class="col-12">
                                    @if (isset($attendance['groupedAttendances']) && isset($attendance['employee']))
                                        <x-attendance-table :grouped-attendances="$attendance['groupedAttendances'] ?? []" :employee="$attendance['employee'] ?? null" :holidays="$attendance['holidays'] ?? []"
                                            :gazatte-dates="$attendance['gazatteDates'] ?? []" :late-minutes="$attendance['lateMinutes'] ?? 0" :early-out-minutes="$attendance['earlyCheckoutMinutes'] ?? 0" :early-minutes="$attendance['earlyCheckinMinutes'] ?? 0"
                                            :over-minutes="$attendance['overMinutes'] ?? 0" />
                                    @else
                                        <p class="text-danger">No attendance data available for this employee.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
@endsection
