<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Models\Package;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;



Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return "Storage link created successfully!";
});


Route::get('/refresh', function () {
    Artisan::call('optimize:clear');
    return "Cleared Successfully!";
});


/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $packages = Package::where('is_active', 1)->get();
    return view('auth.landing', compact('packages'));
})->name('landing');


Route::get('/say-hello', function () {
    return "Hello, World!";
})->name('say-hello');


/*
|--------------------------------------------------------------------------
| School Approval Status Page
|--------------------------------------------------------------------------
*/

Route::get('/school/approval-status', function (Request $request) {

    $id = $request->query('id');
    $name = $request->query('name');
    if (!$id) {
        return redirect('/');
    }
    return view('school.approval_status', compact('id', 'name'));
});


/*
|--------------------------------------------------------------------------
| Student Approval Status Page
|--------------------------------------------------------------------------
*/
Route::get('/student/approval-status', function (Request $request) {
    $id = $request->query('id');
    $name = $request->query('name');
    if (!$id) {
        return redirect('/');
    }
    return view('student.approval-status', compact('id', 'name'));
});



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    Route::get('/admin/approval-schools', [DashboardController::class, 'approvalSchools'])
        ->name('admin.approval-schools');

    Route::get('/admin/registered-schools', [DashboardController::class, 'registeredSchools'])
        ->name('admin.registered-schools');

    Route::get('/admin/create-plan', [DashboardController::class, 'createPlan'])
        ->name('admin.create-plan');

    Route::get('/admin/sms-packages', [DashboardController::class, 'smsPackages'])
        ->name('admin.sms-packages');

    Route::get('/admin/sms-package-activation-requests', [DashboardController::class, 'smsPackageActivationRequests'])
        ->name('admin.sms-package-activation-requests');

    Route::get('/admin/dynamic-operation', [DashboardController::class, 'dynamicOperation'])
        ->name('admin.dynamic-operation');

    Route::get('/admin/subscriptions', [DashboardController::class, 'subscriptions'])
        ->name('admin.subscriptions');
});


