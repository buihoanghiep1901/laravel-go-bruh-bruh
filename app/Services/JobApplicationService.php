<?php

namespace App\Services;

use App\Http\Requests\JobApplicationRequest;
use App\Mail\SendMail;
use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\JobApplicationResume;
use App\Models\JobStage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class JobApplicationService extends BaseService
{
    protected function setModel(): void
    {
        $this->model = new JobApplication();
    }

    public function listJobApplications(Request $request): JsonResponse
    {
        try {
            $full_name = $request->query('full_name');
            $email = $request->query('email');
            $job_id = $request->query('job_id');
            $stage_id = $request->query('stage_id');
            $query = JobApplication::query()->with([
                'job:id,title',
                'stage:id,name',
                'resumes:id,job_application_id,name',
                'mentionGroups:id,name',
                'mentionUsers:id,name',
            ]);

            if ($full_name) {
                $query->where('full_name', 'like', '%' . $full_name . '%');
            }
            if ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            }
            if ($job_id) {
                $query->where('job_id', $job_id);
            }
            if ($stage_id) {
                $query->where('stage_id', $stage_id);
            }

            $jobApplications = $this->getPaginateByQuery($query, $request);
            return $this->responseSuccess($jobApplications, 'success');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'listJobApplication');
        }
    }

    public function getJobApplication($id): JsonResponse
    {
        try {
            return $this->responseSuccess(JobApplication::query()->with([
                'job',
                'stage'
            ])->findOrFail($id), 'success');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'getJobApplication');
        }
    }

    public function storeJobApplication(JobApplicationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $jobApplication = new JobApplication();
            $jobApplication->full_name = $request->get('full_name');
            $jobApplication->email = $request->get('email');
            $jobApplication->job_id = $request->get('job_id');
            $jobApplication->stage_id = $request->get('stage_id');
            $jobApplication->save();


            if ($request->hasFile('resumes')) {
                $resumes = [];
                $s3 = new S3Service();
                foreach ($request->file('resumes') as $resume) {
                    $resumes[] = [
                        'name' => $resume->getClientOriginalName(),
                        'resume' => $s3->uploadFileToS3($resume, JobApplication::FOLDER)
                    ];
                }
                $jobApplication->resumes()->createMany($resumes);
            }
            DB::commit();
            return $this->responseSuccess($jobApplication, 'success');

        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'storeJobApplication');
        }
    }

    public function updateJobApplication(JobApplicationRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $jobApplication = JobApplication::query()->findOrFail($id);
            $jobApplication->update([
                'full_name' => $request->get('full_name'),
                'email' => $request->get('email'),
                'job_id' => $request->get('job_id'),
                'stage_id' => $request->get('stage_id'),
            ]);

            if ($request->hasFile('resumes')) {
                $resumes = [];
                $s3 = new S3Service();
                foreach ($request->file('resumes') as $resume) {
                    $resumes[] = [
                        'name' => $resume->getClientOriginalName(),
                        'resume' => $s3->uploadFileToS3($resume, JobApplication::FOLDER),
                    ];
                }
                $jobApplication->resumes()->createMany($resumes);
            }
            DB::commit();
            return $this->responseSuccess($jobApplication, 'success');

        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'updateJobApplication');
        }
    }

    public function deleteJobApplication($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $jobApplication = JobApplication::query()->findOrFail($id);
            $resumes = $jobApplication->resumes()->get()->pluck('id');
            if ($resumes) {
                foreach ($resumes as $resume) {
                    $this->deleteResume($resume);
                }
            }
            $jobApplication->delete();
            DB::commit();
            return $this->responseSuccess($jobApplication, 'success');

        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), "deleteJobApplication");
        }
    }

//    public function getResume($id){
//        $service = new S3Service();
//        $query=JobApplication::with('resumes')->findOrFail($id);
//        $resumes=[];
//        foreach($query->resumes as $resume){
//            $resumes[]=$service->getPrivateFile($resume->resume);
//        }
//        return $resumes;
//    }

    public function deleteResume($resumeId): JsonResponse
    {
        $s3 = new S3Service();
        try {
            $jobApplicationResume = JobApplicationResume::query()->findOrFail($resumeId);
            $s3->removeFile($jobApplicationResume->resume);
            $jobApplicationResume->delete();
            return $this->responseSuccess($jobApplicationResume, 'success');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'deleteResume');
        }
    }

    public function uploadResume($request): JsonResponse
    {
        $s3 = new S3Service();
        $now = Carbon::now();
        $listJobApplicationResume = [];
        try {
            $jobApplicationId = $request->input('job_application_id');
            $resumes = $request->file('resumes');
            foreach ($resumes as $resume) {
                $listJobApplicationResume[] = [
                    'job_application_id' => $jobApplicationId,
                    'name' => $resume->getClientOriginalName(),
                    'resume' => $s3->uploadFileToS3($resume, JobApplication::FOLDER),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            return $this->responseSuccess(DB::table('job_application_resumes')->insert($listJobApplicationResume), 'success');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 'uploadResume');
        }
    }

    public function changeStage($id, $request): JsonResponse
    {
        try {
            //update stage id
            DB::beginTransaction();
            $jobApplication = JobApplication::query()->findOrFail($id);
            $jobApplication->update(['stage_id' => $request->get('stage_id')]);

            //check interview
            if ($request->get('interview')) {
                $interview = $request->get('interview');
                $interview['job_application_id'] = $jobApplication->id;
                InterviewSchedule::create($interview);
            }
            DB::commit();
            //check  email (title, cc, files, template_files, from, to)
            if ($request->get('send_email')) {
                $data = $request->email;
                //check if this stage id have file on email+interview template
                $stage = JobStage::query()->findOrFail($request->get('stage_id'));
                $templateFiles = $stage->interviewTemplate?->files ?? $stage->emailTemplate?->files;
                $temp = [];
                foreach ($templateFiles as $file) {
                    if (in_array($file->id, $data['template_files'])) {
                        $temp[] = $file->url;
                    }
                }

                $data['template_files'] = $temp;
                $data['to'] = $jobApplication->email;
                Mail::send(new SendMail($data));
            }
            return $this->responseSuccess($data ?? $jobApplication, 'success');

        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'ChangeStatusJobApplication');
        }
    }
}
