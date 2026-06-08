<?php

use App\Http\Controllers\{
    DashboardController,
    SchoolController,
    StreamController,
    StudentController,
    SubjectController,
    PaperController,
    GradingController,
    GradingSystemController,
    UserController,
    ExamController,
    ExamSchoolController,
    ExamConfigurationController,
    ExamAdminController,
    MarkController
};
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => auth()->check() ? redirect('/dashboard') : redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/get-schools/{countyId}', [RegisterController::class, 'getSchools']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    /* Marks Submission & View Flow */
    Route::prefix('exams/{exam}/{examSlug}')->group(function () {
        Route::get('/school/{school}/{schoolSlug}/view', [MarkController::class, 'viewSubmissions'])->name('exams.school.view-submissions');
        Route::get('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/view', [MarkController::class, 'adminShowStudents'])->name('marks.admin-show-students');
        Route::get('/school/{school}/{schoolSlug}/submit-streams', [MarkController::class, 'showSubmissionStreams'])->name('marks.submit-streams');
        Route::get('/school/{school}/{schoolSlug}/stream/{stream}/select-paper', [MarkController::class, 'selectPaper'])->name('marks.select-paper');
        Route::get('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/submit-entry', [MarkController::class, 'showSubmissionStudents'])->name('marks.submit-entry');
        Route::post('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/store', [MarkController::class, 'store'])->name('marks.admin-store');
        Route::post('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/marks/update/{mark}', [MarkController::class, 'updateMark'])->name('marks.update');
        Route::post('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/marks/delete/{mark}', [MarkController::class, 'deleteMark'])->name('marks.delete');
        Route::delete('/school/{school}/{schoolSlug}/stream/{stream}/paper/{paper}/delete-stream-marks', [MarkController::class, 'deleteAllMarks'])
    ->name('marks.delete-all');
    });

    /* Users Management */
    Route::middleware(['can:manage-system'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{user}/update-school', [UserController::class, 'updateSchool'])->name('update-school');
        Route::patch('/{user}/update-role', [UserController::class, 'updateRole'])->name('update-role');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    /* Schools & Students */
    Route::prefix('schools')->name('schools.')->group(function () {
        Route::get('/', [SchoolController::class, 'index'])->name('index');
        Route::get('/create', [SchoolController::class, 'create'])->name('create');
        Route::post('/', [SchoolController::class, 'store'])->name('store');
        Route::get('/{school}/{slug}', [SchoolController::class, 'show'])->name('show');
        Route::get('/{school}/{slug}/edit', [SchoolController::class, 'edit'])->name('edit');
        Route::put('/{school}/{slug}', [SchoolController::class, 'update'])->name('update');
        Route::delete('/{school}/{slug}', [SchoolController::class, 'destroy'])->name('destroy');

        Route::get('/{school}/{slug}/forms/{form}/streams', [StreamController::class, 'index'])->name('forms.streams.index');
        Route::post('/{school}/{slug}/forms/{form}/streams', [StreamController::class, 'store'])->name('forms.streams.store');
        Route::put('/{school}/{slug}/forms/{form}/streams/{stream}', [StreamController::class, 'update'])->name('forms.streams.update');
        Route::delete('/{school}/{slug}/forms/{form}/streams/{stream}', [StreamController::class, 'destroy'])->name('forms.streams.destroy');

        Route::get('/{school}/{slug}/forms/{form}/import', [StudentController::class, 'showImportForm'])->name('forms.import.view');
        Route::post('/{school}/{slug}/forms/{form}/import', [StudentController::class, 'processImport'])->name('forms.import.process');

        Route::prefix('/{school}/{slug}/forms/{form}/streams/{stream}/students')->name('forms.streams.students.')->group(function () {
            Route::get('/', [StudentController::class, 'index'])->name('index');
            Route::post('/', [StudentController::class, 'store'])->name('store');
            Route::put('/{student}', [StudentController::class, 'update'])->name('update');
            Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
        });
    });

    /* Subjects & Papers */
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{subject}/{slug}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{subject}/{slug}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}/{slug}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}/{slug}', [SubjectController::class, 'destroy'])->name('destroy');

        Route::post('/{subject}/papers', [PaperController::class, 'store'])->name('papers.store');
        Route::delete('/papers/{paper}', [PaperController::class, 'destroy'])->name('papers.destroy');
    });

    /* Grading Systems */
    Route::prefix('grading-systems')->name('grading-systems.')->group(function () {
        Route::get('/', [GradingSystemController::class, 'index'])->name('index');
        Route::post('/', [GradingSystemController::class, 'store'])->name('store');
        Route::get('/{gradingSystem}/edit', [GradingSystemController::class, 'edit'])->name('edit');
        Route::put('/{gradingSystem}', [GradingSystemController::class, 'update'])->name('update');
        Route::delete('/{gradingSystem}', [GradingSystemController::class, 'destroy'])->name('destroy');

        Route::prefix('{gradingSystem}/grades')->name('grades.')->group(function () {
            Route::get('/', [GradingController::class, 'index'])->name('index');
            Route::post('/', [GradingController::class, 'store'])->name('store');
            Route::put('/{grading}', [GradingController::class, 'update'])->name('update');
            Route::delete('/{grading}', [GradingController::class, 'destroy'])->name('destroy');
        });
    });

    /* Exams, Participation & Admins */
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');

        Route::middleware(['can:manage-exams'])->group(function () {
            Route::get('/create', [ExamController::class, 'create'])->name('create');
            Route::post('/', [ExamController::class, 'store'])->name('store');
            Route::get('/{exam}/{slug}/edit', [ExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}/{slug}', [ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}/{slug}', [ExamController::class, 'destroy'])->name('destroy');
            Route::patch('/{exam}/{slug}/toggle-status', [ExamController::class, 'toggleStatus'])->name('toggle-status');

            Route::post('/{exam}/register-schools-bulk', [ExamSchoolController::class, 'storeBulk'])->name('register-schools-bulk');
            Route::delete('/{exam}/remove-school/{school}', [ExamSchoolController::class, 'destroy'])->name('remove-school');
            Route::get('/{exam}/{slug}/configure', [ExamConfigurationController::class, 'index'])->name('configurations.index');
            Route::post('/{exam}/{slug}/configure', [ExamConfigurationController::class, 'store'])->name('configurations.store');
            Route::post('/{exam}/add-admin', [ExamAdminController::class, 'store'])->name('add-admin');
            Route::delete('/{exam}/remove-admin/{userId}', [ExamAdminController::class, 'destroy'])->name('remove-admin');
        });

        Route::middleware(['can:view,exam'])->group(function () {
            Route::get('/{exam}/{slug}', [ExamController::class, 'show'])->name('show');
        });
    });
});