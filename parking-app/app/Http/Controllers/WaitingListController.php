<?php

namespace App\Http\Controllers;

use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitingListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $waitingList = WaitingList::with('user')
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        return view('waiting-list.index', compact('waitingList'));
    }

    public function cancel()
    {
        $waitingListEntry = Auth::user()->waitingList()->where('status', 'waiting')->first();

        if ($waitingListEntry) {
            $waitingListEntry->remove();
            return back()->with('status', 'Vous avez été retiré de la liste d\'attente.');
        }

        return back()->with('error', 'Vous n\'êtes pas dans la liste d\'attente.');
    }

    public function updatePositions(Request $request)
    {
        $this->middleware('admin');

        $request->validate([
            'positions' => ['required', 'array'],
            'positions.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->positions as $id => $position) {
            WaitingList::where('id', $id)->update(['position' => $position]);
        }

        return back()->with('status', 'Positions mises à jour avec succès.');
    }
}
