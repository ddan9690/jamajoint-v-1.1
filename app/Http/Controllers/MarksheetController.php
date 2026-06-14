<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MarksheetController extends Controller
{
    // App\Http\Controllers\MarksheetController.php

    public function download(Exam $exam, $examSlug, School $school, $schoolSlug)
    {
        $exam->load(['subject.papers', 'academicYear', 'term']);

        // Check if school has streams for this form
        $streams = $school->streams()
            ->where('form_id', $exam->form_id)
            ->with(['students' => function ($query) {
                $query->orderBy('admission_number', 'asc');
            }])
            ->get();

        $data = [
            'exam' => $exam,
            'school' => $school,
            'streams' => $streams,
            'hasData' => $streams->isNotEmpty() && $streams->sum(fn($s) => $s->students->count()) > 0
        ];

        $pdf = Pdf::loadView('dashboard.downloads.marksheet', $data);

        return $pdf->download("{$school->name}_{$exam->name}.pdf");
    }
}
