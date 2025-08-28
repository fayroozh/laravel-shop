<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::with('user')->latest()->get();
        return view('admin.feedback', compact('feedback'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'feedback' => 'required|string'
        ]);

        $feedback = $request->user()->feedback()->create([
            'feedback' => $validated['feedback'],
            'name' => $request->user()->name,
        ]);

        // Notify admin
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Feedback Received',
                'message' => "New feedback has been submitted by {$request->user()->name}.",
                'type' => 'info',
            ]);
        }

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}