@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.employeeLeaf.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.employee-leaves.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="employee_id">{{ trans('cruds.employeeLeaf.fields.employee') }}</label>
                <select class="form-control select2 {{ $errors->has('employee') ? 'is-invalid' : '' }}" name="employee_id" id="employee_id" required>
                    @foreach($employees as $id => $entry)
                        <option value="{{ $id }}" {{ old('employee_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('employee'))
                    <div class="invalid-feedback">
                        {{ $errors->first('employee') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeLeaf.fields.employee_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.employeeLeaf.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeLeaf.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.employeeLeaf.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @if($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeLeaf.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.employeeLeaf.fields.hr_approval') }}</label>
                @foreach(App\Models\EmployeeLeaf::HR_APPROVAL_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('hr_approval') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="hr_approval_{{ $key }}" name="hr_approval" value="{{ $key }}" {{ old('hr_approval', 'Pending') === (string) $key ? 'checked' : '' }}>
                        <label class="form-check-label" for="hr_approval_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('hr_approval'))
                    <div class="invalid-feedback">
                        {{ $errors->first('hr_approval') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeLeaf.fields.hr_approval_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection