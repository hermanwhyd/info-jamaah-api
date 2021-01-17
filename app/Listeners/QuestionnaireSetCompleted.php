<?php

namespace App\Listeners;

use App\Events\QuestionnaireWasCompleted;
use App\Jobs\QuestionnaireSetCompletedJob;

class QuestionnaireSetCompleted
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\QuestionnaireWasCompleted  $event
     * @return void
     */
    public function handle(QuestionnaireWasCompleted $event)
    {
        dispatch(new QuestionnaireSetCompletedJob($event->evaluation, $event->questionnaire));
    }
}
