<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEmployeeLeafRequest;
use App\Http\Requests\StoreEmployeeLeafRequest;
use App\Http\Requests\UpdateEmployeeLeafRequest;
use App\Models\EmployeeLeaf;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationEmail;
use App\Jobs\SendAdminNotificationEmail;

class EmployeeLeavesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('employee_leave_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $loggedInUserRole = Auth::user()->roles->first();
            $role = $loggedInUserRole->title;
            if($role == "Admin"){
                $query = EmployeeLeaf::with(['employee'])->select(sprintf('%s.*', (new EmployeeLeaf)->table));
            }elseif($role == "Employee"){
                $userId = Auth::id();

                $query = EmployeeLeaf::with(['employee'])
                    ->where('employee_id', $userId)
                    ->select(sprintf('%s.*', (new EmployeeLeaf)->table));
            }elseif($role == "Line Manager"){
                $userId = Auth::id();

                // $query = EmployeeLeaf::with(['employee'])
                //     ->where('employee_id', $userId)
                //     ->select(sprintf('%s.*', (new EmployeeLeaf)->table));
                $query = EmployeeLeaf::with(['employee'])
                    ->whereHas('employee', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->orWhere('employee_id', $userId)
                    ->select(sprintf('%s.*', (new EmployeeLeaf)->table));

            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'employee_leave_show';
                $editGate      = 'employee_leave_edit';
                $deleteGate    = 'employee_leave_delete';
                $crudRoutePart = 'employee-leaves';

                return view('partials.datatableActionEmpLeaves', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('employee_name', function ($row) {
                return $row->employee ? $row->employee->name : '';
            });

            $table->editColumn('hr_approval', function ($row) {
                return $row->hr_approval ? EmployeeLeaf::HR_APPROVAL_RADIO[$row->hr_approval] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'employee']);

            return $table->make(true);
        }

        return view('admin.employeeLeaves.index');
    }

    public function create()
    {
        abort_if(Gate::denies('employee_leave_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employeeLeaves.create', compact('employees'));
    }

    public function store(StoreEmployeeLeafRequest $request)
    {
        $employeeLeaf = EmployeeLeaf::create($request->all());
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDateTime = DateTime::createFromFormat('d/m/Y', $startDate);
        $endDateTime = DateTime::createFromFormat('d/m/Y', $endDate);
        $interval = $startDateTime->diff($endDateTime);
        $numberOfDays = $interval->days + 1;
        $employeeName = User::find($request->employee_id);
        $name = $employeeName->name;
        $lineManagerId = $employeeName->user_id;
        $lineManagerEmail = User::where('id',$lineManagerId)->select('email')->first();

        if($lineManagerEmail){

            $recipients = ['sajid@yopmail.com', $lineManagerEmail->email];

            foreach ($recipients as $recipient) {
                dispatch(new SendAdminNotificationEmail($name, $startDate, $endDate, $numberOfDays, $recipient));
            }
        }else{
            $recipients = ['sajid@yopmail.com'];

            foreach ($recipients as $recipient) {
                dispatch(new SendAdminNotificationEmail($name, $startDate, $endDate, $numberOfDays, $recipient));
            }
        }

        return redirect()->route('admin.employee-leaves.index');
    }

    public function edit(EmployeeLeaf $employeeLeaf,$id)
    {

        abort_if(Gate::denies('employee_leave_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $employees = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $employeedata = EmployeeLeaf::with('employee')->where('id',$id)->first();
        // $employeeLeaf->load('employee');
        // dd($employeedata);
        return view('admin.employeeLeaves.edit', compact('employeedata', 'employees'));
    }

    public function update(UpdateEmployeeLeafRequest $request, EmployeeLeaf $employeeLeaf)
    {
        if($request->line_manager_approval == 'Approved' || $request->hr_approval == 'Approved'){
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $employeeId = EmployeeLeaf::where('id',$request->id)->select('employee_id')->first();
            $employee = User::find($employeeId->employee_id);
            $takenLeaves = $employee->leaves_taken;

            $startDateTime = DateTime::createFromFormat('d/m/Y', $startDate);
            $endDateTime = DateTime::createFromFormat('d/m/Y', $endDate);
            $interval = $startDateTime->diff($endDateTime);
            $numberOfDays = $interval->days + 1;
            $totalLeavesTaken = $takenLeaves + $numberOfDays;
            // dd($totalLeavesTaken);
            if ($employee) {
                $employee->leaves_taken = $totalLeavesTaken;
                $employee->save();
            }
        }
        // $employeeLeaf->update($request->all());
        $updateemployeeleave = EmployeeLeaf::find($request->id);
        $updateemployeeleave->update($request->all());
        return redirect()->route('admin.employee-leaves.index');
    }

    public function show(EmployeeLeaf $employeeLeaf,$id)
    {
        abort_if(Gate::denies('employee_leave_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $employeedata = EmployeeLeaf::with('employee')->where('id',$id)->first();
        // $employeeLeaf->load('employee');

        return view('admin.employeeLeaves.show', compact('employeedata'));
    }

    public function destroy(EmployeeLeaf $employeeLeaf,$id)
    {
        abort_if(Gate::denies('employee_leave_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeId = EmployeeLeaf::where('id',$id)->select('employee_id','start_date','end_date','hr_approval','line_manager_approval')->first();
        if($employeeId->line_manager_approval == 'Approved' || $employeeId->hr_approval == 'Approved'){
            $employee = User::find($employeeId->employee_id);
            $takenLeaves = $employee->leaves_taken;

            $startDate = $employeeId->start_date;
            $endDate = $employeeId->end_date;
            $startDateTime = DateTime::createFromFormat('d/m/Y', $startDate);
            $endDateTime = DateTime::createFromFormat('d/m/Y', $endDate);
            $interval = $startDateTime->diff($endDateTime);
            $numberOfDays = $interval->days + 1;
            $totalLeavesTaken = $takenLeaves - $numberOfDays;
            // dd($totalLeavesTaken);
            if ($employee) {
                $employee->leaves_taken = $totalLeavesTaken;
                $employee->save();
            }
        }

        $deleteEmployeeLeave = EmployeeLeaf::find($id);
        $deleteEmployeeLeave->delete();

        return back();
    }

    public function massDestroy(MassDestroyEmployeeLeafRequest $request)
    {
        $employeeLeaves = EmployeeLeaf::find(request('ids'));

        foreach ($employeeLeaves as $employeeLeaf) {
            $employeeLeaf->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function checkLeaves(Request $request)
    {
        $empId = $request->input('empId');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $leaves = User::find($empId);
        $totalLeave = $leaves->total_leaves;
        $takenLeave = $leaves->leaves_taken;
        $difference = $totalLeave - $takenLeave;

        $startDateTime = DateTime::createFromFormat('d/m/Y', $startDate);
        $endDateTime = DateTime::createFromFormat('d/m/Y', $endDate);
        $interval = $startDateTime->diff($endDateTime);
        $numberOfDays = $interval->days + 1;
        // Check if the number of days is less than or equal to the total leaves
        if ($numberOfDays <= $difference) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'The number of days exceeds the available leaves.'];
        }

        return response()->json($response);
    }
}