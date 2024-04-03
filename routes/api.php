<?php

use App\Http\Controllers\JobApplicationController;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::post('/', function (Request $request) {
    $path = 0;
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $name = sprintf('%s_%s', now()->format('d-m-Y-H-i-s'), $file->getClientOriginalName());
        $path = Storage::disk('s3')->putFileAs('test', new File($file), $name, 'private');
    }

//    $path = Storage::disk('s3')->temporaryUrl(
//        'test/2403-04-2024-16-15-02_SoICT_Do_an_tot_nghiep_Ung_dung.pdf', now()->addMinutes(15)
//    );

    $path = Storage::disk('s3')->delete('test/2403-04-2024-16-15-02_SoICT_Do_an_tot_nghiep_Ung_dung.pdf');

    return response()->json([
        'status' => 'OK',
        'path' => $path,
    ]);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('job-applications')->group(function () {
    Route::get('', [JobApplicationController::class, 'listJobApplications']);
    Route::get('{id}', [JobApplicationController::class, 'getJobApplication']);
    Route::post('', [JobApplicationController::class, 'storeJobApplication']);
    Route::patch('{id}', [JobApplicationController::class, 'updateJobApplication']);
    Route::delete('{id}', [JobApplicationController::class, 'deleteJobApplication']);
//    Route::post('change-stage/{id}', [JobApplicationController::class, 'changeStage']);
    Route::prefix('resume')->group(function () {
        Route::post('', [JobApplicationController::class, 'uploadResume']);
        Route::delete('{resumeId}', [JobApplicationController::class, 'deleteResume']);
//        Route::get('{id}', [JobApplicationController::class, 'getResume']);
//        Route::get('/download/{resumeId}', [JobApplicationController::class, 'downloadResume']);
    });
});
