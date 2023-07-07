@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.apply') }} {{ trans('cruds.employeeLeaf.leave_title') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.employee-leaves.store") }}" enctype="multipart/form-data">
            @csrf
            @if(auth()->user()->roles[0]->title=='Admin')
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
            @else
            <input type="hidden" name="employee_id" id="employee_id" value="{{ auth()->user()->id }}" required>
            @endif
            <div class="form-group">
                <label class="required" for="leave_type">{{ trans('cruds.employeeLeaf.fields.leave') }}</label>
                <select class="form-control select2 {{ $errors->has('leave_type') ? 'is-invalid' : '' }}" name="leave_type" id="leave_type" required>
                <option value="">Please select</option>
                <option value="Annual Leave">Annual Leave</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Leave without Pay">Leave Without Pay</option>
                <option value="Off In Lieu">Off In Lieu</option>
                <option value="Compassionate Leave">Compassionate Leave</option>
                <option value="Parental Leave">Parental Leave</option>
                </select>
                @if($errors->has('leave_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('leave_type') }}
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
            {{-- <div class="form-group">
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
            </div> --}}
            <div class="form-group">
                <label for="leave_reason">{{ trans('cruds.employeeLeaf.fields.leave_reason') }}</label>

                <textarea class="form-control  {{ $errors->has('leave_reason') ? 'is-invalid' : '' }}" name="leave_reason" id="leave_reason">{!! old('leave_reason') !!}</textarea>
                @if($errors->has('leave_reason'))
                    <div class="invalid-feedback">
                        {{ $errors->first('leave_reason') }}
                    </div>
                @endif

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
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Get the form element
    const form = document.querySelector('form');

    // Add a submit event listener to the form
    form.addEventListener('submit', function(event) {
      event.preventDefault();

      // Get the values of the start date and end date inputs
      const startDate = new Date(document.getElementById('start_date').value);
      const endDate = new Date(document.getElementById('end_date').value);

      // Compare the dates
      if (startDate >= endDate) {
        Swal.fire({
            icon: "info",
            title: "Error!",
            text: "Start date must be smaller than end date",
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false
        });
        return;
      }else{
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const empId = $('#employee_id').val();

      $.ajax({
        url: '{{ route("check.leaves") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          start_date: startDate,
          end_date: endDate,
          empId: empId,
        },
        success: function(response) {
            // alert(response);
          if (response.success) {
            // Proceed with form submission
             // Submit the form if the validation passes
             form.submit();
          } else {
            // Display error message
             Swal.fire({
            icon: "info",
            title: "Error!",
            text: "The number of days exceeds the available leaves.",
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false
        });
            $('#validation-result').html(response.message);
          }
        },
        error: function() {
          // Handle AJAX error
          alert('An error occurred during the AJAX request.');
        }
      });
      }


    });
  </script>
@endsection
