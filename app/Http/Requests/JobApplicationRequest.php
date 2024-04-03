<?php

namespace App\Http\Requests;

use App\Models\JobStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobApplicationRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $stageIds = JobStage::query()->where('job_id', $this->job_id)->get()->pluck('id')->toArray();

        return [
            'full_name' => 'required',
            'email' => 'email|required',
            'job_id' => 'required|exists:jobs,id',
//            'candidate_source_id' => 'required',
//            'cv' => 'required|array',
//            'cv.*' => 'required|file',
            'stage_id' => ['nullable', Rule::in($stageIds)]
        ];
    }
}
