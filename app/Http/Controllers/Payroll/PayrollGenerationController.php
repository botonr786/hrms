<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Payroll\MonthlyEmployeeCooperative;
use App\Models\Payroll\Payroll_detail;
use App\Models\Employee;
use App\Models\Rate_master;
use App\Models\Attendance\Process_attendance;
use App\Models\LeaveApprover\Leave_apply;
use App\Models\RateDetail;
use App\Models\Employee\Employee_pay_structure;

class PayrollGenerationController extends Controller
{
    public function payrollDashboard(Request $request)
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            return View('payroll/dashboard');
        } else {
            return redirect('/');
        }
    }
    public function getMonthlyCoopDeduction()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['monthlist'] = MonthlyEmployeeCooperative::select('month_yr')->distinct('month_yr')->get();
            $data['result'] = '';
            return view('payroll/monthly-coop', $data);
        } else {
            return redirect('/');
        }
    }
    public function viewMonthlyCoopDeduction(Request $request)
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->all());

            $data['monthlist'] = MonthlyEmployeeCooperative::select('month_yr')->distinct('month_yr')->get();

            $data['req_month'] = $request->month;

            $employee_rs = MonthlyEmployeeCooperative::join('employee', 'employee.emp_code', '=', 'monthly_employee_cooperatives.emp_code')
                ->select('employee.emp_fname', 'employee.emp_mname', 'employee.emp_lname', 'employee.Designation', 'employee.old_emp_code', 'monthly_employee_cooperatives.*')
                ->where('monthly_employee_cooperatives.month_yr', '=', $request->month)
                // ->where('monthly_employee_cooperatives.status', '=', 'process')
                // ->where('monthly_employee_cooperatives.emp_code', '=', '7086')
                ->orderBy('employee.emp_fname', 'asc')
                ->get();

            // dd($employee_rs);
            //$data['result'] = array();
            // if (count($employee_rs) > 0) {
            //     $data['result'] = $employee_rs;
            // }
            if (count($employee_rs) == 0) {
                Session::flash('error', 'Cooperative for the month ' . $request->month . ' already processed.');
                return redirect('payroll/vw-montly-coop');
            }
            $result = '';
            foreach ($employee_rs as $mainkey => $emcode) {

                $result .= '<tr id="' . $emcode->emp_code . '">
                            <td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
                            <td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->emp_code . '"></td>
                            <td>' . $emcode->old_emp_code . '</td>
                            <td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->emp_fname . ' ' . $emcode->emp_mname . ' ' . $emcode->emp_lname . '"></td>
                            <td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->Designation . '"></td>
                            <td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100%;" value="' . $request->month . '"></td>
                            <td><input type="number" step="any" class="form-control sm_d_coop" name="d_coop' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->coop_amount . '" id="d_coop_' . $emcode->emp_code . '"></td><td><input type="number" step="any" class="form-control sm_d_insup" name="d_insup' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->insurance_prem . '" id="d_insup_' . $emcode->emp_code . '"></td><td><input type="number" step="any" class="form-control sm_d_misc" name="d_misc' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->misc_ded . '" id="d_misc_' . $emcode->emp_code . '"></td>';
            }

            $data['result'] = $result;

            return view('payroll/monthly-coop', $data);
        } else {
            return redirect('/');
        }
    }

    public function addMonthlyCoopDeductionAllemployee()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {
            $data['result'] = '';

            return view('payroll/add-monthly-coop-all', $data);
        } else {
            return redirect('/');
        }
    }

    public function listCoopAllemployee(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {
            $payrolldate = explode('/', $request['month_yr']);
            $payroll_date = "0" . ($payrolldate[0] - 2);
            $origDate = $payroll_date . "/" . $payrolldate[1];

            $employee_rs = MonthlyEmployeeCooperative::join('employee', 'employee.emp_code', '=', 'monthly_employee_cooperatives.emp_code')
                ->select('employee.emp_fname', 'employee.emp_mname', 'employee.emp_lname', 'employee.designation', 'employee.old_emp_code', 'monthly_employee_cooperatives.*')
                ->where('monthly_employee_cooperatives.month_yr', '=', $request['month_yr'])
                // ->where('monthly_employee_cooperatives.status', '=', 'process')
                // ->where('monthly_employee_cooperatives.emp_code', '=', '7086')
                ->orderBy('employee.emp_fname', 'asc')
                ->get();

            //dd($employee_rs);
            //$data['result'] = array();
            // if (count($employee_rs) > 0) {
            //     $data['result'] = $employee_rs;
            // }
            
            // if (count($employee_rs) > 0) {
            //     Session::flash('error', 'Cooperative for the month ' . $request->month . ' already generated.');
            //     return redirect('payroll/vw-montly-coop');
            // }

            //$current_month_days = cal_days_in_month(CAL_GREGORIAN, $payrolldate[0], $payrolldate[1]);
            //dd($current_month_days);
            $datestring = $payrolldate[1] . '-' . $payrolldate[0] . '-01';
            // Converting string to date
            $date = strtotime($datestring);
            $current_month_days = date("t", strtotime(date("Y-m-t", $date)));

            $tomonthyr = $payrolldate[1] . "-" . $payroll_date . "-" . $current_month_days;
            $formatmonthyr = $payrolldate[1] . "-" . $payroll_date . "-01";

            $rate_rs = Rate_master::leftJoin('rate_details', 'rate_details.rate_id', '=', 'rate_masters.id')
                ->select('rate_details.*', 'rate_masters.head_name')
                ->get();

            $result = '';

            $emplist = Employee::where('status', '=', 'active')
                ->where('emp_status', '!=', 'TEMPORARY')
                ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
                ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
            // ->where('employee.emp_code', '=', '1831')
                //->orderBy('emp_fname', 'asc')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();
            foreach ($emplist as $mainkey => $emcode) {

                $process_payroll = $this->getEmpPayroll($emcode->emp_code, $payrolldate[0], $payrolldate[1]);
                $process_payroll = json_decode($process_payroll);

                //dd($process_payroll);

                $process_attendance = Process_attendance::where('process_attendances.employee_code', '=', $emcode->emp_code)
                    ->where('process_attendances.month_yr', '=', $origDate)
                    ->first();

                $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                    ->where('employee.emp_code', '=', $emcode->emp_code)
                    ->select('employee.*', 'employee_pay_structures.*')
                    ->first();

                // $leave_rs = Leave_apply::leftJoin('leave_types', 'leave_types.id', '=', 'leave_applies.leave_type')
                //     ->where('leave_applies.employee_id', '=', $emcode->emp_code)
                //     ->where('leave_applies.status', '=', 'APPROVED')
                //     ->whereBetween('leave_applies.from_date', array($formatmonthyr, $tomonthyr))
                //     ->orwhereBetween('leave_applies.to_date', array($formatmonthyr, $tomonthyr))
                //     ->select('leave_applies.*', 'leave_types.leave_type_name')
                //     ->get();

                $previous_payroll = Payroll_detail::where('employee_id', '=', $emcode->emp_code)
                //->where('month_yr','<',$request['month_yr'])
                    ->orderBy('month_yr', 'desc')
                    ->first();

                $d_coop = 0;
                $d_coop_show = '';
                $d_insuprem = 0;
                $d_insuprem_show = '';

                $calculate_basic_salary = $employee_rs->basic_pay;

                for ($j = 0; $j < sizeof($process_payroll[3]); $j++) {

                    //co_op
                    // if ($process_payroll[3][$j]->rate_id == '24') {
                    //     if ($process_payroll[0]->co_op == '1') {
                    //         if ($process_payroll[3][$j]->inpercentage != '0') {
                    //             $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                    //             $d_coop = $valc;
                    //         } else {
                    //             if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                    //                 $d_coop = $process_payroll[3][$j]->inrupees;
                    //             }
                    //         }
                    //         $d_coop_show = "readonly";
                    //     } else if ($process_payroll[0]->co_op != null && $process_payroll[0]->co_op != '') {
                    //         $d_coop = $process_payroll[0]->co_op;
                    //         //                           $d_coop_show = "";
                    //     } else {
                    //         $valc = 0;
                    //         $d_coop = $valc;
                    //         $d_coop_show = "readonly";
                    //     }
                    // }

                    //insu_prem
                    if ($process_payroll[3][$j]->rate_id == '19') {
                        if ($process_payroll[0]->insu_prem == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = ($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $valc = round($valc,2);
                                $d_insuprem = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $d_insuprem = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $d_insuprem_show = "readonly";
                        } else if ($process_payroll[0]->insu_prem != null && $process_payroll[0]->insu_prem != '') {
                            $d_insuprem = $process_payroll[0]->insu_prem;
                            $d_insuprem_show = "readonly";
                        } else {
                            $valc = 0;
                            $d_insuprem = $valc;
                            $d_insuprem_show = "readonly";
                        }
                        
                    }

                }

                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_code . '"></td>
                                <td>' . $employee_rs->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_fname . ' ' . $employee_rs->emp_mname . ' ' . $employee_rs->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->designation. '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100%;" value="' . $request['month_yr'] . '"></td>
								<td><input type="number" step="any" class="form-control sm_d_coop" name="d_coop' . $emcode->emp_code . '" style="width:100%;" value="' . $d_coop . '" id="d_coop_' . $emcode->emp_code . '" ' . $d_coop_show . '></td><td><input type="number" step="any" class="form-control sm_d_insup" name="d_insup' . $emcode->emp_code . '" style="width:100%;" value="'.$d_insuprem.'" id="d_insup_' . $emcode->emp_code . '"></td></td><td><input type="number" step="any" class="form-control sm_d_misc" name="d_misc' . $emcode->emp_code . '" style="width:100%;" value="0" id="d_misc_' . $emcode->emp_code . '"></td>';

                // print_r($result);
                // die();
            }
            // print_r($result);
            // die();
            $month_yr_new = $request['month_yr'];
            return view('payroll/add-monthly-coop-all', compact('result','month_yr_new'));
        } else {
            return redirect('/');
        }
    }

    public function SaveCoopAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            // dd($request->cboxes);
            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);
                $sm_d_coop_ctrl = explode(',', $request->sm_d_coop_ctrl);
                $sm_d_insup_ctrl = explode(',', $request->sm_d_insup_ctrl);
                $sm_d_misc_ctrl = explode(',', $request->sm_d_misc_ctrl);

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];
                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $data['coop_amount'] = $sm_d_coop_ctrl[$index];
                    $data['insurance_prem'] = $sm_d_insup_ctrl[$index];
                    $data['misc_ded'] = $sm_d_misc_ctrl[$index];

                    $data['status'] = 'process';
                    $data['created_at'] = date('Y-m-d');

                    $employee_pay_structure = Payroll_detail::where('employee_id', '=', $value)
                        ->where('month_yr', '=', $data['month_yr'])
                        ->first();

                    // if (!empty($employee_pay_structure)) {
                    //     Session::flash('message', 'Payroll already generated for said period');
                    // } else {
                        $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                            ->where('employee.emp_code', '=', $value)
                            ->select('employee_pay_structures.*')
                            ->first();

                        $data['pay_structure_amount'] = $employee_rs->co_op;
                        if ($data['pay_structure_amount'] == null) {
                            $data['pay_structure_amount'] = 0;
                        }
                        $data['pay_structure_insu_prem'] = $employee_rs->insu_prem;
                        if ($data['pay_structure_insu_prem'] == null) {
                            $data['pay_structure_insu_prem'] = 0;
                        }
                        $data['pay_structure_misc_ded'] = $employee_rs->emp_misc_ded;
                        if ($data['pay_structure_misc_ded'] == null) {
                            $data['pay_structure_misc_ded'] = 0;
                        }
                        $ps_id = $employee_rs->id;

                        $monthlyEmployeeCooperative = MonthlyEmployeeCooperative::where('emp_code', '=', $value)
                            ->where('month_yr', '=', $sm_month_yr_ctrl[$index])
                            ->first();

                        //dd($monthlyEmployeeCooperative);

                        if (!empty($monthlyEmployeeCooperative)) {
                            MonthlyEmployeeCooperative::where('month_yr', $sm_month_yr_ctrl[$index])->where('emp_code', $value)->update($data);
                            Session::flash('message', 'Record Successfully Updated.');
                            //   Session::flash('message', 'Record Already provided for said period for employee - ' . $value);
                        } else {
                            MonthlyEmployeeCooperative::insert($data);
                            // $payupdate = array(
                            //     'co_op' => $data['coop_amount'],
                            //     'insu_prem' => $data['insurance_prem'],
                            //     'misc_ded' => $data['misc_ded'],
                            //     'updated_at' => date('Y-m-d h:i:s'),
                            // );
                            // Employee_pay_structure::where('employee_code', $value)->update($payupdate);
                            Session::flash('message', 'Record Successfully Saved.');
                        }

                    // }

                }
            } else {
                Session::flash('error', 'No Record is selected');
            }

            return redirect('payroll/vw-montly-coop');
        } else {
            return redirect('/');
        }
    }
    public function UpdateCoopAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            $request->status=$request->statusme;
             //dd($request->status);

            if (isset($request->deleteme) && $request->deleteme == 'yes') {

                $employee_payroll = Payroll_detail::where('month_yr', '=', $request->deletemy)->get();
                if (count($employee_payroll) > 0) {
                    Session::flash('error', 'Records cannot be deleted as payroll for the month already generated.');
                    return redirect('payroll/vw-montly-coop');
                }

                MonthlyEmployeeCooperative::where('month_yr', $request->deletemy)->delete();
                Session::flash('message', 'All generated records deleted successfully.');
                return redirect('payroll/vw-montly-coop');
            }

            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);
                $sm_d_coop_ctrl = explode(',', $request->sm_d_coop_ctrl);
                $sm_d_insup_ctrl = explode(',', $request->sm_d_insup_ctrl);
                $sm_d_misc_ctrl = explode(',', $request->sm_d_misc_ctrl);
                

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];
                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $employee_pay_structure = Payroll_detail::join('employee', 'employee.emp_code', '=', 'payroll_details.employee_id')
                        ->select('employee.old_emp_code', 'payroll_details.*')
                        ->where('payroll_details.employee_id', '=', $value)
                        ->where('payroll_details.month_yr', '=', $data['month_yr'])
                        ->first();

                    // if (!empty($employee_pay_structure)) {
                    //     Session::flash('error', 'Payroll already generated for said period for Employee Code - '.$employee_pay_structure->old_emp_code);
                    // } else {

                        $data['coop_amount'] = $sm_d_coop_ctrl[$index];
                        $data['insurance_prem'] = $sm_d_insup_ctrl[$index];
                        $data['misc_ded'] = $sm_d_misc_ctrl[$index];
                        $data['status'] = $request->status==''?'process':$request->status;
                        $data['updated_at'] = date('Y-m-d');

                        // dd($data);
                        MonthlyEmployeeCooperative::where('month_yr', $sm_month_yr_ctrl[$index])->where('emp_code', $value)->update($data);

                        // $payupdate = array(
                        //     'co_op' => $data['coop_amount'],
                        //     'insu_prem' => $data['insurance_prem'],
                        //     'misc_ded' => $data['misc_ded'],
                        //     'updated_at' => date('Y-m-d h:i:s'),
                        // );
                        // Employee_pay_structure::where('employee_code', $value)->update($payupdate);
                        Session::flash('message', 'Record Successfully Updated.');

                    // }

                }
            } else {
                Session::flash('error', 'No Record is selected');
            }

            return redirect('payroll/vw-montly-coop');
        } else {
            return redirect('/');
        }
    }
    public function getEmpPayroll($empid, $month, $year)
    {
        
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $mnth_yr = $month . '/' . $year;

            //$tomonthyr=date("Y-m-t");
            //$formatmonthyr=date("Y-m-01");
            $tomonthyr = $year . "-" . $month . "-31";
            $formatmonthyr = $year . "-" . $month . "-01";

            $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                ->where('employee.emp_code', '=', $empid)
                ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
                ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
                ->select('employee.*', 'employee_pay_structures.*')->first();

            $leave_rs = Leave_apply::leftJoin('leave_types', 'leave_types.id', '=', 'leave_applies.leave_type')
                ->where('leave_applies.employee_id', '=', $empid)
                ->where('leave_applies.status', '=', 'APPROVED')
                ->where('leave_applies.from_date', '>=', $formatmonthyr)
                ->where('leave_applies.to_date', '<=', $tomonthyr)
                ->select('leave_applies.*', 'leave_types.leave_type_name')
                ->get();

            $process_attendance = Process_attendance::where('process_attendances.employee_code', '=', $empid)
                ->where('process_attendances.month_yr', '=', $mnth_yr)
                ->first();
            //->toSql();

            $rate_rs = RateDetail::leftJoin('rate_masters', 'rate_masters.id', '=', 'rate_details.rate_id')
                ->select('rate_details.*', 'rate_masters.head_name', 'rate_masters.head_type')
                ->where('rate_details.from_date', '>=', date($year.'-01-01'))
                ->where('rate_details.to_date', '<=', date($year.'-12-31'))

                ->get();

                //dd($rate_rs);

            return json_encode(array($employee_rs, $leave_rs, $process_attendance, $rate_rs));
        } else {
            return json_encode(array());
        }
    }

}
