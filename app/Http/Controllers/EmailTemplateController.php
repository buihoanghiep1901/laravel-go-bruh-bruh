<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailTemplateRequest;
use App\Services\EmailTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTemplateController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new EmailTemplateService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
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

    public function store(EmailTemplateRequest $request) : JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(EmailTemplateRequest $request, $id) : JsonResponse
    {
        return $this->service->update($request, $id);
    }

    public function destroy($id) : JsonResponse
    {
        return $this->service->delete($id);
    }

    public function deleteEmailTemplateFile($id) : JsonResponse
    {
        return $this->service->deleteEmailTemplateFile($id);
    }
}
