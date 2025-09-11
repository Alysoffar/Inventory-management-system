<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\AccountApprovalRequest;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }
    
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Find the user first to check their status
        $user = User::where('email', $request->email)->first();
        
        if ($user && $user->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Your account is pending admin approval. Please wait for approval before logging in.',
            ])->onlyInput('email');
        }
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Add welcome message
            $userName = Auth::user()->name ?? 'User';
            session()->flash('welcome_message', "🎉 GREAT TO HAVE YOU BACK, " . strtoupper($userName) . "! 🚀");
            
            return redirect()->intended(route('dashboard'));
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }
    
    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        // Create user with pending status
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company' => $request->company,
            'phone' => $request->phone,
            'status' => 'pending', // User needs admin approval
            'role' => 'user'
        ]);
        
        // Send email to admin for approval
        try {
            Mail::to('alysoffar06@gmail.com')->send(new AccountApprovalRequest($user));
            $emailStatus = 'Admin notification email sent successfully.';
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Failed to send approval email: ' . $e->getMessage());
            $emailStatus = 'Registration successful! Admin will be notified through the system dashboard.';
        }
        
        return redirect()->route('login')
                       ->with('success', "🎉 WELCOME ABOARD! Your registration was successful! {$emailStatus} You will be notified once your account is approved.");
    }
    
    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
    
    /**
     * Show pending approval page
     */
    public function pending()
    {
        return view('auth.pending');
    }
    
    /**
     * Approve user account (admin only)
     */
    public function approveUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'approved';
        $user->save();
        
        // Send approval email to user
        try {
            Mail::to($user->email)->send(new \App\Mail\AccountApproved($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send approval confirmation: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'User account approved successfully.');
    }
}
