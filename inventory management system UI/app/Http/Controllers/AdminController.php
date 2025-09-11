<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\AccountApproved;

class AdminController extends Controller
{
    /**
     * Display pending users for approval
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('admin.pending-users', compact('pendingUsers'));
    }

    /**
     * Approve user account
     */
    public function approveUser(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->status === 'approved') {
                return $this->approvalResponse($user, 'User account was already approved!', 'info');
            }
            
            $user->status = 'approved';
            $user->save();

            // Send approval email to user
            try {
                Mail::to($user->email)->send(new AccountApproved($user));
                $message = "✅ User account for {$user->name} ({$user->email}) has been approved successfully! A confirmation email has been sent to the user.";
            } catch (\Exception $e) {
                \Log::error('Failed to send approval confirmation: ' . $e->getMessage());
                $message = "✅ User account for {$user->name} ({$user->email}) has been approved successfully! However, the confirmation email failed to send.";
            }

            return $this->approvalResponse($user, $message, 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to approve user: ' . $e->getMessage());
            return $this->approvalResponse(null, '❌ Failed to approve user account. Please try again or contact the administrator.', 'error');
        }
    }
    
    /**
     * Generate approval response page
     */
    private function approvalResponse($user, $message, $type)
    {
        // If this is a web request (from admin panel), redirect back
        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['message' => $message, 'type' => $type]);
        }
        
        // If accessed from email link, show a nice approval page
        return view('admin.approval-result', compact('user', 'message', 'type'));
    }

    /**
     * Reject user account
     */
    public function rejectUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete(); // Remove the user entirely
        
        return redirect()->back()->with('success', 'User account rejected and removed.');
    }

    /**
     * List all users
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }
}
