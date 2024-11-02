<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassworkController;
use App\Http\Controllers\ClassworkSubmissionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EssayController;
use App\Http\Controllers\GradeFeeController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\IdentificationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\StudentAnswerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\StudentGradeController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentTotalFeeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestSubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Announcement;
use App\Models\ClassworkSubmission;
use App\Models\Employee;
use App\Models\StudentTotalFee;
use App\Models\TestSubmission;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('employees', EmployeeController::class);
Route::apiResource('admins', AdminController::class)->middleware('auth:sanctum');
Route::apiResource('gradefees', GradeFeeController::class)->middleware('auth:sanctum');
Route::apiResource('students', StudentController::class)->middleware('auth:sanctum');
Route::apiResource('studentsfees', StudentFeeController::class)->middleware('auth:sanctum');
Route::apiResource('classrooms', ClassroomController::class);
Route::apiResource('classlists', ClasslistController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('announcements', AnnouncementController::class);

Route::apiResource('tasks', TaskController::class)->middleware('auth:sanctum');

Route::apiResource('classworks', ClassworkController::class);

Route::post('/employee/register', [AuthController::class, 'EmployeeRegister'])->middleware(['auth:sanctum', 'type.admin']);

// Route::get('/', function() {
//     return ("Hello World");
// });

// admin access
Route::post('/admin/register', [AuthController::class, 'AdminRegister']);
Route::post('/admin/login', [AuthController::class, 'AdminLogin']);

// employee access
Route::post('/employee/login', [AuthController::class, 'EmployeeLogin']);
Route::post('/employee/teacher/login', [AuthController::class, 'TeacherLogin']);
Route::post('/employee/registrar/login', [AuthController::class, 'RegistrarLogin']);
Route::post('/employee/finance/login', [AuthController::class, 'FinanceLogin']);
Route::post('/employee/principal/login', [AuthController::class, 'PrincipalLogin']);


Route::post('/student/login', [AuthController::class, 'StudentLogin']);

Route::post('/student/reset/password/{id}', [StudentController::class, 'studentResetPassword']);

Route::get('/employee/archive', [EmployeeController:: class, 'archive']);
Route::get('/employee/recover/{id}', [EmployeeController:: class, 'recover']);



Route::post('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');

Route::get('/employee/search/{search}', [EmployeeController:: class, 'employeeSearch']);

Route::get('/student/preenrolled', [StudentController:: class, 'ShowStudentPre'])->middleware('auth:sanctum');
Route::get('/student/confirm/{id}', [StudentController:: class, 'StudentConfirm'])->middleware('auth:sanctum');
Route::get('/student/confirm', [StudentController:: class, 'ShowStudentConfirm'])->middleware('auth:sanctum');
Route::post('/student/enroll/{id}', [StudentController:: class, 'StudentEnroll'])->middleware('auth:sanctum');
Route::get('/student/enrolled', [StudentController:: class, 'ShowStudentEnrolled']);
Route::get('/student/grade/{grade}', [StudentController::class, 'getStudentByGrade']);
Route::get('/student/cancel/{id}', [StudentController::class, 'StudentCancel'])->middleware('auth:sanctum');


Route::get('/gradefee/type/{type}', [GradeFeeController:: class, 'gradetype']);


Route::get('/studentfee/{id}', [StudentFeeController:: class, 'studentFee']);
Route::post('/student/pay', [StudentFeeController::class, 'studentFeePay'])->middleware('auth:sanctum');


Route::get('/count-employee', [EmployeeController::class, 'count']);
Route::get('/count-students', [StudentController::class, 'count']);

Route::put('/employee/update/profile', [EmployeeController:: class, 'updateProfile'])->middleware('auth:sanctum');
Route::get('/employee/profile',[EmployeeController::class, 'employeeProfile'])->middleware('auth:sanctum');
Route::put('/employee/reset/password', [EmployeeController::class, 'resetPassword'])->middleware('auth:sanctum');

Route::get('/all/teacher', [EmployeeController::class, 'showTeacher']);

Route::get('/classlist/class/{id}', [ClasslistController::class, 'getByClassId']);

Route::get('/class/subjects/{id}', [SubjectController::class, 'getSubjectByClass']);

Route::get('/teacher/subjects', [SubjectController::class, 'getSubjectByTeacher'])->middleware('auth:sanctum');
Route::get('/student/subjects', [SubjectController::class, 'getSubjectByStudent'])->middleware('auth:sanctum');

Route::get('/classlist/students', [ClasslistController::class, 'getStudentByAdviser'])->middleware('auth:sanctum');

Route::get('/subject/students/{id}', [SubjectController::class, 'getStudentsBySubject']);

Route::post('/finance/makepayment', [StudentTotalFeeController::class, 'makePayment'])->middleware('auth:sanctum');

Route::get('/student/soa', [StudentPaymentController::class, 'getStudentSOA'])->middleware('auth:sanctum');
Route::get('/student/payment/history/{id}', [StudentPaymentController::class, 'getStudentPayment']);
Route::get('/student/balance/{id}', [StudentTotalFeeController::class, 'getStudentBalance']);

Route::get('/task/subject/{id}', [TaskController::class, 'getTaskBySubject']);


Route::put('/task/edit/{id}', [TaskController::class, "editTask"])->middleware('auth:sanctum');

Route::post('/teacher/create/test', [TestController::class, 'createTestAndQuestions']);
Route::get('/test/{id}', [TestController::class, 'getTestWithQuestions']);
Route::put('/teacher/update/test/{id}', [TestController::class, 'updateTestAndQuestions']);

Route::get('/test/subject/{id}', [TestController::class, 'getTestsBySubject'])->middleware('auth:sanctum');
Route::delete('/test/delete/{id}', [TestCOntroller::class, 'deleteTest']);
Route::put('/test/update/{id}', [TestController::class, 'updateTest']);

Route::put('/essay/update/{id}', [QuestionController::class, 'updateEssay']);

Route::put('/identification/update/{id}', [QuestionController::class, "updateIdentification"]);

Route::put('/question/update/{id}', [QuestionController::class, 'updateQuestion']);

Route::get('/students/questions/{id}', [TestController::class, 'getSortedQuestionsByTestId']);

Route::post('/student/submit/test', [TestSubmissionController::class, 'submitTest'])->middleware('auth:sanctum');

Route::get('/test/{testId}/submissions', [TestSubmissionController::class, 'getSubmissions']);

Route::get('/test/{testId}/student/{studentId}/submission', [TestSubmissionController::class, 'getStudentSubmissionAnswer']);

Route::post('/employee/reset/password/{id}', [EMployeeController::class, 'employeeResetPassword']);

Route::post('/essay/create', [QuestionController::class, 'addEssay']);
Route::post('/multiple-choice/create', [QuestionController::class, 'addMultipleChoice']);
Route::post('/identification/create', [QuestionController::class, 'addIdentification']);
Route::delete('/question/delete/{id}', [QuestionController::class, 'deleteQuestion']);

Route::get('/classwork/{id}', [ClassworkController::class, 'getClassworkBySubject'])->middleware('auth:sanctum');

Route::post('/classwork/submit/{id}', [ClassworkSubmissionController::class, 'submitClasswork'])->middleware('auth:sanctum');

Route::get('/classwork/student/{classworkId}/submissions', [ClassworkSubmissionController::class, 'getStudentClassworkSubmissions'])->middleware('auth:sanctum');

Route::get('/classwork/student/{id}', [ClassworkController::class, 'getClassworksForStudent'])->middleware('auth:sanctum');

Route::get('/classwork/{classworkId}/submissions', [ClassworkController::class, 'getSubmissionsByClasswork']);

Route::post('/submission/{id}/score', [ClassworkSubmissionController::class, 'createOrUpdateScore']);

// routes/api.php
Route::post('/test/{testId}/student/{studentId}/submission/update', [StudentAnswerController::class, 'updateScores']);

//student profile
Route::get('/student/profile', [StudentController::class, 'studentProfile'])->middleware('auth:sanctum');
Route::put('/student/update/profile', [StudentController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::put('/student/reset/profile/password', [StudentController::class, 'resetPassword'])->middleware('auth:sanctum');


// enrollemt form
Route::get('/download/form', [RegistrarController::class, 'getEnrollmentForm']);

//announcements
Route::get('/principal/announcements', [AnnouncementController::class, 'getPrincipalAnnouncement']);
Route::get('/employee/announcements', [AnnouncementController::class, 'getEmployeeAnnouncement']);
Route::get('/student/announcements', [AnnouncementController::class, 'getStudentAnnouncement']);
Route::get('/admin/announcements', [AnnouncementController::class, 'getAdminAnnouncement']);
Route::get('/teacher/announcements', [AnnouncementController::class, 'getTeacherAnnouncement']);


//academic year
Route::post('/academic-year', [AcademicYearController::class, 'createAcademicYear']);
Route::get('/academic-year', [AcademicYearController::class, 'getAcademicYear']);
Route::get('/academic-year', [AcademicYearController::class, 'getAcademicYear']);
Route::post('/activate/academic-year', [AcademicYearController::class, 'makeAcademicYearActive']);
Route::delete('/academic-year/delete/{id}', [AcademicYearController::class, 'deleteAcademicYear']);

//student grade
Route::post('/teacher/make/grade', [StudentGradeController::class, 'updateStudentGrade']);
Route::put('/teacher/create-update/grade', [StudentGradeController::class, 'createStudentGrade']);
Route::get('/teacher/grade/{studentId}/{subjectId}', [StudentGradeController::class, 'getStudentGrade']);
Route::get('/student/grade', [StudentGradeController::class, 'getStudentGradesByAcademicYear'])->middleware('auth:sanctum');

//emrollment
Route::get('/unenrolled/students', [EnrollmentController::class, 'getUnenrolledStudents']);
Route::put('/enroll/oldstudent/{id}', [StudentController::class, 'OldStudentConfirm']);



//dashboard count
Route::get('/count/admin/announcements', [AnnouncementController::class , 'getTotalAdminAnnouncement']);
Route::get('/count/principal/announcements', [AnnouncementController::class , 'getTotalPrincipalAnnouncement']);
Route::get('/count/pre-enrolled/students', [StudentController::class, 'getPreEnrolledCount']);
Route::get('/count/confirmed/students', [StudentController::class, 'getConfirmedCount']);
Route::get('/count/enrolled/students', [EnrollmentController::class, 'getCountEnrolledByGrade']);



//academic-year archives
Route::get('/academic-year/deactivated', [AcademicYearController::class, 'getArchivedAcademiicYear']);
Route::get('/academic-year/{id}/students/archive', [EnrollmentController::class, 'getArchivedByAcademicYear']);



//parent access
Route::post('/parent/login', [AuthController::class, 'ParentLogin']);
Route::post('/parent/reset/password/{id}', [GuardianController::class, 'guardianResetPassword']);
Route::get('/parent/soa', [StudentPaymentController::class, 'getParentSOA'])->middleware('auth:sanctum');
Route::get('/parent/subjects', [SubjectController::class, 'getSubjectByParent'])->middleware('auth:sanctum');
Route::get('/parent/grades', [StudentGradeController::class, 'getParentGradesByAcademicYear'])->middleware('auth:sanctum');
Route::get('/parent/student/profile', [StudentController::class, 'parentProfile'])->middleware('auth:sanctum');
Route::put('/parent/reset/profile/password', [GuardianController::class, 'resetPassword'])->middleware('auth:sanctum');



//classroom principal
Route::get('/student/no/classroom', [ClassroomController::class, 'countStudentsByGrades']);


//registrar report
Route::get('/academic-years', [AcademicYearController::class, 'getAcademicYears']);
Route::get('/count/students/by-grade/{academic_year_id}', [EnrollmentController::class, 'getStudentCountByGrade']);
Route::get('/enrollment-counts-by-year', [EnrollmentController::class, 'getEnrollmentCountsByYear']);



//lms archive
Route::get('/teacher/subjects/archived', [SubjectController::class , 'getArchivedSubjectsByTeacher'])->middleware('auth:sanctum');
Route::get('/teacher/count/subjects', [SubjectController::class, 'getSubjectCountByTeacher'])->middleware('auth:sanctum');


//student dashboard
Route::get('/student/subejcts/coount', [SubjectController::class, 'getSubjectCountByStudent'])->middleware('auth:sanctum');

// missing documents
Route::get('/student/without-birth-certificate', [StudentController::class, 'getStudentsWithoutBirthCertificate']);
Route::get('/student/without-report-card', [StudentController::class, 'getStudentsWithoutReportCard']);
Route::get('/student/without-transcript-record', [StudentController::class, 'getStudentsWithoutTranscriptRecord']);
Route::get('/student/without-good-moral', [StudentController::class, 'getStudentsWithoutGoodMoral']);


Route::get('/audit/all', [AuditController::class, 'getAudit']);
