<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeStageRequest;
use App\Http\Requests\JobApplicationRequest;
use App\Services\JobApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    protected JobApplicationService $service;

    public function __construct()
    {
        $this->service = new JobApplicationService();
    }

    public function listJobApplications(Request $request): JsonResponse
    {
        return $this->service->listJobApplications($request);
    }


    public function getJobApplication($id): JsonResponse
    {
        return $this->service->getJobApplication($id);
    }

    public function storeJobApplication(JobApplicationRequest $request): JsonResponse
    {
        return $this->service->storeJobApplication($request);
    }

    public function updateJobApplication(JobApplicationRequest $request, $id): JsonResponse
    {
        return $this->service->updateJobApplication($request, $id);
    }

    public function deleteJobApplication($id): JsonResponse
    {
        return $this->service->deleteJobApplication($id);
    }

    public  function deleteResume($resumeId): JsonResponse
    {
        return $this->service->deleteResume($resumeId);
    }

    public  function uploadResume(Request $request): JsonResponse
    {
        return $this->service->uploadResume($request);
    }

    public  function changeStage(changeStageRequest $request,$id): JsonResponse
    {
        return $this->service->changeStage($id,$request);
    }

}

