<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStudent;
use App\Models\Expense;
use App\Models\Income;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamRoutine;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolPayment;
use App\Models\SchoolSubscription;
use App\Models\SmsPackage;
use App\Models\Teacher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // -----------------------------
    // Admin Dashboard & Menu Pages
    // -----------------------------

    public function admin()
    {
        // Total schools
        $totalSchools = School::count();

        // Pending schools (approval_status = pending)
        $pendingSchools = School::where('approval_status', 'pending')->count();

        // Total SMS packages
        $smsPackages = SmsPackage::count();

        return view('admin.dashboard', compact('totalSchools', 'pendingSchools', 'smsPackages'));
    }


    public function approvalSchools()
    {
        return view('admin.approval-schools');
    }

    public function registeredSchools()
    {
        return view('admin.registered-schools');
    }

    public function createPlan()
    {
        return view('admin.create-plan');
    }

    public function smsPackages()
    {
        return view('admin.sms-packages');
    }

    public function smsPackageActivationRequests()
    {
        return view('admin.sms-package-activation-requests');
    }

    public function dynamicOperation()
    {
        return view('admin.dynamic-operation');
    }

    public function subscriptions()
    {
        return view('admin.subscriptions');
    }

    // -----------------------------
    // School Dashboard & Menu Pages
    // -----------------------------
    public function school()
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }

        $schoolId = $school->user_id;

        // --- Existing Card Logic ---
        $classes = SchoolClass::where('school_id', $school->id)->count();
        $teachersCount = Teacher::where('school_id', $school->id)->count();
        $studentsCount = AdmissionStudent::where('school_id', $schoolId)->count();
        //dd($schoolId);
        $studentsActive = AdmissionStudent::where('school_id', $schoolId)->where('status', 'approved')->count();
        $studentsInactive = $studentsCount - $studentsActive;

        // --- Discount-aware fee totals ---
        // Total fees = sum of fee amounts, but use after_discount where a discount exists for that student+fee
        $rawFeeTypes = \App\Models\SchoolFeeType::where('school_id', $school->id)
            ->get(['id', 'student_id', 'fee_type_name', 'fee_name', 'amount']);

        // Load all discounts for this school keyed by student_id|fee_name
        $discounts = \Illuminate\Support\Facades\DB::table('school_fee_discounts')
            ->where('school_id', $school->id)
            ->get(['student_id', 'fee_name', 'after_discount']);

        $discountMap = [];
        foreach ($discounts as $d) {
            $discountMap[$d->student_id . '|' . $d->fee_name] = (float) $d->after_discount;
        }

        $totalFees = 0;
        foreach ($rawFeeTypes as $fee) {
            $key = $fee->student_id . '|' . $fee->fee_name;
            if ($fee->student_id && isset($discountMap[$key])) {
                $totalFees += $discountMap[$key];
            } else {
                $totalFees += (float) $fee->amount;
            }
        }

        $totalCollection = SchoolPayment::where('school_id', $school->id)->sum('type_amount');
        $totalDue        = max($totalFees - $totalCollection, 0);
        $totalIncome     = Income::where('school_id', $school->id)->sum('amount');
        $totalExpense    = Expense::where('school_id', $school->id)->sum('amount');

        // --- Dynamic Chart Logic (Fixed to start from January) ---
        $months = collect();
        $monthlyPayments = collect();
        $monthlyDues = collect();
        $monthlyIncomes = collect();
        $monthlyExpenses = collect();

        $currentMonthNumber = now()->month;

        for ($m = 1; $m <= $currentMonthNumber; $m++) {
            $date = Carbon::create(now()->year, $m, 1);
            $months->push($date->format('M'));

            // Payments vs Due
            $monthlyPayments->push(SchoolPayment::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('type_amount'));

            $monthlyDues->push(SchoolPayment::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('payable_due'));

            // Income vs Expense Trend
            $monthlyIncomes->push(Income::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('amount'));

            $monthlyExpenses->push(Expense::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('amount'));
        }

        // Profit vs Loss Calculation
        $net = $totalIncome + $totalExpense;
        $profitPercent = $net > 0 ? round(($totalIncome / $net) * 100) : 50;
        $lossPercent = $net > 0 ? round(($totalExpense / $net) * 100) : 50;

        // Bank vs Cash Calculation
        $bankTotal = SchoolPayment::where('school_id', $schoolId)->where('pay_method', 'Bank')->sum('type_amount');
        $cashTotal = SchoolPayment::where('school_id', $schoolId)->where('pay_method', 'Cash')->sum('type_amount');

        // --- Table Variables ---
        $subscription = SchoolSubscription::where('school_id', $schoolId)->with('package')->where('status', 'active')->latest()->first();
        $teachersList = Teacher::where('school_id', $schoolId)->latest()->take(5)->get();
        $topClasses = SchoolExamSchedule::where('school_id', $schoolId)->latest()->take(5)->get();
        $dueList = SchoolPayment::where('school_id', $schoolId)->with(['student.schoolClass'])->where('total_due', '>', 0)->latest()->take(5)->get();
        $upcomingExams = SchoolExamRoutine::where('school_id', $schoolId)->where('exam_date', '>=', now()->toDateString())->orderBy('exam_date', 'asc')->take(5)->get();

        return view('school.dashboard', compact(
            'classes',
            'teachersCount',
            'studentsCount',
            'studentsActive',
            'studentsInactive',
            'totalFees',
            'totalCollection',
            'totalDue',
            'totalIncome',
            'totalExpense',
            'subscription',
            'teachersList',
            'topClasses',
            'dueList',
            'upcomingExams',
            'months',
            'monthlyPayments',
            'monthlyDues',
            'monthlyIncomes',
            'monthlyExpenses',
            'profitPercent',
            'lossPercent',
            'bankTotal',
            'cashTotal'
        ));
    }

    // Teacher Pages
    public function teacherRegistration()
    {
        return view('school.teacher-registration');
    }

    public function SchoolclassPermission()
    {
        return view('school.class-permission');
    }

    // Student Pages
    public function studentAdmission()
    {
        return view('school.student-admission');
    }
    public function studentLists()
    {
        return view('school.student-lists');
    }


    // Academic Pages
    public function classes()
    {
        return view('school.classes');
    }
    public function groups()
    {
        return view('school.groups');
    }
    public function sections()
    {
        return view('school.sections');
    }
    public function sessions()
    {
        return view('school.sessions');
    }
    public function subjects()
    {
        return view('school.subjects');
    }
    public function syllabus()
    {
        return view('school.syllabus');
    }

    public function classRoutine()
    {
        return view('school.class-routine');
    }

    // Guardian 
    public function guardians()
    {
        return view('school.guardians');
    }

    // Finance Pages
    public function income()
    {
        return view('school.income');
    }
    public function membership()
    {
        return view('school.membership');
    }
    public function expense()
    {
        return view('school.expense');
    }

    // Subscription / Plans
    public function currentPlan()
    {
        return view('school.current-plan');
    }
    public function smsPackage()
    {
        return view('school.sms-package');
    }


    // Fees Pages
    public function feesType()
    {
        return view('school.fees_type');
    }
    public function discount()
    {
        return view('school.discount');
    }
    public function payment()
    {
        return view('school.payment');
    }
    public function dueList()
    {
        return view('school.due-list');
    }



    // Employee Pages
    public function employee()
    {
        return view('school.employee');
    }
    public function payroll()
    {
        return view('school.payroll');
    }


    // Notice Pages
    public function announcement()
    {
        return view('school.announcement');
    }
    public function createHoliday()
    {
        return view('school.create_holiday');
    }


    // ================= Exam =================
    public function examName()
    {
        return view('school.exam.exam_name');
    }

    public function examRoutine()
    {
        return view('school.exam.exam_routine');
    }

    public function grade()
    {
        return view('school.exam.grade');
    }

    public function admitCard()
    {
        return view('school.exam.admit_card');
    }

    public function seatPlan()
    {
        return view('school.exam.seat_plan');
    }

    public function markSubmit()
    {
        return view('school.exam.mark_submit');
    }

    public function schedule()
    {
        return view('school.exam.schedule');
    }

    public function resultFind()
    {
        return view('school.exam.result_find');
    }


    // ================= Inventory =================

    public function product()
    {
        return view('school.inventory.product');
    }

    public function purchase()
    {
        return view('school.inventory.purchase');
    }

    public function return()
    {
        return view('school.inventory.return');
    }

    public function duePaid()
    {
        return view('school.inventory.due_paid');
    }

    public function profitLoss()
    {
        return view('school.inventory.profit_loss');
    }

    public function addPayment()
    {
        return view('school.inventory.add_payment');
    }


    // ================= Role =================

    public function rolePermission()
    {
        return view('school.role.role_permission');
    }


    // -----------------------------
    // Teacher Dashboard & Menu Pages
    // -----------------------------

    public function teacher()
    {

        $students = 0;
        $assignments = 0;
        $classes = 0;

        return view('teacher.dashboard', compact(
            'students',
            'assignments',
            'classes'
        ));
    }

    public function teacherList()
    {
        return view('teacher.teacher-list');
    }

    public function classPermission()
    {
        return view('teacher.class-permission');
    }

    public function teacherAssignment()
    {
        return view('teacher.assignment');
    }

    public function teacherStudentList()
    {
        return view('teacher.student-list');
    }

    public function teacherClassTime()
    {
        return view('teacher.class-time');
    }

    // -----------------------------
    // Student Dashboard & Menu Pages
    // -----------------------------

    public function student()
    {
        $subjects   = 6;
        $attendance = 95;

        return view('student.dashboard', compact('subjects', 'attendance'));
    }

    public function studentTeacherList()
    {
        return view('student.teacher-list');
    }

    public function studentList()
    {
        return view('student.student-list');
    }

    public function classTime()
    {
        return view('student.class-time');
    }

    public function classPromote()
    {
        return view('student.class-promote');
    }

    public function assignment()
    {
        return view('student.assignment');
    }

    public function underConstruction()
    {
        return view('upcoming.under_construction');
    }
}