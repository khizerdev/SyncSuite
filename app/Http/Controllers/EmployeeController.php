<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Attachment;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-employee|create-employee|edit-employee|delete-employee' => ['only' => ['index', 'store']],
             'permission:create-employee' => ['only' => ['create', 'store']],
             'permission:edit-employee' => ['only' => ['edit', 'update']],
             'permission:delete-employee' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Employee::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('employees.edit', $row->id);
                    $attdUrl = route('employees.attd', $row->id);
                    $deleteUrl = route('employees.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/employees/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2">Delete</button>';
                    $btn .= '<a href="'.$attdUrl.'" class="btn btn-warning btn-sm">View Attd</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.employees.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $employee = Employee::create($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }

            
            
            // Handle Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Employee created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = Employee::with('attachments')->findOrFail($id);
        return view('pages.employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validatedData = $request->validated();

            $employee->update($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                // Delete the old profile picture if exists
                $oldProfilePicture = $employee->attachments()->where('file_type', 'like', 'image%')->first();
                if ($oldProfilePicture) {
                    Storage::delete($oldProfilePicture->file_path);
                    $oldProfilePicture->delete();
                }

                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                // Delete the old resume if exists
                $oldResume = $employee->attachments()->where('file_type', 'application/pdf')->first();
                if ($oldResume) {
                    Storage::delete($oldResume->file_path);
                    $oldResume->delete();
                }

                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }


            // Handle Documents
            if ($request->hasFile('documents')) {
                // Delete the old documents if exists
                $oldDocuments = $employee->attachments()->where('file_type', '!=', 'application/pdf')->where('file_type', '!=', 'image%')->get();
                foreach ($oldDocuments as $oldDocument) {
                    Storage::delete($oldDocument->file_path);
                    $oldDocument->delete();
                }

                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }


            return response()->json([
                'message' => 'Employee updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
    
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete employee', 'error' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);
        $pathToFile = storage_path('app/' . $attachment->file_path);

        return response()->download($pathToFile, $attachment->file_name);
    }

   
    public function attd($employeeId)
{
    $employee = Employee::findOrFail($employeeId);
    $shift = $employee->timings;

    $attendances = Attendance::where('code', $employee->code)
        ->orderBy('datetime')
        ->get();

    $dailyMinutes = [];
    $groupedAttendances = [];

    $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));

    for ($i = 0; $i < count($attendances); $i++) {
        $checkIn = Carbon::parse($attendances[$i]->datetime);
        $date = $checkIn->format('Y-m-d');

        // Find the next check-out or check-in
        $nextEntry = null;
        for ($j = $i + 1; $j < count($attendances); $j++) {
            $nextEntry = Carbon::parse($attendances[$j]->datetime);  
            if (abs($nextEntry->diffInHours($checkIn)) <= 16) {
                break;
            }
            $nextEntry = null;
        }
        $shiftStart = Carbon::parse($shift->start_time);
        $shiftEnd = Carbon::parse($shift->end_time);
        if ($isNightShift) {
            $shiftEnd->addDay();
        }
        $maxCheckOut = $shiftEnd->copy()->addHours(4);

        if ($nextEntry && $nextEntry <= $maxCheckOut) {
            $checkOut = $nextEntry;
            $i = $j;
        } else {
            $checkOut = null;
        }

        if ($isNightShift) {
            $calculationCheckIn = $checkIn->copy()->addHours(6);
            $calculationCheckOut = $checkOut ? $checkOut->copy()->addHours(6) : null;
        } else {
            $calculationCheckIn = $checkIn;
            $calculationCheckOut = $checkOut;
        }

        $groupedAttendances[$date][] = [
            'original_checkin' => $checkIn,
            'original_checkout' => $checkOut,
            'calculation_checkin' => $calculationCheckIn,
            'calculation_checkout' => $calculationCheckOut,
            'is_incomplete' => !$checkOut
        ];
    }

    foreach ($groupedAttendances as $date => $entries) {
        $shiftStartTime = Carbon::parse($shift->start_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');
        $shiftEndTime = Carbon::parse($shift->end_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');

        $shiftStart = Carbon::parse($date . ' ' . $shiftStartTime);
        $shiftEnd = Carbon::parse($date . ' ' . $shiftEndTime);

        if ($isNightShift) {
            $shiftEnd->addDay();
        }

        $totalMinutes = 0;

        foreach ($entries as $entry) {
            if (!$entry['is_incomplete']) {
                
                $entryhour = $entryTimeStart->format('H:i:s');

                if (Carbon::parse($entryhour)->lessThan($shiftStartTime)) {
                    $entryTimeStart = $entryTimeStart->copy()->setTime(Carbon::parse($shiftStartTime)->hour, Carbon::parse($shiftStartTime)->minute, Carbon::parse($shiftStartTime)->second);
                }
                
                $entryTimeEnd = $entry['calculation_checkout'];

                $startTime = $entryTimeStart->max($shiftStart);
                $endTime = $entryTimeEnd->min($shiftEnd);

                // Calculate the overlap in minutes only if the times are valid within the shift
                if ($startTime->lt($endTime)) {
                    $totalMinutes += $startTime->diffInMinutes($endTime);
                }
            }
        }

        $dailyMinutes[$date] = $totalMinutes;
    }
        
    return view('pages.employees.attendance', compact('groupedAttendances', 'dailyMinutes', 'employee', 'shift', 'isNightShift'));
}
    
}