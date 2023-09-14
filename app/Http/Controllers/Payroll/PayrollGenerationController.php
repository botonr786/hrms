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
use App\Models\Payroll\MonthlyEmployeeItax;
use App\Models\Payroll\MonthlyEmployeeAllowance;
use App\Models\Payroll\MonthlyEmployeeOvertime;

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

            //dd($request->cboxes);
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
    public function getMonthlyItaxDeduction()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['monthlist'] = MonthlyEmployeeItax::select('month_yr')->distinct('month_yr')->get();

            $data['result'] = '';
            $payroll_details_rs = '';
            $data['Classname'] = 'monthly-incometax';

            return view('payroll/monthly-incometax', $data);
        } else {
            return redirect('/');
        }
    }
    public function addMonthlyItaxDeductionAllemployee()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {
            $data['Classname'] = 'monthly-incometax';
            $data['result'] = '';

            return view('payroll/add-monthly-itax-all', $data);
        } else {
            return redirect('/');
        }
    }
    public function listItaxAllemployee(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {
            $payrolldate = explode('/', $request['month_yr']);
            //$payroll_date = "0" . ($payrolldate[0] - 2);
            $payroll_date = $payrolldate[0];
            $origDate = $payroll_date . "/" . $payrolldate[1];

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
                //->where('emp_status', '!=', 'TEMPORARY')
                ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
                ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
            // ->where('employee.emp_code', '=', '5571')
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

                $d_itax = 0;
                $d_itax_show = '';
                $calculate_basic_salary = $employee_rs->basic_pay;

                for ($j = 0; $j < sizeof($process_payroll[3]); $j++) {

                    //i_tax
                    if ($process_payroll[3][$j]->rate_id == '18') {
                        if ($process_payroll[0]->i_tax == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $d_itax = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $d_itax = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $d_itax_show = "readonly";
                        } else if ($process_payroll[0]->i_tax != null && $process_payroll[0]->i_tax != '') {
                            $d_itax = $process_payroll[0]->i_tax;
                            //                           $d_itax_show = "";
                        } else {
                            $valc = 0;
                            $d_itax = $valc;
                            $d_itax_show = "readonly";
                        }
                    }

                }

                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_code . '"></td>
                                <td>' . $employee_rs->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_fname . ' ' . $employee_rs->emp_mname . ' ' . $employee_rs->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->designation . '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100%;" value="' . $request['month_yr'] . '"></td>
								<td><input type="number" step="any" class="form-control sm_d_itax" name="d_itax' . $emcode->emp_code . '" style="width:100%;" value="' . $d_itax . '" id="d_itax_' . $emcode->emp_code . '" ' . $d_itax_show . '></td>';

                // print_r($result);
                // die();
            }
            // print_r($result);
            // die();
            $month_yr_new = $request['month_yr'];
            return view('payroll/add-monthly-itax-all', compact('result','month_yr_new'));
        } else {
            return redirect('/');
        }
    }
    public function SaveItaxAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->cboxes);
            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);
                $sm_d_itax_ctrl = explode(',', $request->sm_d_itax_ctrl);

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];

                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $data['itax_amount'] = $sm_d_itax_ctrl[$index];

                    $data['status'] = 'process';
                    $data['created_at'] = date('Y-m-d');

                    //dd($data);

                    $employee_pay_structure = Payroll_detail::where('employee_id', '=', $value)
                        ->where('month_yr', '=', $data['month_yr'])
                        ->first();

                    // dd($employee_pay_structure);

                    if (!empty($employee_pay_structure)) {
                        Session::flash('error', 'Payroll already generated for said period');
                    } else {
                        $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                            ->where('employee.emp_code', '=', $value)
                            ->select('employee_pay_structures.*')
                            ->first();

                        $data['pay_structure_amount'] = $employee_rs->i_tax;
                        if ($data['pay_structure_amount'] == null) {
                            $data['pay_structure_amount'] = 0;
                        }
                        $ps_id = $employee_rs->id;

                        $monthlyEmployeeItax = MonthlyEmployeeItax::where('emp_code', '=', $value)
                            ->where('month_yr', '=', $sm_month_yr_ctrl[$index])
                            ->first();

                        //dd($monthlyEmployeeItax);

                        if (!empty($monthlyEmployeeItax)) {
                            Session::flash('error', 'Record Already provided for said period for employee - ' . $value);
                        } else {
                            MonthlyEmployeeItax::insert($data);
                            // $payupdate = array(
                            //     'i_tax' => $data['itax_amount'],
                            //     'updated_at' => date('Y-m-d h:i:s'),
                            // );
                            // Employee_pay_structure::where('employee_code', $value)->update($payupdate);
                            Session::flash('message', 'Record Successfully Saved.');
                        }

                    }

                }
            } else {
                Session::flash('error', 'No Record is selected');
            }

            return redirect('payroll/vw-montly-itax');
        } else {
            return redirect('/');
        }
    }
    public function viewMonthlyItaxDeduction(Request $request)
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->all());

            $data['monthlist'] = MonthlyEmployeeItax::select('month_yr')->distinct('month_yr')->get();

            $data['req_month'] = $request->month;

            $employee_rs = MonthlyEmployeeItax::join('employee', 'employee.emp_code', '=', 'monthly_employee_itaxes.emp_code')
                ->select('employee.emp_fname', 'employee.emp_mname', 'employee.emp_lname', 'employee.designation', 'employee.old_emp_code', 'monthly_employee_itaxes.*')
                ->where('monthly_employee_itaxes.month_yr', $request->month)
                // ->where('monthly_employee_itaxes.status', 'process')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();

            //dd($employee_rs);
            //$data['result'] = array();
            // if (count($employee_rs) > 0) {
            //     $data['result'] = $employee_rs;
            // }

            $result = '';

            // if (count($employee_rs) == 0) {
            //     Session::flash('error', 'Income Tax for the month ' . $request->month . ' already processed.');
            //     return redirect('payroll/vw-montly-itax');
            // }
            foreach ($employee_rs as $mainkey => $emcode) {

                $result .= '<tr id="' . $emcode->emp_code . '">
                            <td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
                            <td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->emp_code . '"></td>
                            <td>' . $emcode->old_emp_code . '</td>
                            <td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->emp_fname . ' ' . $emcode->emp_mname . ' ' . $emcode->emp_lname . '"></td>
                            <td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->designation . '"></td>
                            <td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100%;" value="' . $request->month . '"></td>
                            <td><input type="number" step="any" class="form-control sm_d_itax" name="d_itax' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->itax_amount . '" id="d_itax_' . $emcode->emp_code . '"></td>';
            }

            $data['result'] = $result;

            return view('payroll/monthly-incometax', $data);
        } else {
            return redirect('/');
        }
    }
    public function UpdateItaxAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            // dd($request->cboxes);
            $request->status=$request->statusme;
            if (isset($request->deleteme) && $request->deleteme == 'yes') {

                $employee_payroll = Payroll_detail::where('month_yr', '=', $request->deletemy)->get();
                if (count($employee_payroll) > 0) {
                    Session::flash('error', 'Records cannot be deleted as payroll for the month already generated.');
                    return redirect('payroll/vw-montly-itax');
                }

                MonthlyEmployeeItax::where('month_yr', $request->deletemy)->delete();
                Session::flash('message', 'All generated records deleted successfully.');
                return redirect('payroll/vw-montly-itax');
            }

            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);
                $sm_d_itax_ctrl = explode(',', $request->sm_d_itax_ctrl);

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];
                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $data['itax_amount'] = $sm_d_itax_ctrl[$index];

                    $data['status'] = $request->status==''?'process':$request->status;
                    $data['updated_at'] = date('Y-m-d');

                    // dd($data);

                    // $employee_pay_structure = Payroll_detail::where('employee_id', '=', $value)
                    //     ->where('month_yr', '=', $data['month_yr'])
                    //     ->first();
                    $employee_pay_structure = Payroll_detail::join('employee', 'employee.emp_code', '=', 'payroll_details.employee_id')
                        ->select('employee.old_emp_code', 'payroll_details.*')
                        ->where('payroll_details.employee_id', '=', $value)
                        ->where('payroll_details.month_yr', '=', $data['month_yr'])
                        ->first();

                    // if (!empty($employee_pay_structure)) {
                    //     Session::flash('error', 'Payroll already generated for said period against Employee Code - '.$employee_pay_structure->old_emp_code);
                    // } else {

                        MonthlyEmployeeItax::where('month_yr', $sm_month_yr_ctrl[$index])->where('emp_code', $value)->update($data);

                        // $payupdate = array(
                        //     'i_tax' => $data['itax_amount'],
                        //     'updated_at' => date('Y-m-d h:i:s'),
                        // );
                        // Employee_pay_structure::where('employee_code', $value)->update($payupdate);
                        Session::flash('message', 'Record Successfully Updated.');

                    // }

                }
            } else {
                Session::flash('error', 'No Record is selected');
            }

            return redirect('payroll/vw-montly-itax');
        } else {
            return redirect('/');
        }
    }
    
    public function getMonthlyEarningAllowances()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['monthlist'] = MonthlyEmployeeAllowance::select('month_yr')->distinct('month_yr')->get();
            $data['Classname'] = 'vw-montly-allowances';
            $data['result'] = '';
            $payroll_details_rs = '';

            return view('payroll/monthly-allowance', $data);
        } else {
            return redirect('/');
        }
    }
    public function viewMonthlyEarningAllowances(Request $request)
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->all());

            $data['monthlist'] = MonthlyEmployeeAllowance::select('month_yr')->distinct('month_yr')->get();

            $data['req_month'] = $request->month;

        //     $emplist = Process_attendance::join('employee', 'employee.emp_code', '=', 'process_attendances.employee_code')
        //     ->select('employee.*', 'process_attendances.*')
        //     ->where('process_attendances.month_yr', '=', $request['month_yr'])
        //     ->where('process_attendances.status', '=', 'A')
        //     //->where('employee.emp_status', '!=', 'TEMPORARY')
        //     ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
        //     ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
        //     ->where('employee.status', '=', 'active')
        // // ->where('employee.emp_code', '=', '1831')
        //     ->orderBy('employee.emp_fname', 'asc')
        //     ->get();


            $employee_rs = MonthlyEmployeeAllowance::join('employee', 'employee.emp_code', '=', 'monthly_employee_allowances.emp_code')
                ->join('process_attendances', 'employee.emp_code', '=', 'process_attendances.employee_code')
                ->select('employee.emp_fname', 'employee.emp_mname', 'employee.emp_lname', 'employee.designation', 'employee.old_emp_code', 'monthly_employee_allowances.*','process_attendances.no_of_working_days','process_attendances.no_of_days_absent','process_attendances.no_of_days_leave_taken','process_attendances.no_of_present','process_attendances.no_of_tour_leave','process_attendances.no_of_days_salary')
                ->where('monthly_employee_allowances.month_yr', $request->month)
                ->where('process_attendances.month_yr', '=', $request->month)
                // ->where('process_attendances.status', '=', 'A')
                // ->where('monthly_employee_allowances.status', 'process')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();

           //dd($employee_rs);

            $result = '';
            // if (count($employee_rs) == 0) {
            //     Session::flash('error', 'Allowances for the month ' . $request->month . ' already processed.');
            //     return redirect('payroll/vw-montly-allowances');
            // }
            foreach ($employee_rs as $mainkey => $emcode) {
                $no_of_present=0;
                $no_of_days_leave_taken=0;
                $no_of_days_absent=0;

                if(isset($emcode->no_of_present) && $emcode->no_of_present!=''){
                    $no_of_present=$emcode->no_of_present;
                }
                if(isset($emcode->no_of_days_leave_taken) && $emcode->no_of_days_leave_taken!=''){
                    $no_of_days_leave_taken=$emcode->no_of_days_leave_taken;
                }
                if(isset($emcode->no_of_days_absent) && $emcode->no_of_days_absent!=''){
                    $no_of_days_absent=$emcode->no_of_days_absent;
                }

                $infoTitle="Absent Days:".$no_of_days_absent."  Leave Days:".$no_of_days_leave_taken;

                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $emcode->emp_code . '"><input type="hidden" readonly class="form-control sm_tot_wdays" name="tot_wdays' . $emcode->emp_code . '" style="width:100%;" id="tot_wdays_' . $emcode->emp_code . '" value="' . $emcode->total_days . '"></td>
                                <td>' . $emcode->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100px;" value="' . $emcode->emp_fname . ' ' . $emcode->emp_mname . ' ' . $emcode->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100px;" value="' . $emcode->month_yr . '"></td>
                                <td><input type="hidden" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100px;" value="' . $emcode->designation . '"><div class="row"><div class="col-md-6"><input type="text" readonly class="form-control sm_no_of_present" name="no_of_present' . $emcode->emp_code . '" style="width:70px;" value="' . $no_of_present . '"></div><div class="col-md-6"><a title="'.$infoTitle.'"><i class="fa fa-info" style="padding-top:7px;"></i></a></div></div></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_tiff" id="no_d_tiff_' . $emcode->emp_code . '" name="no_d_tiff' . $emcode->emp_code . '" value="' . $emcode->no_days_tiffalw . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="text" style="width:100px;" class="form-control sm_et_tiffalw" id="et_tiffalw_' . $emcode->emp_code . '" name="et_tiffalw' . $emcode->emp_code . '" value="' . $emcode->pay_structure_tiff_alw . '" readonly></td>';

                $result .= '<td><input type="text" style="width:100px;" class="form-control sm_e_tiffalw" id="e_tiffalw_' . $emcode->emp_code . '" name="e_tiffalw' . $emcode->emp_code . '" value="' . $emcode->tiffin_alw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_conv" id="no_d_conv_' . $emcode->emp_code . '" name="no_d_conv' . $emcode->emp_code . '" value="' . $emcode->no_days_convalw . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_et_convalw" id="et_convalw_' . $emcode->emp_code . '" name="et_convalw' . $emcode->emp_code . '" value="' . $emcode->pay_structure_conv_alw. '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_conv" name="e_conv' . $emcode->emp_code . '" value="' . $emcode->convence_alw . '" id="e_conv_' . $emcode->emp_code . '" readonly ></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_misc" id="no_d_misc_' . $emcode->emp_code . '" name="no_d_misc' . $emcode->emp_code . '" value="' . $emcode->no_days_miscalw . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_et_miscalw" id="et_miscalw_' . $emcode->emp_code . '" name="et_miscalw' . $emcode->emp_code . '" value="' . $emcode->pay_structure_misc_alw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_miscalw" name="e_miscalw' . $emcode->emp_code . '" value="' . $emcode->misc_alw . '" id="e_miscalw_' . $emcode->emp_code . '" readonly ></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_extra_misc_alw" name="e_extra_misc_alw' . $emcode->emp_code . '" value="' . $emcode->extra_misc_alw . '" id="e_extra_misc_alw_' . $emcode->emp_code . '" ></td>';
                // other_allw
                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_other" id="no_d_other_' . $emcode->emp_code . '" name="no_d_other' . $emcode->emp_code . '" value="' . $emcode->no_days_otheralw . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_et_otheralw" id="et_otheralw_' . $emcode->emp_code . '" name="et_otheralw' . $emcode->emp_code . '" value="' . $emcode->pay_structure_other_alw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_otheralw" name="e_otheralw' . $emcode->emp_code . '" value="' . $emcode->other_alw . '" id="e_otheralw_' . $emcode->emp_code . '" readonly ></td>';

        
            }

            $data['result'] = $result;
            $data['month_yr_new'] = $request->month;
            return view('payroll/monthly-allowance', $data);
        } else {
            return redirect('/');
        }
    }
    public function addMonthlyAllowancesAllemployee()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['result'] = '';

            return view('payroll/add-monthly-allowance-all', $data);
        } else {
            return redirect('/');
        }
    }
    public function listAllowancesAllemployee(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {
            $payrolldate = explode('/', $request['month_yr']);
            //$payroll_date = "0" . ($payrolldate[0] - 2);
            $payroll_date = $payrolldate[0];
            $origDate = $payroll_date . "/" . $payrolldate[1];

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

            // $emplist = Employee::where('status', '=', 'active')
            //     ->where('emp_status', '!=', 'TEMPORARY')
            //     ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
            //     ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
            // // ->where('employee.emp_code', '=', '5571')
            //     ->orderBy('emp_fname', 'asc')
            //     ->get();

            $emplist = Process_attendance::join('employee', 'employee.emp_code', '=', 'process_attendances.employee_code')
                ->select('employee.*', 'process_attendances.*')
                ->where('process_attendances.month_yr', '=', $request['month_yr'])
                // ->where('process_attendances.status', '=', 'A')
                //->where('employee.emp_status', '!=', 'TEMPORARY')
                ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
                ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
                ->where('employee.status', '=', 'active')
            // ->where('employee.emp_code', '=', '1831')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();

                //dd($emplist);

            if (count($emplist) == 0) {
                Session::flash('error', 'Please process the attandance for the month before generating allowances.');
                return redirect('payroll/add-montly-allowances');
            }

            foreach ($emplist as $mainkey => $emcode) {

                $process_payroll = $this->getEmpPayroll($emcode->emp_code, $payrolldate[0], $payrolldate[1]);
                $process_payroll = json_decode($process_payroll);

                // dd($process_payroll);

                $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                    ->where('employee.emp_code', '=', $emcode->emp_code)
                    ->select('employee.*', 'employee_pay_structures.*')
                    ->first();

                // $previous_payroll = Payroll_detail::where('employee_id', '=', $emcode->emp_code)
                // //->where('month_yr','<',$request['month_yr'])
                //     ->orderBy('month_yr', 'desc')
                //     ->first();

                $e_tiffalw = 0;
                $e_tiffalw_show = '';
                $e_othalw = 0;
                $e_othalw_show = '';
                $e_conv = 0;
                $e_conv_show = '';
                $e_medical = 0;
                $e_medical_show = '';
                $e_miscalw = 0;
                $e_miscalw_show = '';
                $e_extra_misc_alw = 0;
                $e_extra_misc_alw_show = '';
                $e_other_allw = 0;
                $e_other_allw_show = '';
                

                $calculate_basic_salary = $employee_rs->basic_pay;

                for ($j = 0; $j < sizeof($process_payroll[3]); $j++) {

                    //tiff alw
                    if ($process_payroll[3][$j]->rate_id == '6') {
                        if ($process_payroll[0]->tiff_alw == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $e_tiffalw = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $e_tiffalw = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $e_tiffalw_show = "readonly";
                        } else if ($process_payroll[0]->tiff_alw != null && $process_payroll[0]->tiff_alw != '') {
                            $e_tiffalw = $process_payroll[0]->tiff_alw;
                            $e_tiffalw_show = "readonly";
                        } else {
                            $valc = 0;
                            $e_tiffalw = $valc;
                            $e_tiffalw_show = "readonly";
                        }
                    }

                    //conv
                    if ($process_payroll[3][$j]->rate_id == '7') {
                        if ($process_payroll[0]->conv == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $e_conv = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $e_conv = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $e_conv_show = "readonly";
                        } else if ($process_payroll[0]->conv != null && $process_payroll[0]->conv != '') {
                            $e_conv = $process_payroll[0]->conv;
                             $e_conv_show = "readonly";
                        } else {
                            $valc = 0;
                            $e_conv = $valc;
                            $e_conv_show = "readonly";
                        }
                    }

                    //misc_alw
                    if ($process_payroll[3][$j]->rate_id == '9') {
                        if ($process_payroll[0]->misc_alw == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $e_miscalw = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $e_miscalw = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $e_miscalw_show = "readonly";
                        } else if ($process_payroll[0]->misc_alw != null && $process_payroll[0]->misc_alw != '') {
                            $e_miscalw = $process_payroll[0]->misc_alw;
                             $e_miscalw_show = "readonly";
                        } else {
                            $valc = 0;
                            $e_miscalw = $valc;
                            $e_miscalw_show = "readonly";
                        }
                    }

                    //over_time
                    // if ($process_payroll[3][$j]->rate_id == '10') {
                    //     if ($process_payroll[0]->over_time == '1') {
                    //         if ($process_payroll[3][$j]->inpercentage != '0') {
                    //             $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                    //             $e_overtime = $valc;
                    //         } else {
                    //             if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                    //                 $e_overtime = $process_payroll[3][$j]->inrupees;
                    //             }
                    //         }
                    //         $e_overtime_show = "readonly";
                    //     } else if ($process_payroll[0]->over_time != null && $process_payroll[0]->over_time != '') {
                    //         $e_overtime = $process_payroll[0]->over_time;
                    //         //$e_overtime_show = "";
                    //     } else {
                    //         $valc = 0;
                    //         $e_overtime = $valc;
                    //         $e_overtime_show = "readonly";
                    //     }
                    // }
                    
                    // other_allowence
                    
                    if ($process_payroll[3][$j]->rate_id == '5') {
                        if ($process_payroll[0]->others_alw == '1') {
                            if ($process_payroll[3][$j]->inpercentage != '0') {
                                $valc = round($calculate_basic_salary * $process_payroll[3][$j]->inpercentage / 100);
                                $e_other_allw = $valc;
                            } else {
                                if (($calculate_basic_salary <= $process_payroll[3][$j]->max_basic) && ($calculate_basic_salary >= $process_payroll[3][$j]->min_basic)) {
                                    $e_other_allw = $process_payroll[3][$j]->inrupees;
                                }
                            }
                            $e_other_allw_show = "readonly";
                        } else if ($process_payroll[0]->others_alw != null && $process_payroll[0]->others_alw != '') {
                            $e_other_allw = $process_payroll[0]->others_alw;
                            $e_other_allw_show = "readonly";
                        } else {
                            $valc = 0;
                            $e_other_allw = $valc;
                            $e_other_allw_show = "readonly";
                        }
                    }

                }

                $no_of_present=0;
                $no_of_days_leave_taken=0;
                $no_of_days_absent=0;

                if(isset($emcode->no_of_present) && $emcode->no_of_present!=''){
                    $no_of_present=$emcode->no_of_present;
                }
                if(isset($emcode->no_of_days_leave_taken) && $emcode->no_of_days_leave_taken!=''){
                    $no_of_days_leave_taken=$emcode->no_of_days_leave_taken;
                }
                if(isset($emcode->no_of_days_absent) && $emcode->no_of_days_absent!=''){
                    $no_of_days_absent=$emcode->no_of_days_absent;
                }

                $infoTitle="Absent Days:".$no_of_days_absent."  Leave Days:".$no_of_days_leave_taken;


                $cal_tiff_alw = $e_tiffalw;
                $tot_wdays = $no_of_present + $no_of_days_leave_taken + $no_of_days_absent;

                $perday_tiffalw = $e_tiffalw / $tot_wdays;
                $cal_tiff_alw = round(($perday_tiffalw * $no_of_present), 2);

                $cal_conv_alw = $e_conv;
                $perday_convalw = $e_conv / $tot_wdays;
                $cal_conv_alw = round(($perday_convalw * $no_of_present), 2);

                $cal_misc_alw = $e_miscalw;
                $perday_miscalw = $e_miscalw / $tot_wdays;
                $cal_misc_alw = round(($perday_miscalw * $no_of_present), 2);
                
                $cal_other_alw = $e_other_allw;
                $perday_otheralw = $e_other_allw / $tot_wdays;
                $cal_other_alw = round(($perday_otheralw * $no_of_present), 2);
                
                

                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_code . '"><input type="hidden" readonly class="form-control sm_tot_wdays" name="tot_wdays' . $emcode->emp_code . '" style="width:100%;" id="tot_wdays_' . $emcode->emp_code . '" value="' . $tot_wdays . '"></td>
                                <td>' . $employee_rs->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100px;" value="' . $employee_rs->emp_fname . ' ' . $employee_rs->emp_mname . ' ' . $employee_rs->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100px;" value="' . $request['month_yr'] . '"></td>
                                <td><input type="hidden" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100px;" value="' . $employee_rs->designation . '"><div class="row"><div class="col-md-6"><input type="text" readonly class="form-control sm_no_of_present" name="no_of_present' . $emcode->emp_code . '" style="width:70px;" value="' . $no_of_present . '"></div><div class="col-md-6"><a title="'.$infoTitle.'"><i class="fa fa-info" style="padding-top:7px;"></i></a></div></div></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_tiff" id="no_d_tiff_' . $emcode->emp_code . '" name="no_d_tiff' . $emcode->emp_code . '" value="' . $emcode->no_of_present . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="text" style="width:100%;" class="form-control sm_et_tiffalw" id="et_tiffalw_' . $emcode->emp_code . '" name="et_tiffalw' . $emcode->emp_code . '" value="' . $e_tiffalw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_tiffalw" id="e_tiffalw_' . $emcode->emp_code . '" name="e_tiffalw' . $emcode->emp_code . '" value="' . $cal_tiff_alw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_conv" id="no_d_conv_' . $emcode->emp_code . '" name="no_d_conv' . $emcode->emp_code . '" value="' . $emcode->no_of_present . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_et_convalw" id="et_convalw_' . $emcode->emp_code . '" name="et_convalw' . $emcode->emp_code . '" value="' . $e_conv . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_conv" name="e_conv' . $emcode->emp_code . '" value="' . $cal_conv_alw . '" id="e_conv_' . $emcode->emp_code . '" ' . $e_conv_show . '></td>';

                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_misc" id="no_d_misc_' . $emcode->emp_code . '" name="no_d_misc' . $emcode->emp_code . '" value="' . $emcode->no_of_present . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_et_miscalw" id="et_miscalw_' . $emcode->emp_code . '" name="et_miscalw' . $emcode->emp_code . '" value="' . $e_miscalw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_miscalw" name="e_miscalw' . $emcode->emp_code . '" value="' . $cal_misc_alw . '" id="e_miscalw_' . $emcode->emp_code . '" ' . $e_miscalw_show . '></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_extra_misc_alw" name="e_extra_misc_alw' . $emcode->emp_code . '" value="' . $e_extra_misc_alw . '" id="e_extra_misc_alw_' . $emcode->emp_code . '" ' . $e_extra_misc_alw_show . '></td>';
                
                $result .= '<td><input type="number" step="any" style="width:80px;" class="form-control sm_no_d_other" id="no_d_other_' . $emcode->emp_code . '" name="no_d_other' . $emcode->emp_code . '" value="' . $emcode->no_of_present . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="text" style="width:100%;" class="form-control sm_et_otheralw" id="et_otheralw_' . $emcode->emp_code . '" name="et_otheralw' . $emcode->emp_code . '" value="' . $e_other_allw . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_e_otheralw" id="e_otheralw_' . $emcode->emp_code . '" name="e_otheralw' . $emcode->emp_code . '" value="' . $cal_other_alw . '" readonly></td>';

                // print_r($result);
                // die();
            }
            // print_r($result);
            // die();
            $month_yr_new = $request['month_yr'];
            return view('payroll/add-monthly-allowance-all', compact('result','month_yr_new'));
        } else {
            return redirect('/');
        }
    }
    public function SaveAllowancesAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->cboxes);
            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);

                $sm_tot_wdays_ctrl = explode(',', $request->sm_tot_wdays_ctrl);
                $sm_no_d_tiff_ctrl = explode(',', $request->sm_no_d_tiff_ctrl);
                $sm_no_d_conv_ctrl = explode(',', $request->sm_no_d_conv_ctrl);
                $sm_no_d_misc_ctrl = explode(',', $request->sm_no_d_misc_ctrl);
                $sm_no_d_other_ctrl = explode(',', $request->sm_no_d_other_ctrl);

                $sm_et_tiffalw_ctrl = explode(',', $request->sm_et_tiffalw_ctrl);
                $sm_e_tiffalw_ctrl = explode(',', $request->sm_e_tiffalw_ctrl);

                $sm_et_convalw_ctrl = explode(',', $request->sm_et_convalw_ctrl);
                $sm_e_conv_ctrl = explode(',', $request->sm_e_conv_ctrl);

                $sm_et_miscalw_ctrl = explode(',', $request->sm_et_miscalw_ctrl);
                $sm_e_miscalw_ctrl = explode(',', $request->sm_e_miscalw_ctrl);
                
                $sm_e_extra_misc_alw_ctrl = explode(',', $request->sm_e_extra_misc_alw_ctrl);
               
                $sm_et_otheralw_ctrl = explode(',', $request->sm_et_otheralw_ctrl);
                $sm_e_otheralw_ctrl = explode(',', $request->sm_e_otheralw_ctrl);

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];

                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $data['total_days'] = $sm_tot_wdays_ctrl[$index];
                    $data['no_days_tiffalw'] = $sm_no_d_tiff_ctrl[$index];
                    $data['no_days_convalw'] = $sm_no_d_conv_ctrl[$index];
                    $data['no_days_miscalw'] = $sm_no_d_misc_ctrl[$index];
                    $data['no_days_otheralw'] = $sm_no_d_other_ctrl[$index];

                    $data['pay_structure_tiff_alw'] = $sm_et_tiffalw_ctrl[$index];
                    $data['pay_structure_conv_alw'] = $sm_et_convalw_ctrl[$index];
                    $data['pay_structure_misc_alw'] = $sm_et_miscalw_ctrl[$index];
                    $data['pay_structure_other_alw'] = $sm_et_otheralw_ctrl[$index];

                    $data['tiffin_alw'] = $sm_e_tiffalw_ctrl[$index];
                    $data['convence_alw'] = $sm_e_conv_ctrl[$index];
                    $data['misc_alw'] = $sm_e_miscalw_ctrl[$index];
                   

                    $data['extra_misc_alw'] = $sm_e_extra_misc_alw_ctrl[$index];
                    $data['other_alw'] = $sm_e_otheralw_ctrl[$index];

                    $data['status'] = 'process';
                    $data['created_at'] = date('Y-m-d');

                    // if ($value == '7050') {

                    //     dd($data);
                    // }

                    $employee_pay_structure = Payroll_detail::where('employee_id', '=', $value)
                        ->where('month_yr', '=', $data['month_yr'])
                        ->first();

                    //dd($employee_pay_structure);

                    // if (!empty($employee_pay_structure)) {
                    //     Session::flash('error', 'Payroll already generated for said period');
                    // } else {

                        $monthlyEmployeeAllowance = MonthlyEmployeeAllowance::where('emp_code', '=', $value)
                            ->where('month_yr', '=', $data['month_yr'])
                            ->first();

                        //dd($monthlyEmployeeAllowance);

                        if (!empty($monthlyEmployeeAllowance)) {
                            
                            MonthlyEmployeeAllowance::where('month_yr',  $sm_month_yr_ctrl[$index])->where('emp_code',$value)->update($data);
                            Session::flash('message', 'Record Successfully updated.');
                            // Session::flash('error', 'Record Already provided for said period for employee - ' . $value);
                        } else {
                            MonthlyEmployeeAllowance::insert($data);
                            // $payupdate = array(
                            //     'i_tax' => $data['itax_amount'],
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

            return redirect('payroll/vw-montly-allowances');
        } else {
            return redirect('/');
        }
    }
    public function UpdateAllowancesAll(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            // dd($request->cboxes);
            $request->status=$request->statusme;

            if (isset($request->deleteme) && $request->deleteme == 'yes') {

                $employee_payroll = Payroll_detail::where('month_yr', '=', $request->deletemy)->get();
                if (count($employee_payroll) > 0) {
                    Session::flash('error', 'Records cannot be deleted as payroll for the month already generated.');
                    return redirect('payroll/vw-montly-allowances');
                }

                MonthlyEmployeeAllowance::where('month_yr', $request->deletemy)->delete();
                Session::flash('message', 'All generated records deleted successfully.');
                return redirect('payroll/vw-montly-allowances');
            }

            $request->empcode_check = explode(',', $request->cboxes);

            if (isset($request->empcode_check) && count($request->empcode_check) != 0) {

                $sm_emp_code_ctrl = explode(',', $request->sm_emp_code_ctrl);
                $sm_emp_name_ctrl = explode(',', $request->sm_emp_name_ctrl);
                $sm_emp_designation_ctrl = explode(',', $request->sm_emp_designation_ctrl);
                $sm_month_yr_ctrl = explode(',', $request->sm_month_yr_ctrl);

                $sm_tot_wdays_ctrl = explode(',', $request->sm_tot_wdays_ctrl);
                $sm_no_d_tiff_ctrl = explode(',', $request->sm_no_d_tiff_ctrl);
                $sm_no_d_conv_ctrl = explode(',', $request->sm_no_d_conv_ctrl);
                $sm_no_d_misc_ctrl = explode(',', $request->sm_no_d_misc_ctrl);
                $sm_no_d_other_ctrl = explode(',', $request->sm_no_d_other_ctrl);

                $sm_et_tiffalw_ctrl = explode(',', $request->sm_et_tiffalw_ctrl);
                $sm_e_tiffalw_ctrl = explode(',', $request->sm_e_tiffalw_ctrl);

                $sm_et_convalw_ctrl = explode(',', $request->sm_et_convalw_ctrl);
                $sm_e_conv_ctrl = explode(',', $request->sm_e_conv_ctrl);

                $sm_et_miscalw_ctrl = explode(',', $request->sm_et_miscalw_ctrl);
                $sm_e_miscalw_ctrl = explode(',', $request->sm_e_miscalw_ctrl);

                $sm_e_extra_misc_alw_ctrl = explode(',', $request->sm_e_extra_misc_alw_ctrl);
                $sm_et_otheralw_ctrl = explode(',', $request->sm_et_otheralw_ctrl);
                $sm_e_otheralw_ctrl = explode(',', $request->sm_e_otheralw_ctrl);

                foreach ($request->empcode_check as $key => $value) {

                    $index = array_search($value, $sm_emp_code_ctrl);

                    $data['emp_code'] = $value;

                    //$data['emp_name'] = $request['emp_name' . $value];
                    // $data['emp_name'] = $sm_emp_name_ctrl[$index];

                    //$data['emp_designation'] = $request['emp_designation' . $value];
                    //$data['emp_designation'] = $sm_emp_designation_ctrl[$index];

                    // $data['month_yr'] = $request['month_yr' . $value];
                    $data['month_yr'] = $sm_month_yr_ctrl[$index];

                    $data['total_days'] = $sm_tot_wdays_ctrl[$index];
                    $data['no_days_tiffalw'] = $sm_no_d_tiff_ctrl[$index];
                    $data['no_days_convalw'] = $sm_no_d_conv_ctrl[$index];
                    $data['no_days_miscalw'] = $sm_no_d_misc_ctrl[$index];
                    $data['no_days_otheralw'] = $sm_no_d_other_ctrl[$index];

                    $data['pay_structure_tiff_alw'] = $sm_et_tiffalw_ctrl[$index];
                    $data['pay_structure_conv_alw'] = $sm_et_convalw_ctrl[$index];
                    $data['pay_structure_misc_alw'] = $sm_et_miscalw_ctrl[$index];
                    $data['pay_structure_other_alw'] = $sm_et_otheralw_ctrl[$index];

                    $data['tiffin_alw'] = $sm_e_tiffalw_ctrl[$index];
                    $data['convence_alw'] = $sm_e_conv_ctrl[$index];
                    $data['misc_alw'] = $sm_e_miscalw_ctrl[$index];
                   

                    $data['extra_misc_alw'] = $sm_e_extra_misc_alw_ctrl[$index];
                    $data['other_alw'] = $sm_e_otheralw_ctrl[$index];
                    
                    $data['status'] = $request->status==''?'process':$request->status;
                    $data['updated_at'] = date('Y-m-d');

                    // dd($data);

                    // $employee_pay_structure = Payroll_detail::where('employee_id', '=', $value)
                    //     ->where('month_yr', '=', $data['month_yr'])
                    //     ->first();

                    $employee_pay_structure = Payroll_detail::join('employee', 'employee.emp_code', '=', 'payroll_details.employee_id')
                        ->select('employee.old_emp_code', 'payroll_details.*')
                        ->where('payroll_details.employee_id', '=', $value)
                        ->where('payroll_details.month_yr', '=', $data['month_yr'])
                        ->first();

                    // if (!empty($employee_pay_structure)) {
                    //     Session::flash('error', 'Payroll already generated for said period against Employee Code - '.$employee_pay_structure->old_emp_code);
                    // } else {

                        MonthlyEmployeeAllowance::where('month_yr', $sm_month_yr_ctrl[$index])->where('emp_code', $value)->update($data);

                        // $payupdate = array(
                        //     'i_tax' => $data['itax_amount'],
                        //     'updated_at' => date('Y-m-d h:i:s'),
                        // );
                        // Employee_pay_structure::where('employee_code', $value)->update($payupdate);
                        Session::flash('message', 'Record Successfully Updated.');

                    // }

                }
            } else {
                Session::flash('error', 'No Record is selected');
            }

            return redirect('payroll/vw-montly-allowances');
        } else {
            return redirect('/');
        }
    }
    public function getMonthlyOvertimes()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['monthlist'] = MonthlyEmployeeOvertime::select('month_yr')->distinct('month_yr')->get();

            $data['result'] = '';
            $payroll_details_rs = '';

            return view('payroll/monthly-overtime', $data);
        } else {
            return redirect('/');
        }
    }
    public function viewMonthlyOvertimes(Request $request)
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            //dd($request->all());

            $payrolldate = explode('/', $request->month);
            //$payroll_date = "0" . ($payrolldate[0] - 2);
            $payroll_date = $payrolldate[0];
            $origDate = $payroll_date . "/" . $payrolldate[1];

            //$current_month_days = cal_days_in_month(CAL_GREGORIAN, $payrolldate[0], $payrolldate[1]);
            //dd($current_month_days);
            $datestring = $payrolldate[1] . '-' . $payrolldate[0] . '-01';
            

            $prevYear=$payrolldate[1];
            $prevMonth=$payrolldate[0];
            if($prevMonth=='01'){
                $prevYear=$payrolldate[1]-1;
                $prevMonth='12';
            }else{
                $prevYear=$payrolldate[1];
                $prevMonth=$payrolldate[0]-1; 
                if($prevMonth<10){
                    $prevMonth=str_pad($prevMonth,2,"0",STR_PAD_LEFT );
                }               
            }
            $datestring_prev = $prevYear . '-' . $prevMonth . '-01';
            
            // Converting string to date
            $date = strtotime($datestring);
            $current_month_days = date("t", strtotime(date("Y-m-t", $date)));

            $datePrev = strtotime($datestring_prev);
            $previous_month_days = date("t", strtotime(date("Y-m-t", $datePrev)));

            //dd($current_month_days . '---'.$previous_month_days);

            $tomonthyr = $payrolldate[1] . "-" . $payroll_date . "-" . $current_month_days;
            $formatmonthyr = $payrolldate[1] . "-" . $payroll_date . "-01";


            $data['monthlist'] = MonthlyEmployeeOvertime::select('month_yr')->distinct('month_yr')->get();

            $data['req_month'] = $request->month;
            $data['current_month_days'] = $current_month_days;
            $data['previous_month_days'] = $previous_month_days;

            $employee_rs = MonthlyEmployeeOvertime::join('employee', 'employee.emp_code', '=', 'monthly_employee_overtimes.emp_code')
                ->select('employee.emp_fname', 'employee.emp_mname', 'employee.emp_lname', 'employee.designation', 'employee.old_emp_code', 'monthly_employee_overtimes.*')
                ->where('monthly_employee_overtimes.month_yr', $request->month)
                ->where('monthly_employee_overtimes.status', 'process')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();

            //dd($employee_rs);

            $result = '';
            if (count($employee_rs) == 0) {
                Session::flash('error', 'Overtime for the month ' . $request->month . ' already processed.');
                return redirect('payroll/vw-montly-overtime');
            }
            foreach ($employee_rs as $mainkey => $emcode) {

                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:50px;" value="' . $emcode->emp_code . '"><input type="hidden" readonly class="form-control sm_curr_mdays" name="curr_mdays' . $emcode->emp_code . '" style="width:100%;" id="curr_mdays_' . $emcode->emp_code . '" value="' . $current_month_days . '"><input type="hidden" readonly class="form-control sm_prev_mdays" name="prev_mdays' . $emcode->emp_code . '" style="width:100%;" id="prev_mdays_' . $emcode->emp_code . '" value="' . $previous_month_days . '"></td>
                                <td>' . $emcode->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100px;" value="' . $emcode->emp_fname . ' ' . $emcode->emp_mname . ' ' . $emcode->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100px;" value="' . $emcode->designation . '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:60px;" value="' . $request->month . '"></td>';

                $result .= '<td><input type="text" style="width:100px;" class="form-control sm_basic" id="basic_' . $emcode->emp_code . '" name="basic' . $emcode->emp_code . '" value="' . $emcode->pay_structure_basic . '"   readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_lm_ot_hrs" id="lm_ot_hrs_' . $emcode->emp_code . '" name="lm_ot_hrs' . $emcode->emp_code . '" value="' . $emcode->last_month_ot_hrs . '" onkeyup="calculate_ot(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_cm_ot_hrs" id="cm_ot_hrs_' . $emcode->emp_code . '" name="cm_ot_hrs' . $emcode->emp_code . '" value="' . $emcode->current_month_ot_hrs . '" onkeyup="calculate_ot(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_lm_ot" id="lm_ot_' . $emcode->emp_code . '" name="lm_ot' . $emcode->emp_code . '" value="' . $emcode->last_month_ot . '" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100px;" class="form-control sm_cm_ot" id="cm_ot_' . $emcode->emp_code . '" name="cm_ot' . $emcode->emp_code . '" value="' . $emcode->curr_month_ot . '" readonly></td>';

                $result .= '<td><input type="number" style="width:100px;" class="form-control sm_e_overtime" name="e_overtime' . $emcode->emp_code . '" step="any" value="' . $emcode->ot_alws . '" id="e_overtime_' . $emcode->emp_code . '" readonly></td>';

            }

            $data['result'] = $result;
            $data['month_yr_new'] = $request->month;
            return view('payroll/monthly-overtime', $data);
        } else {
            return redirect('/');
        }
    }
    public function addMonthlyOvertimesAllemployee()
    {
        $email = Session::get('emp_email');
        if (!empty($email)) {

            $data['result'] = '';

            return view('payroll/add-monthly-overtimes-all', $data);
        } else {
            return redirect('/');
        }
    }
    public function listOvertimesAllemployee(Request $request)
    {

        $email = Session::get('emp_email');
        if (!empty($email)) {

            $payrolldate = explode('/', $request['month_yr']);
            //$payroll_date = "0" . ($payrolldate[0] - 2);
            $payroll_date = $payrolldate[0];
            $origDate = $payroll_date . "/" . $payrolldate[1];

            //$current_month_days = cal_days_in_month(CAL_GREGORIAN, $payrolldate[0], $payrolldate[1]);
            //dd($current_month_days);
            $datestring = $payrolldate[1] . '-' . $payrolldate[0] . '-01';
            

            $prevYear=$payrolldate[1];
            $prevMonth=$payrolldate[0];
            if($prevMonth=='01'){
                $prevYear=$payrolldate[1]-1;
                $prevMonth='12';
            }else{
                $prevYear=$payrolldate[1];
                $prevMonth=$payrolldate[0]-1; 
                if($prevMonth<10){
                    $prevMonth=str_pad($prevMonth,2,"0",STR_PAD_LEFT );
                }               
            }
            $datestring_prev = $prevYear . '-' . $prevMonth . '-01';
            
            // Converting string to date
            $date = strtotime($datestring);
            $current_month_days = date("t", strtotime(date("Y-m-t", $date)));

            $datePrev = strtotime($datestring_prev);
            $previous_month_days = date("t", strtotime(date("Y-m-t", $datePrev)));

            //dd($current_month_days . '---'.$previous_month_days);

            $tomonthyr = $payrolldate[1] . "-" . $payroll_date . "-" . $current_month_days;
            $formatmonthyr = $payrolldate[1] . "-" . $payroll_date . "-01";

            $rate_rs = Rate_master::leftJoin('rate_details', 'rate_details.rate_id', '=', 'rate_masters.id')
                ->select('rate_details.*', 'rate_masters.head_name')
                ->get();

            $result = '';


            $emplist = Process_attendance::join('employee', 'employee.emp_code', '=', 'process_attendances.employee_code')
                ->select('employee.*', 'process_attendances.*')
                ->where('process_attendances.month_yr', '=', $request['month_yr'])
                // ->where('process_attendances.status', '=', 'A')
               // ->where('employee.emp_status', '!=', 'TEMPORARY')
                ->where('employee.emp_status', '!=', 'EX-EMPLOYEE')
                ->where('employee.emp_status', '!=', 'EX- EMPLOYEE')
                ->where('employee.status', '=', 'active')
            // ->where('employee.emp_code', '=', '1831')
                ->orderByRaw('cast(employee.old_emp_code as unsigned)', 'asc')
                ->get();

            if (count($emplist) == 0) {
                Session::flash('error', 'Please process the attandance for the month before generating overtimes.');
                return redirect('payroll/add-montly-overtimes');
            }

            foreach ($emplist as $mainkey => $emcode) {

                $process_payroll = $this->getEmpPayroll($emcode->emp_code, $payrolldate[0], $payrolldate[1]);
                $process_payroll = json_decode($process_payroll);

                //dd($process_payroll);

                $employee_rs = Employee::leftJoin('employee_pay_structures', 'employee_pay_structures.employee_code', '=', 'employee.emp_code')
                    ->where('employee.emp_code', '=', $emcode->emp_code)
                    ->select('employee.*', 'employee_pay_structures.*')
                    ->first();

                $e_overtime = 0;
                $e_overtime_show = '';

                $calculate_basic_salary = $employee_rs->basic_pay;

                



                // $cal_tiff_alw = $e_tiffalw;
                // $tot_wdays = $emcode->no_of_present + $emcode->no_of_days_leave_taken + $emcode->no_of_days_absent;

                // $perday_tiffalw = $e_tiffalw / $tot_wdays;
                // $cal_tiff_alw = round(($perday_tiffalw * $emcode->no_of_present), 2);

                // $cal_conv_alw = $e_conv;
                // $perday_convalw = $e_conv / $tot_wdays;
                // $cal_conv_alw = round(($perday_convalw * $emcode->no_of_present), 2);

                // $cal_misc_alw = $e_miscalw;
                // $perday_miscalw = $e_miscalw / $tot_wdays;
                // $cal_misc_alw = round(($perday_miscalw * $emcode->no_of_present), 2);

                //dd($current_month_days . '---'.$previous_month_days);


                $result .= '<tr id="' . $emcode->emp_code . '">
								<td><div class="checkbox"><label><input type="checkbox" name="empcode_check[]" id="chk_' . $emcode->emp_code . '" value="' . $emcode->emp_code . '" class="checkhour"></label></div></td>
								<td><input type="text" readonly class="form-control sm_emp_code" name="emp_code' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_code . '"><input type="hidden" readonly class="form-control sm_curr_mdays" name="curr_mdays' . $emcode->emp_code . '" style="width:100%;" id="curr_mdays_' . $emcode->emp_code . '" value="' . $current_month_days . '"><input type="hidden" readonly class="form-control sm_prev_mdays" name="prev_mdays' . $emcode->emp_code . '" style="width:100%;" id="prev_mdays_' . $emcode->emp_code . '" value="' . $previous_month_days . '"></td>
                                <td>' . $employee_rs->old_emp_code . '</td>
								<td><input type="text" readonly class="form-control sm_emp_name" name="emp_name' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->emp_fname . ' ' . $employee_rs->emp_mname . ' ' . $employee_rs->emp_lname . '"></td>
								<td><input type="text" readonly class="form-control sm_emp_designation" name="emp_designation' . $emcode->emp_code . '" style="width:100%;" value="' . $employee_rs->designation . '"></td>
								<td><input type="text" readonly class="form-control sm_month_yr" name="month_yr' . $emcode->emp_code . '" style="width:100%;" value="' . $request['month_yr'] . '"></td>';

                $result .= '<td><input type="text" style="width:100%;" class="form-control sm_basic" id="basic_' . $emcode->emp_code . '" name="basic' . $emcode->emp_code . '" value="' . $calculate_basic_salary . '"  onkeyup="calculate_days(' . $emcode->emp_code . ');" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100%;" class="form-control sm_lm_ot_hrs" id="lm_ot_hrs_' . $emcode->emp_code . '" name="lm_ot_hrs' . $emcode->emp_code . '" value="0" onkeyup="calculate_ot(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100%;" class="form-control sm_cm_ot_hrs" id="cm_ot_hrs_' . $emcode->emp_code . '" name="cm_ot_hrs' . $emcode->emp_code . '" value="0" onkeyup="calculate_ot(' . $emcode->emp_code . ');"></td>';

                $result .= '<td><input type="number" step="any" style="width:100%;" class="form-control sm_lm_ot" id="lm_ot_' . $emcode->emp_code . '" name="lm_ot' . $emcode->emp_code . '" value="0" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100%;" class="form-control sm_cm_ot" id="cm_ot_' . $emcode->emp_code . '" name="cm_ot' . $emcode->emp_code . '" value="0" readonly></td>';

                $result .= '<td><input type="number" step="any" style="width:100%;" class="form-control sm_e_overtime" name="e_overtime' . $emcode->emp_code . '" value="0" id="e_overtime_' . $emcode->emp_code . '" readonly></td>';

                
                
                // print_r($result);
                // die();
            }
            // print_r($result);
            // die();
            
            $month_yr_new = $request['month_yr'];
            return view('payroll/add-monthly-overtimes-all', compact('result', 'month_yr_new','current_month_days','previous_month_days'));
        } else {
            return redirect('/');
        }
    }


}
