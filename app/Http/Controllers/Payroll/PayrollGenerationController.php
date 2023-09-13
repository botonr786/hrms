<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class PayrollGenerationController extends Controller
{
    public function payrollDashboard(Request $request)
    {
        if (!empty(Session::get('admin'))) {
            $email = Session::get('adminusernmae');

            return View('payroll/dashboard');
        } else {
            return redirect('/');
        }
    }
}
