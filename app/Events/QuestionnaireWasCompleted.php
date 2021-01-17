<?php

namespace App\Events;
use App\Models\EvaluationResult;

class QuestionnaireWasCompleted extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $evaluation;
    public $questionnaire;
    
    public function __construct(EvaluationResult $result)
    {
        $this->evaluation = $result->evaluation;
        $this->questionnaire = $result->question->questionnaires()->first();
    }
}
