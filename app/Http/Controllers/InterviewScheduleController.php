<?php

namespace App\Http\Controllers;

use App\Models\InterviewSchedule;
use App\Services\InterviewScheduleService;
use Illuminate\Http\Request;

class InterviewScheduleController extends Controller
{
    public function __construct()
    {
        $this->service = new InterviewScheduleService();
    }
    public function getListInterviewSchedule(Request $request){
        return $this->service->getListInterviewSchedule($request);
    }

    public function getInterviewScheduleByJobId(Request $request, $id){
        return $this->service->getListInterviewScheduleByJobId($request, $id);
    }

    public function getAllInterviewSchedule(){
        return $this->service->getAllInterviewSchedule();
    }

    public function storeInterviewSchedule(Request $request){
        return $this->service->store($request);
    }
}
