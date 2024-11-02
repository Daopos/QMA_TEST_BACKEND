<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Audit;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\Registrar;
use App\Models\Student;
use App\Notifications\LoginSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function AdminRegister(Request $request) {
        $fields = $request->validate([
            'username' => 'required|max:255',
            'password' => 'required',
        ]);

        $admin = Admin::create($fields);

        $token = $admin->createToken($admin->username);



        return [
            'user' => $admin,
            'token' => $token->plainTextToken
        ];
    }

    public function AdminLogin(Request $request) {
        $request->validate([
            'username' => 'required|exists:admins',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if(!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401); // Return 401 Unauthorized status code
        }
        $token = $admin->createToken($admin->username, ['role:admin']);

        return [
            'user' => $admin,
            'token' => $token->plainTextToken
        ];
    }

    public function AdminLogout(Request $request) {
    }

            // 'name' => 'required|max:255',

    public function EmployeeRegister(Request $request) {
                $fields = $request->validate([
                    'email' => 'required|email|unique:employees',
                    'password' => 'required',
                    'fname' => 'required',
                    'mname' => 'nullable',
                    'lname' => 'required',
                    'address' => 'required',
                    'type' => 'required',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Updated validation rule for image
                    'desc' => 'nullable',
                ]);

                // Handle image upload
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('images', 'public');
                    $fields['image'] = $imagePath;
                }

                $user = Employee::create($fields);

                // $token = $user->createToken($user->email);

                 Audit::create([
        'user' => 'Admin',
        'action' => 'Admin registered new employee: ' . $user->fname . ' ' . $user->lname,
        'user_level' => 'Admin',
    ]);


                return [
                    'user' => $user,
                    // 'token' => $token->plainTextToken
                ];
        }


    public function EmployeeLogin(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:employees',
            'password' => 'required',
        ]);

        $user = Employee::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return [
                'message' => 'The provided credentials are incorrect.'
            ];
        }
        $token = "";
        if($user->type == "Teacher") {
            $token = $user->createToken($user->email, ['role:teacher']);
        }
        if($user->type == "Principal") {
            $token = $user->createToken($user->email, ['role:principal']);
        }
        if($user->type == "Registrar") {
            $token = $user->createToken($user->email, ['role:registrar']);
        }
        if($user->type == "Finance") {
            $token = $user->createToken($user->email, ['role:finance']);
        }



        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function RegistrarLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required',
        ]);

        // Find the employee by email
        $registrar = Employee::where('email', $request->email)
                             ->where('type', 'Registrar')
                             ->first();

        // Check if the registrar exists and the password matches
        if (!$registrar || !Hash::check($request->password, $registrar->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        // Create a token with a specific role
        $token = $registrar->createToken('registrar-token', ['role:registrar'])->plainTextToken;

        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($registrar, $loginDate, $device);
        $notification->sendLoginNotification();


        $fullName = $registrar->fname . ' ' .
        ($registrar->mname ? $registrar->mname[0] . '. ' : '') . // Check if mname is not null
        $registrar->lname;
        Audit::create([
            'user' =>$fullName,  // Use . for concatenation and add spaces as needed
            'action' => 'Log in',
            'user_level' => "Registrar",
        ]);
        return response()->json(['user' => $registrar, 'token' => $token], 200);
    }


    public function TeacherLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required',
        ]);

        $teacher = Employee::where('email', $request->email)
        ->where('type', 'Teacher')
        ->first();


        // Check if the employee exists, the password is correct, and the employee type is 'teacher'
        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $teacher->createToken('teacher-token', ['role:teacher'])->plainTextToken;

        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($teacher, $loginDate, $device);
        $notification->sendLoginNotification();

        $fullName = $teacher->fname . ' ' .
        ($teacher->mname ? $teacher->mname[0] . '. ' : '') . // Check if mname is not null
        $teacher->lname;
        Audit::create([
            'user' =>   $fullName,  // Use . for concatenation and add spaces as needed
            'action' => 'Log in',
            'user_level' => "Teacher",
        ]);
        return response()->json(['user' => $teacher, 'token' => $token]);
    }

    public function FinanceLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required',
        ]);

        $finance = Employee::where('email', $request->email)
        ->where('type', 'Finance')
        ->first();


        // Check if the employee exists, the password is correct, and the employee type is 'teacher'
        if (!$finance || !Hash::check($request->password, $finance->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $finance->createToken('finance-token', ['role:finance'])->plainTextToken;

        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($finance, $loginDate, $device);
        $notification->sendLoginNotification();


            $fullName = $finance->fname . ' ' .
                        ($finance->mname ? $finance->mname[0] . '. ' : '') . // Check if mname is not null
                        $finance->lname;

            Audit::create([
                'user' => $fullName,
                'action' => 'Log in',
                'user_level' => "Finance",
            ]);

        return response()->json(['user' => $finance, 'token' => $token]);
    }


    public function PrincipalLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required',
        ]);

        $principal = Employee::where('email', $request->email)
        ->where('type', 'Principal')
        ->first();


        // Check if the employee exists, the password is correct, and the employee type is 'teacher'
        if (!$principal || !Hash::check($request->password, $principal->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $principal->createToken('principal-token', ['role:principal'])->plainTextToken;

        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($principal, $loginDate, $device);
        $notification->sendLoginNotification();


        $fullName = $principal->fname . ' ' .
        ($principal->mname ? $principal->mname[0] . '. ' : '') . // Check if mname is not null
        $principal->lname;
        Audit::create([
            'user' => $fullName,  // Use . for concatenation and add spaces as needed
            'action' => 'Log in',
            'user_level' => "Principal",
        ]);

        return response()->json(['user' => $principal, 'token' => $token]);
    }



    public function Logout(Request $request) {

        $request->user()->tokens()->delete();

        return [
            'message' =>  "You are logged out",
        ];
    }

    public function StudentLogin(Request $request)
    {
        $request->validate([
            'lrn' => 'required|exists:students',
            'password' => 'required',
        ]);

        $student = Student::where('lrn', $request->lrn)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $student->createToken('student-token', ['role:student'])->plainTextToken;

        // Send login notification
        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($student, $loginDate, $device);
        $notification->sendLoginNotification();


        $fullName = $student->firstname . ' ' .
        ($student->middlename ? $student->middlename[0] . '. ' : '') . // Check if mname is not null
        $student->lastname;
        Audit::create([
            'user' =>  $fullName,  // Use . for concatenation and add spaces as needed
            'action' => 'Log in',
            'user_level' => "Student",
        ]);

        return response()->json(['user' => $student, 'token' => $token]);
    }

    public function ParentLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:guardians',
            'password' => 'required',
        ]);

        $parent = Guardian::where('username', $request->username)->first();

        if (!$parent || !Hash::check($request->password, $parent->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $parent->createToken('parent-token', ['role:parent'])->plainTextToken;

        // Send login notification
        $loginDate = now()->format('F j, l g:i A'); // e.g., October 15, Tuesday 9:30 PM
        $device = $request->header('User-Agent'); // Get device info from User-Agent

        $notification = new LoginSuccessful($parent, $loginDate, $device);
        $notification->sendLoginNotification();

        $student = Student::where('id', $parent->student_id)->first();
        $fullName = $student->firstname . ' ' .
        ($student->middlename ? $student->middlename[0] . '. ' : '') . // Check if mname is not null
        $student->lastname;
        Audit::create([
            'user' => 'Parent of'. $fullName,  // Use . for concatenation and add spaces as needed
            'action' => 'Log in',
            'user_level' => "Parent",
        ]);


        return response()->json(['user' => $parent, 'token' => $token]);
    }
}