/*
|---------------------------------------------------------------------------
| School Routes
|---------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:school'])->group(function () {

    // -----------------------------
    // School Dashboard & Menu Pages
    // -----------------------------
    Route::get('/school/dashboard', [DashboardController::class, 'school'])->name('school.dashboard');

    // Teacher & Student
    Route::get('/school/teacher-registration', [DashboardController::class, 'teacherRegistration'])
        ->name('school.teacher-registration');
    Route::get('/school/teacher-id-card', [DashboardController::class, 'underConstruction'])
        ->name('school.teacher-id-card');
    Route::get('/school/teacher-attendance', [DashboardController::class, 'underConstruction'])
        ->name('school.teacher-attendance');
        
    Route::get('/school/student-admission', [DashboardController::class, 'studentAdmission'])
        ->name('school.student-admission');
    Route::get('/school/students', [DashboardController::class, 'studentLists'])
        ->name('school.students');
    Route::get('/school/student-promote', [DashboardController::class, 'underConstruction'])
        ->name('school.student-promote');
    Route::get('/school/student-class-time', [DashboardController::class, 'underConstruction'])
        ->name('school.student-class-time');
    Route::get('/school/student-id-card', [DashboardController::class, 'underConstruction'])
        ->name('school.student-id-card');
    Route::get('/school/student-attendance', [DashboardController::class, 'underConstruction'])
        ->name('school.student-attendance');

    // Academic Settings
    Route::get('/school/classes', [DashboardController::class, 'classes'])->name('school.classes');
    Route::get('/school/groups', [DashboardController::class, 'groups'])->name('school.groups');
    Route::get('/school/sections', [DashboardController::class, 'sections'])->name('school.sections');
    Route::get('/school/sessions', [DashboardController::class, 'sessions'])->name('school.sessions');
    Route::get('/school/subjects', [DashboardController::class, 'subjects'])->name('school.subjects');
    Route::get('/school/syllabus', [DashboardController::class, 'syllabus'])->name('school.syllabus');
    Route::get('/school/class-permission', [DashboardController::class, 'SchoolclassPermission'])->name('school.class-permission');
    Route::get('/school/class-routine', [DashboardController::class, 'classRoutine'])->name('school.class-routine');

    // Guardian & Parents
    Route::get('/school/guardians', [DashboardController::class, 'guardians'])->name('school.guardians');

    // Finance
    Route::get('/school/income', [DashboardController::class, 'income'])->name('school.income');
    Route::get('/school/membership', [DashboardController::class, 'membership'])
        ->name('school.membership');
    Route::get('/school/expense', [DashboardController::class, 'expense'])->name('school.expense');
    Route::get('/school/product', [DashboardController::class, 'underConstruction'])->name('school.product');
    Route::get('/school/supplier', [DashboardController::class, 'underConstruction'])->name('school.supplier');
    Route::get('/school/purchase', [DashboardController::class, 'underConstruction'])->name('school.purchase');
    Route::get('/school/due-paid', [DashboardController::class, 'underConstruction'])->name('school.due-paid');

    // Subscription / Plans
    Route::get('/school/current-plan', [DashboardController::class, 'currentPlan'])->name('school.current-plan');
    Route::get('/school/sms-package', [DashboardController::class, 'smsPackage'])
        ->name('school.sms-package');




    // ================= Fees =================
    Route::get('/school/fees-type', [DashboardController::class, 'feesType'])
        ->name('school.fees-type');

    Route::get('/school/discount', [DashboardController::class, 'discount'])
        ->name('school.discount');

    Route::get('/school/payment', [DashboardController::class, 'payment'])
        ->name('school.payment');

    Route::get('/school/due-list', [DashboardController::class, 'dueList'])
        ->name('school.due-list');




    // ================= HRM =================
    Route::get('/school/employee', [DashboardController::class, 'employee'])
        ->name('school.employee');

    Route::get('/school/payroll', [DashboardController::class, 'payroll'])
        ->name('school.payroll');

    Route::get('/school/role-permission', [DashboardController::class, 'underConstruction'])
        ->name('school.role-permission');

    // ================= Question Bank =================
    Route::get('/school/omr', [DashboardController::class, 'underConstruction'])
        ->name('school.omr');
    Route::get('/school/questions', [DashboardController::class, 'underConstruction'])
        ->name('school.questions');


    // ================= Notice =================
    Route::get('/school/announcement', [DashboardController::class, 'announcement'])
        ->name('school.announcement');


    // ================= Holiday =================
    Route::get('/school/create-holiday', [DashboardController::class, 'createHoliday'])
        ->name('school.create-holiday');


    // ================= Exam Management =================
    Route::get('/school/exam-name', [DashboardController::class, 'examName'])
        ->name('school.exam-name');

    Route::get('/school/exam-routine', [DashboardController::class, 'examRoutine'])
        ->name('school.exam-routine');

    Route::get('/school/grade', [DashboardController::class, 'grade'])
        ->name('school.grade');

    Route::get('/school/admit-card', [DashboardController::class, 'admitCard'])
        ->name('school.admit-card');

    Route::get('/school/seat-plan', [DashboardController::class, 'seatPlan'])
        ->name('school.seat-plan');

    Route::get('/school/mark-submit', [DashboardController::class, 'markSubmit'])
        ->name('school.mark-submit');

    Route::get('/school/schedule', [DashboardController::class, 'schedule'])
        ->name('school.schedule');

    Route::get('/school/result-find', [DashboardController::class, 'resultFind'])
        ->name('school.result-find');

    // ================= Inventory Management =================    
    Route::get('/school/return', [DashboardController::class, 'return'])
        ->name('school.return');

    Route::get('/school/profit-loss', [DashboardController::class, 'profitLoss'])
        ->name('school.profit-loss');

    Route::get('/school/add-payment', [DashboardController::class, 'addPayment'])
        ->name('school.add-payment');

    // ================= Role Management =================
    // Route::get('/school/role-permission', [DashboardController::class, 'rolePermission'])
    //     ->name('school.role-permission');
});



/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {

    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->name('student.dashboard');

    Route::get('/student/teacher-list', [DashboardController::class, 'studentTeacherList'])
        ->name('student.teacher.list');

    Route::get('/student/student-list', [DashboardController::class, 'studentList'])
        ->name('student.student.list');

    Route::get('/student/class-time', [DashboardController::class, 'classTime'])
        ->name('student.class.time');

    Route::get('/student/class-promote', [DashboardController::class, 'classPromote'])
        ->name('student.class.promote');

    Route::get('/student/assignment', [DashboardController::class, 'assignment'])
        ->name('student.assignment');
});


/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {

    Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])
        ->name('teacher.dashboard');

    Route::get('/teacher/teacher-list', [DashboardController::class, 'teacherList'])
        ->name('teacher.teacher.list');

    Route::get('/teacher/class-permission', [DashboardController::class, 'classPermission'])
        ->name('teacher.class.permission');

    Route::get('/teacher/assignment', [DashboardController::class, 'teacherAssignment'])
        ->name('teacher.assignment');

    Route::get('/teacher/student-list', [DashboardController::class, 'teacherStudentList'])
        ->name('teacher.student.list');

    Route::get('/teacher/class-time', [DashboardController::class, 'teacherClassTime'])
        ->name('teacher.class.time');
});