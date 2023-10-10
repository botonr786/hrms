<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use Session;
use Validator;
use view;
use App\Models\UserModel;

class LandingController extends Controller
{
    public function index()
    {
        return view("index");
    }
    public function indexloginpay()
    {
        return view("index-pay");
    }
    public function indexser()
    {
        return view("user-login");
    }
    public function indexfor()
    {
        return view("forgot-password");
    }

    public function register()
    {
        // $data = array('to_name' => 'Subho', 'body_content' => 'This is mail testing local');

        // $toemail = 'm.subhasish@gmail.com';
        // Mail::send('mailsmcommon', $data, function ($message) use ($toemail) {
        //     $message->to($toemail, 'Workpermitcloud')->subject
        //         ('Organisation   Details');
        //     $message->from('noreply@workpermitcloud.co.uk', 'Workpermitcloud');
        // });
        return view("register");
    }

    public function employerdashboard()
    {
        return view("employer-dashboard");
    }

    public function Doregister(Request $request)
    {
        //dd($_SERVER);
        // if ($_SERVER['HTTP_HOST'] != 'workpermitcloud.co.uk') {
        //     Session::flash('message', 'Invalid Referrer');
        //     return redirect('register');
        // }

        // if ($_SERVER['HTTP_REFERER'] != 'http://localhost/hrms/register' || $_SERVER['HTTP_REFERER'] != 'https://workpermitcloud.co.uk/hrms/register') {
        //     die('don\'t be an jerk, ruin your own site');
        // }

        $eml = [];
        if ($request->email != "") {
            $eml = explode(".", $request->email);
            //dd($eml);
        }
        if (count($eml) > 4) {
            Session::flash("message", "Invalid input");
            return redirect("register");
        }
        if ($request->p_no != "" && substr($request->p_no, 0, 2) == "83") {
            // case-insensitive here
            Session::flash("message", "Invalid input");
            return redirect("register");
        }
        //dd(stripos("83", $request->p_no));
        if ($request->pass != $request->con_password) {
            Session::flash(
                "message",
                "Password and Confirm Password both not same"
            );
            return redirect("register")->withInput();
        } else {
            $Employee = DB::table("users")
                ->where("email", "=", $request->email)
                ->first();

            if (empty($Employee)) {
                $employee_pid = DB::table("registration")

                    ->orderBy("id", "DESC")

                    ->first();
                if (empty($employee_pid)) {
                    $pid = "EM1";
                } else {
                    $pid = "EM" . ($employee_pid->id + 1);
                }

                $datareg = [
                    "com_name" => $request->com_name,
                    "f_name" => $request->f_name,

                    "l_name" => $request->l_name,
                    "reg" => $pid,
                    "email" => $request->email,
                    "organ_email" => $request->email,

                    "status" => "active",
                    "verify" => "not approved",
                    "licence" => "no",

                    "p_no" => $request->p_no,
                    "pass" => $request->pass,
                    "created_at" => date("Y-m-d h:i:s"),
                ];
                DB::table("registration")->insert($datareg);

                $datauser = [
                    "name" => $request->com_name,
                    "user_type" => "employer",

                    "status" => "active",
                    "employee_id" => $pid,
                    "email" => $request->email,

                    "updated_at" => date("Y-m-d h:i:s"),
                    "created_at" => date("Y-m-d h:i:s"),
                    "password" => $request->pass,
                ];
                DB::table("users")->insert($datauser);

                $le_type = DB::table("le_type")->get();

                foreach ($le_type as $value_le) {
                    $datauserleave = [
                        "leave_type_name" => $value_le->leave_type_name,
                        "alies" => $value_le->alies,

                        "remarks" => $value_le->remarks,
                        "emid" => $pid,
                        "leave_type_status" => "active",
                    ];
                    DB::table("leave_type")->insert($datauserleave);
                }

                $em_type = DB::table("em_type")->get();

                foreach ($em_type as $value_em) {
                    $datauseremployty = [
                        "employee_type_name" => $value_em->name,

                        "emid" => $pid,
                        "employee_type_status" => "Active",
                    ];
                    DB::table("employee_type")->insert($datauseremployty);
                }

                $data = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "desig" => $request->desig,
                ];

                $toemail = "soumya.dev23@gmail.com";
                //$toemail = 'info@workpermitcloud.co.uk';
                //$toemail = 'm.subhasish@gmail.com';
                Mail::send("mailre", $data, function ($message) use ($toemail) {
                    $message
                        ->to($toemail, "EITClimbr")
                        ->subject("New Organisation Registered");
                    $message->from("noreply@eitclimbr.in", "Climbr");
                });

                $datamail = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "desig" => $request->desig,
                ];
                $toemail = "debarati@eitpl.in";
                Mail::send("mailre", $datamail, function ($message) use (
                    $toemail
                ) {
                    $message
                        ->to($toemail, "EITClimbr")
                        ->subject("Organisation Details");
                    $message->from("noreply@eitclimbr.in", "Climbr");
                });

                $data = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "pass" => $request->pass,
                ];
                $toemail = $request->email;
                Mail::send("mailor", $data, function ($message) use ($toemail) {
                    $message
                        ->to($toemail, "EITClimbr")
                        ->subject(
                            "Welcome to climber Cloud HR Management System"
                        );
                    $message->from("noreply@eitclimbr.in", "Climbr");
                });

