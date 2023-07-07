@can($viewGate)
    <a class="btn btn-xs btn-primary" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
        {{ trans('global.leaveView') }}
    </a>
@endcan
@if($row->hr_approval == 'Pending' || $row->hr_approval == 'Unapproved')
@can($editGate)
    <a class="btn btn-xs btn-info" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        {{ trans('global.leaveEdit') }}
    </a>
@endcan
@elseif($row->hr_approval == 'Approved')
@can($editGate)
    <a class="btn btn-xs btn-info" href="#">
        {{ trans('global.leaveEdit') }}
    </a>
@endcan
@endif

@can('employee_leave_delete')
    <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.leaveDelete') }}">
    </form>
@endcan
