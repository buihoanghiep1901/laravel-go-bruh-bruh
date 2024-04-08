<?php

namespace App\Services;



use App\Http\Requests\InterviewTemplateRequest;
use App\Models\InterviewFile;
use App\Models\InterviewTemplate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterviewTemplateService extends BaseService
{

    protected function setModel(): void
    {
        $this->model = new InterviewTemplate();
    }

    public function list(Request $request): JsonResponse
    {
        try {
//            $createdByMe = $request->query('created_by_me');
//            $userLoginId = auth()->id();
            $query = InterviewTemplate::query()->with('creator:id,name', 'files:id,interview_template_id,url')
                ->select(['id', 'title']);

//            if($createdByMe) {
//                $query->where('created_by', $userLoginId);
//            }

            $result = $this->getPaginateByQuery($query, $request);
            return $this->responseSuccess($result, 'success');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 'listInterviewTemplate');
        }

    }

    public function getAll(): JsonResponse
    {
        try {
            $result = InterviewTemplate::query()
                ->with('creator:id,name', 'files')
                ->get();

            return $this->responseSuccess($result, 'success');
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 'getAllInterviewTemplate');
        }
    }

    public function get($id): JsonResponse
    {
        return $this->responseSuccess(InterviewTemplate::with('creator:id,name', 'files', 'members:id,name,email')->findOrFail($id), 'success');
    }

    public function store(InterviewTemplateRequest $request) : JsonResponse
    {
        $service = new S3Service();
        $now = Carbon::now();
        $interviewFiles = [];
        try {
            DB::beginTransaction();
            $result = InterviewTemplate::query()->create(
                [
                    'title' => $request->get('title'),
                    'content' => $request->get('content'),
                    'note' => $request->get('note') ?? '',
                    'created_by' => $request->get('created_by'),
                ]
            );
            if($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $file) {
                    $interviewUrl = $service->uploadFileToS3($file,InterviewTemplate::FOLDER);
                    $interviewFiles[] = [
                        'interview_template_id' => $result->id,
                        'url' => $interviewUrl,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                InterviewFile::query()->insert($interviewFiles);
            }

            DB::commit();
            return $this->responseSuccess($result, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'storeInterviewTemplate');
        }
    }

    public function update($request, $id): JsonResponse
    {
        $now = Carbon::now();
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $result = InterviewTemplate::query()->findOrFail($id);
            $result->update(
                [
                    'title' => $request->get('title'),
                    'content' => $request->get('content'),
                    'note' => $request->get('note') ?? '',
                ]
            );

            if($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $file) {
                    $interviewUrl = $service->uploadFileToS3($file, InterviewTemplate::FOLDER);
                    $interviewFiles[] = [
                        'email_template_id' => $result->id,
                        'url' => $interviewUrl,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }

                InterviewFile::query()->insert($interviewFiles);
            }
            DB::commit();
            return $this->responseSuccess('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'updateMention');
        }
    }

    public function delete($id): JsonResponse
    {
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $interviewTemplate = InterviewTemplate::query()->with('files')->findOrFail($id);
            foreach($interviewTemplate->files as $interviewFile) {
                $service->removeFile($interviewFile->url);
            }

            $interviewTemplate->files()->delete();
            $interviewTemplate->delete();
            DB::commit();
            return $this->responseSuccess($interviewTemplate, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'deleteInterviewTemplate');
        }
    }

    public function deleteInterviewTemplateFile($id): JsonResponse
    {
        $service = new S3Service();
        try {
            DB::beginTransaction();
            $interviewFile = InterviewTemplate::query()->findOrFail($id);
            $service->removeFile($interviewFile->url);
            $interviewFile->delete();
            DB::commit();
            return $this->responseSuccess($interviewFile, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'deleteInterviewTemplateFile');
        }
    }
}
