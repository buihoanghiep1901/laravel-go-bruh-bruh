<?php

namespace App\Http\Requests;

use App\Models\JobApplication;
use App\Models\JobStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class changeStageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $stages=JobApplication::findOrFail($this->id)->job->jobStages->pluck('id')->toArray();
        $rule=[
            'stage_id'=>['required', Rule::in($stages)],
            'send_email'=>['required', Rule::in([0, 1])],
            'email' => ['required_if:send_mail,1', 'array'],
            'email.title'=>['required_if:send_email,1','string'],
            'email.cc'=>['required_if:send_email,1', 'array'],
            'email.content'=>['required_if:send_email,1','string'],
            'email.files'=>['nullable', 'array'],
            'email.template_files'=>['nullable', 'array'],
        ];

        $jobStage = JobStage::query()->where('id', $this->stage_id)->first();
        if ($jobStage?->interview_template_id) {
            $interviewRule = [
                'interview' => ['nullable', 'array'],
                'interview.schedule_date' => ['required_with:interview', 'date_format:Y/m/d H:i:s'],
//                'interview.members' => 'required_with:interview|array',
//                'interview.members.user' => 'nullable|array',
//                'interview.members.user.*' => 'nullable|integer|exists:users,id',
//                'interview.members.group' => 'nullable|array',
//                'interview.members.group.*' => 'nullable|integer|exists:mention_groups,id',
            ];
        }
        return array_merge($rule, $interviewRule ?? []);

    }
}
