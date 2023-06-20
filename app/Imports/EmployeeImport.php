<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Employee;
use Auth;

class EmployeeImport implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        echo "<pre>"; print_r($row['name']); die;
        $getEmp = Employee::where('emp_name', $row['name'])->where('emp_id',$row['employeecode'])->first();

        if(!empty($getEmp)){
            $emp_code = $getEmp->emp_id;
        }
        else{
            $emp_code = '';
        }

        $employee = Employee::where('emp_name', $row['name'])->where('emp_id',$emp_code)->first();
        if(empty($employee)){
            return new Employee([
                'emp_id'        => $emp_code,
                'emp_name'      => $row['name'],
                'phone'         => (float)$row['officephone'],
                'status'        => $row('employeestatus'),
                'created_at'    => time(),

            ]);
        }else{
            $employee = Employee::where('emp_name', $row['name'])->where('emp_id',$emp_code)->update([
                'emp_name'      => $row['name'],
                'phone'         => (float)$row['officephone'],
                'status'        => $row('employeestatus'),
                'updated_at'    => time(),
            ]);
        }
    }
}
