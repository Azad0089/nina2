<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification;

class VerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $latest = Verification::where('user_id', $user->id)->latest()->first();

        return view('dashboard.verification', compact('user', 'latest'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:190',
            'id_document' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240', // up to 10MB
        ]);

        $user = Auth::user();

        $file = $request->file('id_document');
        $filename = 'id_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('verifications', $filename, 'public');

        Verification::create([
            'user_id' => $user->id,
            'full_name' => $request->input('full_name'),
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Identity verification submitted! Our staff will review your documents within 24-48 hours.');
    }
}
