<?php

namespace App\Services;

use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplicationMention;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InterviewScheduleService extends BaseService
{

    protected function setModel(): void
    {
        $this->model = new InterviewSchedule();
    }

    public function getListInterviewSchedule(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = InterviewSchedule::with(['jobApplication.job:id,name', 'employees:id,name']);
//        if ($user->role_name != 'admin') {
//            $mentionGroupIds = array_column($user->mentionGroups?->toArray() ?? [], 'id');
//            $jobIds = Job::query()->where('created_by', $user->id)
//                ->orWhereHas('mentionUsers', function ($q) use ($user) {
//                    $q->where('users.id', $user->id);
//                })
//                ->orWhereHas('mentionGroups', function ($q) use ($mentionGroupIds) {
//                    $q->whereIn('mention_groups.id', $mentionGroupIds);
//                })
//                ->pluck('id')
//                ->toArray();
//
//            $query->whereHas('jobApplication', function ($q) use ($user, $mentionGroupIds, $jobIds) {
//                $q->whereIn('job_id', $jobIds)
//                    ->orWhereHas('mentionUsers', fn($qr) => $qr->where('users.id', $user->id))
//                    ->orWhereHas('mentionGroups', fn($qr) => $qr->whereIn('mention_groups.id', $mentionGroupIds));
//            });
//        }
        if (isset($request->email)) {
            $query->whereHas('jobApplication', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->query('email') . '%');
            });
        }

        if (isset($request->job_id)) {
            $query->whereHas('jobApplication', function ($q) use ($request) {
                $q->where('job_id', $request->query('job_id'));
            });
        }

        if (isset($request->schedule_date)) {
            $query->whereDate('schedule_date', $request->schedule_date);
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->employee)) {
            $query->whereHas('employees', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee . '%');
            });
        }

        $listInterviewSchedule = $this->getPaginateByQuery($query, $request);
        return $this->responseSuccess($listInterviewSchedule, 'success');
    }

    public function getAllInterviewSchedule()
    {
        return $this->responseSuccess(InterviewSchedule::with('jobApplication.job:id,name', 'employees:id,name'), 'success');
    }

    public function getListInterviewScheduleByJobId(Request $request, string $jobId): JsonResponse
    {
        $job = Job::query()->where('id', $jobId)->first();
        if (!$job) {
            return $this->responseSuccess([], 'success');
        }

        $query = InterviewSchedule::query()->select('interview_schedules.*')
            ->whereHas('jobApplication', function ($q) use ($job) {
                $q->where('job_id', $job->id);
            })
            ->with(['jobApplication:id,email,stage_id', 'employees']);
//        $user = Auth::user();
//        $mentionGroupIds = array_column($user->mentionGroups?->toArray() ?? [], 'id');
//        if (
//            $user->role_name != 'admin' && $user->id != $job->created_by &&
//            !in_array($user->id, array_column($job->mentionUsers->toArray(), 'id')) &&
//            !array_intersect($mentionGroupIds, array_column($job->mentionGroups->toArray(), 'id'))
//        ) {
//            $query->whereHas('jobApplication', function ($q) use ($user, $mentionGroupIds, $id) {
//                $q->where('job_id', $id)
//                    ->where(function ($sq) use ($user, $mentionGroupIds) {
//                        $sq->orWhereHas('mentionUsers', fn($qr) => $qr->where('users.id', $user->id))
//                            ->orWhereHas('mentionGroups', fn($qr) => $qr->whereIn('mention_groups.id', $mentionGroupIds));
//                    });
//            });
//        } else {
//            $query->whereHas('jobApplication', function ($q) use ($id) {
//                $q->where('job_id', $id);
//            });
//        }
        if (isset($request->email)) {
            $query->whereHas('jobApplication', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->query('email') . '%');
            });
        }

        if (isset($request->schedule_date)) {
            $query->whereDate('schedule_date', $request->schedule_date);
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->employee)) {
            $query->whereHas('employees', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee . '%');
            });
        }

        $listInterviewSchedule = $this->getPaginateByQuery($query, $request);
        return $this->responseSuccess($listInterviewSchedule, 'success');
    }

    public function store(Request $data): JsonResponse
    {
        try {
            DB::beginTransaction();
            $interviewSchedule = new InterviewSchedule();
            $interviewSchedule->job_application_id = $data['job_application_id'];
            $interviewSchedule->schedule_date = $data['schedule_date'];
            $interviewSchedule->status = $data['status'] ?? 'pending';
            $result = $interviewSchedule->save();
//            ddd($interviewSchedule);
//            dd($data['members'])
            if ($data['members']) {
                $time = now();
                $dataMentions = [];
                foreach ($data['members'] as $key => $mention) {
                    foreach ($mention as $item) {
                        $dataMentions[] = [
                            'job_application_id' => $data['job_application_id'],
                            'type' => $key,
                            'mention_id' => $item,
                            'created_at' => $time,
                            'updated_at' => $time
                        ];
                    }
                }
                if ($dataMentions) JobApplicationMention::query()->insert($dataMentions);
            }
            DB::commit();

            return $this->responseSuccess($dataMentions, 'success');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'getAllInterviewSchedule');
        }
    }
}
