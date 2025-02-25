<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class NotificationController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);

        $notification->markAsRead();

        return back()->with('status', 'Notification marquée comme lue.');
    }

    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', 'Toutes les notifications ont été marquées comme lues.');
    }
}
