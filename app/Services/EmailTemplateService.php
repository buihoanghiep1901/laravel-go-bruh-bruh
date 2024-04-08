<?php

namespace App\Services;

use App\Http\Requests\EmailTemplateRequest;
use App\Models\EmailFile;
use App\Models\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmailTemplateService extends BaseService
{

    protected function setModel()
    {
        $this->model = new EmailTemplate();
    }

    public function list(Request $request)
    {
        try {
//            $createdByMe = $request->query('created_by_me');
//            $userLoginId = auth()->id();
            $query = EmailTemplate::query()->with('creator:id,name', 'files:id,email_template_id,url')
                ->select(['id', 'title', 'created_by']);


//            if($createdByMe) {
//                $query->where('created_by', $userLoginId);
//            }

            $emailTemplates = $this->getPaginateByQuery($query, $request);
            return $this->responseSuccess($emailTemplates, 'success');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 'listEmailTemplate');
        }

    }

    public function getAll()
    {
        try {
            $emailTemplates = EmailTemplate::query()->with('creator:id,name', 'files')->get();
            return $this->responseSuccess($emailTemplates, 'success');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 'getAllEmailTemplate');
        }
    }

    public function get($id)
    {
        return $this->responseSuccess(EmailTemplate::with('creator:id,name', 'files')->findOrFail($id), 'success');
    }

    public function store(EmailTemplateRequest $request) : JsonResponse
    {
//        $userLoginId = auth()->id();
        $service = new S3Service();
        $now = Carbon::now();
        $emailFiles = [];
        try {
            DB::beginTransaction();
            $emailTemplate = EmailTemplate::query()->create(
                [
                    'title' => $request->get('title'),
                    'created_by' => $request->get('created_by'),
                    'content' => $request->get('content') ?? '',
                ]
            );
            if($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $file) {
                    $emailUrl = $service->uploadFileToS3($file, EmailFile::FOLDER_S3);
                    $emailFiles[] = [
                        'email_template_id' => $emailTemplate->id,
                        'url' => $emailUrl,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }

                EmailFile::insert($emailFiles);
            }

            DB::commit();
            return $this->responseSuccess($emailTemplate, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'storeEmailTemplate');
        }
    }

    public function update($request, $id)
    {
        $now = Carbon::now();
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $emailTemplate = EmailTemplate::query()->findOrFail($id);
            $emailTemplate->update(
                [
                    'title' => $request->get('title'),
                    'content' => $request->get('content'),
                ]
            );

            if($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $file) {
                    $emailUrl = $service->uploadFileToS3($file);
                    $emailFiles[] = [
                        'email_template_id' => $emailTemplate->id,
                        'url' => $emailUrl,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }

                EmailFile::insert($emailFiles);
            }
            DB::commit();
            return $this->responseSuccess('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'updateMention');
        }
    }

    public function delete($id)
    {
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $emailTemplate = EmailTemplate::with('files')->findOrFail($id);
            foreach($emailTemplate->files as $emailFile) {
                $service->removeFile($emailFile->url);
            }

            $emailTemplate->files()->delete();
            $emailTemplate->delete();
            DB::commit();
            return $this->responseSuccess($emailTemplate, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'deleteEmailTemplate');
        }
    }

    public function deleteEmailTemplateFile($id)
    {
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $emailFile = EmailFile::findOrFail($id);
            $service->removeFile($emailFile->url);
            $emailFile->delete();
            DB::commit();
            return $this->responseSuccess($emailFile, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'deleteEmailTemplateFile');
        }
    }
}
