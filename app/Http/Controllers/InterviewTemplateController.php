<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewTemplateRequest;
use App\Services\InterviewTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewTemplateController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new InterviewTemplateService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) : JsonResponse
    {
        return $this->service->list($request);
    }

    public function getAll()
    {
        return $this->service->getAll();
    }

    public function get($id) : JsonResponse
    {
        return $this->service->get($id);
    }

    public function store(InterviewTemplateRequest $request) : JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(InterviewTemplateRequest $request, $id) : JsonResponse
    {
        return $this->service->update($request, $id);
    }

    public function destroy($id) : JsonResponse
    {
        return $this->service->delete($id);
    }

    public function deleteInterviewTemplateFile($id) : JsonResponse
    {
        return $this->service->deleteInterviewTemplateFile($id);
    }
}
