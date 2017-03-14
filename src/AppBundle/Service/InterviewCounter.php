<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\InterviewDistribution;
use AppBundle\Entity\Semester;

class InterviewCounter
{
    const YES = 'Ja';
    const MAYBE = 'Kanskje';
    const NO = 'Nei';

    /**
     * @param Application[] $applications
     * @param string        $suitable
     *
     * @return int
     */
    public function count(array $applications, string $suitable)
    {
        $count = 0;

        foreach ($applications as $application) {
            $interview = $application->getInterview();
            if ($interview === null) {
                continue;
            }

            $suitableAssistant = $interview->getInterviewScore()->getSuitableAssistant();
            if ($suitableAssistant === $suitable) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @param Application[] $applications
     * @param Semester      $semester
     *
     * @return array
     */
    public function createInterviewDistributions(array $applications, Semester $semester)
    {
        $interviewDistributions = array();

        foreach ($applications as $application) {
            $interviewer = $application->getInterview()->getInterviewer();

            if (!array_key_exists($interviewer->__toString(), $interviewDistributions)) {
                $interviewDistributions[$interviewer->__toString()] = new InterviewDistribution($interviewer, $semester);
            }
        }

        return array_values($interviewDistributions);
    }
}
