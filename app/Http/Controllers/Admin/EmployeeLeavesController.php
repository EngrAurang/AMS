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

class EmployeeLeavesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('employee_leaf_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = EmployeeLeaf::with(['employee'])->select(sprintf('%s.*', (new EmployeeLeaf)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'employee_leaf_show';
                $editGate      = 'employee_leaf_edit';
                $deleteGate    = 'employee_leaf_delete';
                $crudRoutePart = 'employee-leaves';

                return view('partials.datatablesActions', compact(
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
        abort_if(Gate::denies('employee_leaf_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employeeLeaves.create', compact('employees'));
    }

    public function store(StoreEmployeeLeafRequest $request)
    {
        $employeeLeaf = EmployeeLeaf::create($request->all());

        return redirect()->route('admin.employee-leaves.index');
    }

    public function edit(EmployeeLeaf $employeeLeaf)
    {
        abort_if(Gate::denies('employee_leaf_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $employeeLeaf->load('employee');

        return view('admin.employeeLeaves.edit', compact('employeeLeaf', 'employees'));
    }

    public function update(UpdateEmployeeLeafRequest $request, EmployeeLeaf $employeeLeaf)
    {
        $employeeLeaf->update($request->all());

        return redirect()->route('admin.employee-leaves.index');
    }

    public function show(EmployeeLeaf $employeeLeaf)
    {
        abort_if(Gate::denies('employee_leaf_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeLeaf->load('employee');

        return view('admin.employeeLeaves.show', compact('employeeLeaf'));
    }

    public function destroy(EmployeeLeaf $employeeLeaf)
    {
        abort_if(Gate::denies('employee_leaf_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employeeLeaf->delete();

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
}
