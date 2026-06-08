<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAdmin;
use Illuminate\Http\Request;

class ExamAdminController extends Controller
{
    /**
     * Store a newly created exam admin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Exam $exam)
    {
        // 1. Validate the incoming user ID
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // 2. Ensure the user isn't already added as an admin for this exam
        // firstOrCreate prevents duplicate entries in the database
        ExamAdmin::firstOrCreate([
            'exam_id' => $exam->id,
            'user_id' => $request->user_id
        ]);

        return back()->with('success', 'Admin assigned successfully to ' . $exam->name);
    }

    /**
     * Remove the specified exam admin from storage.
     *
     * @param  int  $examId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($examId, $userId)
    {
        // 1. Find and delete the record linking the user to the exam
        $deleted = ExamAdmin::where('exam_id', $examId)
                            ->where('user_id', $userId)
                            ->delete();

        if ($deleted) {
            return back()->with('success', 'Admin removed successfully.');
        }

        return back()->with('error', 'Admin record not found.');
    }
}