                Session::flash(
                    "message",
                    "Thank you for registration.Please check your email for next steps."
                );

                return redirect("register");
            } else {
                Session::flash("message", "Email id already exits.");
                return redirect("register");
            }
        }
        //  @if(auth()->check())
        //auth()->user()->name
    }

    public function Dashboard(Request $request)
    {
        
        if ($request->input("emp_email")) {
            $Employee1 = DB::table("users")
                ->where(
                    "email",
                    "=",
                    base64_decode($request->input("emp_email"))
                )
                ->where("user_type", "!=", "admin")
                ->where("status", "=", "active")
                ->first();

            Session::put("emp_email", $Employee1->email);
            Session::put("emp_pass", $Employee1->password);
            Session::put("user_type", $Employee1->user_type);
            Session::put("users_id", $Employee1->id);
        }

        $email = Session::get("emp_email");

        if (!empty($email)) {
            $user_type = Session::get("user_type");

            if ($user_type == "employer") {
                $data["Roledata"] = DB::table("registration")
                    ->where("status", "=", "active")
                    ->where("email", "=", $email)
                    ->first();
            } else {
             
                $usemail = Session::get("user_email");
                $users_id = Session::get("users_id");

                $dtaem = DB::table("users")

                    ->where("id", "=", $users_id)
                    ->first();
                $data["Roledata"] = DB::table("employee")
                    ->where("emid", "=", $dtaem->emid)
                    ->where("emp_code", "=", $dtaem->employee_id)
                    ->first();
               
                $data["Roles_auth"] = DB::table("role_authorization")
                    ->where("emid", "=", $dtaem->emid)
                    ->where("member_id", "=", $dtaem->email)
                    ->get()
                    ->toArray();
                    // dd($data["Roles_auth"]);
            }
            //dd($data);
            return View("employer-dashboard", $data);
        } else {
            return redirect("/");
        }
    }

    public function Doforgot(Request $request)
    {
        $Employee = DB::table("users")
            ->where("email", "=", $request->email)
            ->where("status", "=", "active")
            ->where("user_type", "!=", "admin")
            ->first();

        if (!empty($Employee)) {
            $Roledata = DB::table("users")

                ->where("employee_id", "=", $Employee->emid)
                ->where("status", "=", "active")
                ->first();

            $data = ["pass" => $Employee->password, "name" => $Employee->name];
            $toemail = $request->email;
            Mail::send("mailforgot", $data, function ($message) use ($toemail) {
                $message
                    ->to($toemail, "Workpermitcloud")
                    ->subject("Forgot  Password ");
                $message->from(
                    "noreply@workpermitcloud.co.uk",
                    "Workpermitcloud"
                );
            });

            Session::flash("message", "Mail sent successfully.");
            return redirect("forgot-password");
        } else {
            Session::flash("message", "Your email id was wrong!!");
            return redirect("forgot-password");
        }
    }
    public function DoLogin(Request $request)
    {
        // dd($request->email);

        $validator = Validator::make(
            $request->all(),
            [
                "email" => "required",
                "psw" => "required",
            ],
            [
                "email.required" => "Email Required",
                "psw.required" => "Password Required",
            ]
        );

        if ($validator->fails()) {
            return redirect("/")
                ->withErrors($validator)
                ->withInput();
        } else {
            $Employee = UserModel::where("email", "=", $request->email)
                ->where("password", "=", $request->psw)
                ->where("status", "=", "active")
                ->where("user_type", "!=", "admin")
                ->first();
            // dd($Employee);
            if (!empty($Employee)) {
                
                if ($Employee->user_type == "employer") {
                    //emid=$Employee->employee_id
                    //check for subscription
                    $subsCnt = DB::Table("subscriptions")
                        ->where("emid", "=", $Employee->employee_id)
                        ->count();

                    if ($subsCnt > 0) {
                        $subscription = DB::Table("subscriptions")
                            ->where("emid", "=", $Employee->employee_id)
                            ->where("expiry_date", ">=", date("Y-m-d"))
                            ->where("status", "=", "active")
                            ->first();

                        if (!empty($subscription)) {
                            Session::put("employee_id", $request->employee_id);
                            Session::put("emp_email", $request->email);
                            Session::put("user_type", $Employee->user_type);
                            Session::put("users_id", $Employee->id);
                            Session::put("emp_pass", $request->psw);
                        } else {
                            Session::flash(
                                "message",
                                "Your subscription plan expired!! Renew to continue."
                            );
                            return redirect("/");
                        }
                    } else {
                      
                       Session::put("employee_id", $request->employee_id);
                        Session::put("emp_email", $request->email);
                        Session::put("user_type", $Employee->user_type);
                        Session::put("users_id", $Employee->id);
                        Session::put("emp_pass", $request->psw);
                    }
                } else {
                    //emid=$Employee->emid
                    //check for subscription
                    $subsCnt = DB::Table("subscriptions")
                        ->where("emid", "=", $Employee->emid)
                        ->count();

                    if ($subsCnt > 0) {
                        $subscription = DB::Table("subscriptions")
                            ->where("emid", "=", $Employee->emid)
                            ->where("expiry_date", ">=", date("Y-m-d"))
                            ->where("status", "=", "active")
                            ->first();

                        if (!empty($subscription)) {
                            $Roledata = DB::table("users")
                                ->where("employee_id", "=", $Employee->emid)
                                ->where("status", "=", "active")
                                ->first();
                            if (!empty($Roledata)) {
                                Session::put("employee_id", $request->employee_id);
                                Session::put("emp_email", $Roledata->email);
                                Session::put("user_email", $request->email);
                                Session::put("user_type", $Employee->user_type);
                                Session::put("users_id", $Employee->id);
                                Session::put("emp_pass", $request->psw);
                            } else {
                                Session::flash(
                                    "message",
                                    "You are not active!!"
                                );
                                return redirect("/");
                            }
                        } else {
                            Session::flash(
                                "message",
                                "Your organisation subscription plan expired!! Try after renewal."
                            );
                            return redirect("/");
                        }
                    } else {
                        $Roledata = DB::table("users")
                            ->where("employee_id", "=", $Employee->emid)
                            ->where("status", "=", "active")
                            ->first();
                            // dd($Roledata);
                        if (!empty($Roledata)) {
                            Session::put("employee_id", $request->employee_id);
                            Session::put("emp_email", $Roledata->email);
                            Session::put("user_email", $request->email);
                            Session::put("user_type", $Employee->user_type);
                            Session::put("users_id", $Employee->id);
                            Session::put("emp_pass", $request->psw);
                        } else {
                            Session::flash("message", "You are not active!!");
                            return redirect("/");
                        }
                    }
                }

                return redirect()->intended("employerdashboard");
            } else {
                Session::flash("message", "Your email or password was wrong!!");
                return redirect("/");
            }
        }
        //  @if(auth()->check())
        //auth()->user()->name
    }
    public function Logout(Request $request)
    {
        Session::forget("users_id");
        Session::forget("user_type");
        Session::forget("emp_pass");
        Session::forget("emp_email");
        Session::forget("admin_userpp_email");
        Session::forget("admin_userpp_member");
        Session::forget("admin_userp_user_type");
        Session::flash("message", "You are successfully Logout.");
        return redirect("/");
    }

    public function DoLoginuser(Request $request)
    {
        
        $Employee = DB::table("users_admin_emp")
            ->where("login_id", "=", $request->email)
            ->where("password", "=", $request->psw)
            ->where("status", "=", "active")
            ->where("user_type", "=", "user")
            ->first();
        // dd($Employee);
        if (!empty($Employee)) {
            Session::put("admin_userpp_email", $Employee->login_id);
            Session::put("admin_userpp_member", $Employee->employee_id);
            Session::put("admin_userp_user_type", $Employee->user_type);

            return redirect("user-check-organisation");
        } else {
            Session::flash("message", "Your Login Id Or password was wrong!!");
            return redirect("user-login");
        }
    }

    public function indexserorganisation()
    {
        if (!empty(Session::get("admin_userpp_email"))) {
            $member = Session::get("admin_userpp_member");

            $data["users"] = DB::table("role_authorization_admin_organ")
                ->where("member_id", "=", $member)
                ->where("status", "=", "active")
                ->get();

            return view("organisation-list", $data);
        } else {
            return redirect("user-login");
        }
    }

    public function DoLoginorganisation(Request $request)
    {
        if (!empty(Session::get("admin_userpp_email"))) {
            $member = Session::get("admin_userpp_member");

            $Employee = DB::table("users")
                ->where("employee_id", "=", $request->com)
                ->where("status", "=", "active")
                ->where("user_type", "=", "employer")
                ->first();

            //dd($request->com);

            if (!empty($Employee)) {
                Session::put("emp_email", $Employee->email);
                Session::put("user_type", $Employee->user_type);
                Session::put("users_id", $Employee->id);
                Session::put("emp_pass", $Employee->password);
                return redirect()->intended("employerdashboard");
            } else {
                Session::flash("message", "Organisation Is not active!!");
                return redirect("user-login");
            }
        } else {
            return redirect("user-login");
        }
    }

    public function Logoutusert(Request $request)
    {
        Session::forget("users_id");
        Session::forget("admin_userpp_email");
        Session::forget("admin_userpp_member");
        Session::forget("admin_userp_user_type");
        Session::forget("user_type");
        Session::forget("emp_pass");
        Session::forget("emp_email");
        Session::flash("message", "You are successfully Logout.");
        return redirect("user-login");
    }

    public function DoLoginpay(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "email" => "required",
                "psw" => "required",
            ],
            [
                "email.required" => "Email Required",
                "psw.required" => "Password Required",
            ]
        );

        if ($validator->fails()) {
            return redirect("/")
                ->withErrors($validator)
                ->withInput();
        } else {
            $Employee = DB::table("users")
                ->where("email", "=", $request->email)
                ->where("password", "=", $request->psw)
                ->where("status", "=", "active")
                ->where("user_type", "!=", "admin")
                ->first();

            if (!empty($Employee)) {
                if ($Employee->user_type == "employer") {
                    Session::put("emp_email", $request->email);
                    Session::put("user_type", $Employee->user_type);
                    Session::put("users_id", $Employee->id);
                    Session::put("emp_pass", $request->psw);
                    return redirect("billingorganization/billinglist");
                } else {
                    Session::flash("message", "You are not Organisation!!");
                    return redirect("login-pay");
                }
            } else {
                Session::flash("message", "Your email or password was wrong!!");
                return redirect("login-pay");
            }
        }
        //  @if(auth()->check())
        //auth()->user()->name
    }

    public function payregister()
    {
        return view("register-pay");
    }

    public function Dopayregister(Request $request)
    {
        if ($request->pass != $request->con_password) {
            Session::flash(
                "message",
                "Password and Confirm Password both not same"
            );
            return redirect("register");
        } else {
            $Employee = DB::table("users")
                ->where("email", "=", $request->email)
                ->first();

            if (empty($Employee)) {
                $employee_pid = DB::table("registration")

                    ->orderBy("id", "DESC")

                    ->first();
                if (empty($employee_pid)) {
                    $pid = "EM1";
                } else {
                    $pid = "EM" . ($employee_pid->id + 1);
                }

                $datareg = [
                    "com_name" => $request->com_name,
                    "f_name" => $request->f_name,

                    "l_name" => $request->l_name,
                    "reg" => $pid,
                    "email" => $request->email,
                    "organ_email" => $request->email,

                    "status" => "active",
                    "verify" => "not approved",
                    "licence" => "no",

                    "p_no" => $request->p_no,
                    "pass" => $request->pass,
                    "created_at" => date("Y-m-d"),
                ];
                DB::table("registration")->insert($datareg);

                $datauser = [
                    "name" => $request->com_name,
                    "user_type" => "employer",

                    "status" => "active",
                    "employee_id" => $pid,
                    "email" => $request->email,

                    "updated_at" => date("Y-m-d h:i:s"),
                    "created_at" => date("Y-m-d h:i:s"),
                    "password" => $request->pass,
                ];
                DB::table("users")->insert($datauser);

                $le_type = DB::table("le_type")->get();

                foreach ($le_type as $value_le) {
                    $datauserleave = [
                        "leave_type_name" => $value_le->leave_type_name,
                        "alies" => $value_le->alies,

                        "remarks" => $value_le->remarks,
                        "emid" => $pid,
                        "leave_type_status" => "active",
                    ];
                    DB::table("leave_type")->insert($datauserleave);
                }

                $em_type = DB::table("em_type")->get();

                foreach ($em_type as $value_em) {
                    $datauseremployty = [
                        "employee_type_name" => $value_em->name,

                        "emid" => $pid,
                        "employee_type_status" => "Active",
                    ];
                    DB::table("employee_type")->insert($datauseremployty);
                }

                $data = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "desig" => $request->desig,
                ];
                $toemail = "info@workpermitcloud.co.uk";
                Mail::send("mailre", $data, function ($message) use ($toemail) {
                    $message
                        ->to($toemail, "Workpermitcloud")
                        ->subject("Organisation   Details");
                    $message->from(
                        "noreply@workpermitcloud.co.uk",
                        "Workpermitcloud"
                    );
                });

                $datamail = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "desig" => $request->desig,
                ];
                $toemail = "admin@workpermitcloud.co.uk";
                Mail::send("mailre", $datamail, function ($message) use (
                    $toemail
                ) {
                    $message
                        ->to($toemail, "Workpermitcloud")
                        ->subject("Organisation   Details");
                    $message->from(
                        "noreply@workpermitcloud.co.uk",
                        "Workpermitcloud"
                    );
                });

                $data = [
                    "f_name" => $request->f_name,
                    "l_name" => $request->l_name,
                    "com_name" => $request->com_name,
                    "p_no" => $request->p_no,
                    "email" => $request->email,
                    "pass" => $request->pass,
                ];
                $toemail = $request->email;
                Mail::send("mailor", $data, function ($message) use ($toemail) {
                    $message
                        ->to($toemail, "Workpermitcloud")
                        ->subject(
                            "Welcome to Work Permit Cloud HR Management System"
                        );
                    $message->from(
                        "noreply@workpermitcloud.co.uk",
                        "Workpermitcloud"
                    );
                });

                Session::flash(
                    "message",
                    "Thank you for registration.Please check your email for next steps."
                );

                return redirect("login-pay-register");
            } else {
                Session::flash("message", "Email id already exits.");
                return redirect("login-pay-register");
            }
        }
        //  @if(auth()->check())
        //auth()->user()->name
    }

    public function indexpayfor()
    {
        return view("forgot-password-pay");
    }

    public function Dopayforgot(Request $request)
    {
        $Employee = DB::table("users")
            ->where("email", "=", $request->email)
            ->where("status", "=", "active")
            ->where("user_type", "!=", "admin")
            ->first();

        if (!empty($Employee)) {
            $Roledata = DB::table("users")

                ->where("employee_id", "=", $Employee->emid)
                ->where("status", "=", "active")
                ->first();

            $data = ["pass" => $Employee->password, "name" => $Employee->name];
            $toemail = $request->email;
            Mail::send("mailforgot", $data, function ($message) use ($toemail) {
                $message
                    ->to($toemail, "Workpermitcloud")
                    ->subject("Forgot  Password ");
                $message->from(
                    "noreply@workpermitcloud.co.uk",
                    "Workpermitcloud"
                );
            });

            Session::flash("message", "Mail sent successfully.");
            return redirect("login-pay-forgot-password");
        } else {
            Session::flash("message", "Your email id was wrong!!");
            return redirect("login-pay-forgot-password");
        }
    }
}
