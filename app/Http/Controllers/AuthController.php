<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function index()
    {
        return response() -> json (User::latest()->get());
        
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);
    
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
    
        $token = $user->createToken('myapptoken')->plainTextToken;
    
        // $response = [
        //     'user' => $user,
        //     'token' => $token
        // ];
    
        // return response($response, 201);

        // Flash success message to session
        $request->session()->flash('success', 'Registration successful! You can now login.');

        // Redirect to the login page
        return redirect()->route('api/login');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    
        if (!Auth::attempt($credentials)) {
            return response(['message' => 'Invalid credentials'], 401);
        }
    
        $user = $request->user();
        $token = $user->createToken('myapptoken')->plainTextToken;
    
        return redirect('/dashboard')->with('token', $token);
    }
    

    public function dashboard()
    {
    
    $data = User::all(); 

    // Pass the data
    return view('dashboard', ['data' => $data]);
    }

    

}
