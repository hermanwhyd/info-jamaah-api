<?php

namespace App\Jobs;
use App\Models\Evaluation;
use App\Models\Questionnaire;

class QuestionnaireSetCompletedJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    
    private $evaluation;
    private $questionnaire;

    public function __construct(Evaluation $evaluation, Questionnaire $questionnaire)
    {
        $this->evaluation = $evaluation;
        $this->questionnaire = $questionnaire;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $questionIds = $this->questionnaire->questions()->pluck('id')->toArray();
        $resultCounter = $this->evaluation->results()->whereIn('question_id', $questionIds)->count();
        
        if(count($questionIds) == $resultCounter) {
            $this->evaluation->questionnaires()->updateExistingPivot($this->questionnaire->id, [
                'completed_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
