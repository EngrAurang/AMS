@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.employeeLeaf.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.employee-leaves.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.id') }}
                        </th>
                        <td>
                            {{ $employeeLeaf->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.employee') }}
                        </th>
                        <td>
                            {{ $employeeLeaf->employee->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.start_date') }}
                        </th>
                        <td>
                            {{ $employeeLeaf->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.end_date') }}
                        </th>
                        <td>
                            {{ $employeeLeaf->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.line_manager_approval') }}
                        </th>
                        <td>
                            {{ App\Models\EmployeeLeaf::LINE_MANAGER_APPROVAL_RADIO[$employeeLeaf->line_manager_approval] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.employeeLeaf.fields.hr_approval') }}
                        </th>
                        <td>
                            {{ App\Models\EmployeeLeaf::HR_APPROVAL_RADIO[$employeeLeaf->hr_approval] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.employee-leaves.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection