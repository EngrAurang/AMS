<?php

namespace App\Http\Requests;

use App\Models\EmployeeLeaf;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEmployeeLeafRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('employee_leaf_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:employee_leaves,id',
        ];
    }
}
