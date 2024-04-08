<?php

use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\InterviewTemplateController;
use App\Http\Controllers\JobApplicationController;
use App\Mail\SendMail;
use App\Models\JobApplication;
use App\Models\Test;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


Route::post('/test/{id}', function (Request $request) {


//    dd(json_decode(Test::query()->first()->data));
    $test=Test::query()->first();
    $test['data']=json_decode($test['data'],true);
//    dd($test);
    return response()->json($test);
});

Route::post('send-mail', function (Request $request) {
    $data= [];
    $data['files']=$request->email['files'];
    $data = [
        'title' => $request->title,
        'email' => $request->email,
        'files' => $request->file('files'),
        'cc' => $request->cc,
    ];

    Mail::send(new SendMail($data));
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('email-templates')->group(function() {
    Route::get('', [EmailTemplateController::class, 'index']);
    Route::get('all', [EmailTemplateController::class, 'getAll']);
    Route::get('{id}', [EmailTemplateController::class, 'get']);
    Route::post('', [EmailTemplateController::class, 'store']);
    Route::post('{id}', [EmailTemplateController::class, 'update']);
    Route::delete('{id}', [EmailTemplateController::class, 'destroy']);
    Route::delete('/file/{id}', [EmailTemplateController::class, 'deleteEmailTemplateFile']);
});

Route::prefix('interview-templates')->group(function() {
    Route::get('', [InterviewTemplateController::class, 'index']);
    Route::get('all', [InterviewTemplateController::class, 'getAll']);
    Route::get('{id}', [InterviewTemplateController::class, 'get']);
    Route::post('', [InterviewTemplateController::class, 'store']);
    Route::post('{id}', [InterviewTemplateController::class, 'update']);
    Route::delete('{id}', [InterviewTemplateController::class, 'destroy']);
    Route::delete('/file/{id}', [InterviewTemplateController::class, 'deleteEmailTemplateFile']);
});

Route::prefix('job-applications')->group(function () {
    Route::get('', [JobApplicationController::class, 'listJobApplications']);
    Route::get('{id}', [JobApplicationController::class, 'getJobApplication']);
    Route::post('', [JobApplicationController::class, 'storeJobApplication']);
    Route::patch('{id}', [JobApplicationController::class, 'updateJobApplication']);
    Route::delete('{id}', [JobApplicationController::class, 'deleteJobApplication']);
    Route::post('change-stage/{id}', [JobApplicationController::class, 'changeStage']);
    Route::prefix('resume')->group(function () {
        Route::post('', [JobApplicationController::class, 'uploadResume']);
        Route::delete('{resumeId}', [JobApplicationController::class, 'deleteResume']);
//        Route::get('{id}', [JobApplicationController::class, 'getResume']);
//        Route::get('/download/{resumeId}', [JobApplicationController::class, 'downloadResume']);
    });
});

Route::prefix('interview-schedules')->group(function () {
    Route::get('', [InterviewScheduleController::class, 'getListInterviewSchedule']);
    Route::get('jobs/{id}', [InterviewScheduleController::class, 'getInterviewScheduleByJobId']);
    Route::post('', [InterviewScheduleController::class, 'storeInterviewSchedule']);
});
