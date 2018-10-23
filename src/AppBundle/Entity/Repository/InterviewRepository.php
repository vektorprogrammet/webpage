<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Interview;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Type\InterviewStatusType;
use Doctrine\ORM\EntityRepository;

/**
 * InterviewRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InterviewRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @param Semester $semester
     *
     * @return Interview
     */
    public function findLastScheduledByUserInSemester(User $user, Semester $semester)
    {
        $result = $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->where('interview.interviewer = :user')
            ->setParameter('user', $user)
            ->andWhere('application.semester = :semester')
            ->setParameter('semester', $semester)
            ->andWhere('interview.lastScheduleChanged IS NOT NULL')
            ->orderBy('interview.lastScheduleChanged', 'DESC')
            ->getQuery()
            ->getResult();

        return !empty($result) ? $result[0] : null;
    }
    public function findAllInterviewedInterviewsBySemester($semester)
    {
        $interviews = $this->getEntityManager()->createQuery('
		SELECT interview
		FROM AppBundle:Interview interview
		JOIN AppBundle:Application app
		WITH interview.application = app
		JOIN AppBundle:ApplicationStatistic appStat
		WITH app.statistic = appStat
		WHERE interview.interviewed = 1
		AND appStat.semester = :semester
		')
            ->setParameter('semester', $semester)
            ->getResult();

        return $interviews;
    }

    /**
     * @param User     $user
     * @param Semester $semester
     *
     * @return int
     */
    public function numberOfInterviewsByUserInSemester(User $user, Semester $semester)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT COUNT(i)
        FROM AppBundle:Interview i
        JOIN AppBundle:Application a
        WITH a.interview = i
        WHERE i.interviewer = ?1
        AND a.semester = ?2
        AND a.previousParticipation = 0
        ')
            ->setParameter(1, $user)
            ->setParameter(2, $semester);

        return $query->getSingleScalarResult();
    }

    public function findLatestInterviewByUser(User $user)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT i
        FROM AppBundle:Interview i
        WHERE i.user = ?1
        ORDER BY i.conducted ASC
        ')
            ->setParameter(1, $user)
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $responseCode
     *
     * @return Interview
     */
    public function findByResponseCode(string $responseCode)
    {
        return $this->createQueryBuilder('interview')
            ->where('interview.responseCode = :responseCode')
            ->setParameter('responseCode', $responseCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $interviewer
     *
     * @return Interview[]
     */
    public function findUncompletedInterviewsByInterviewerInCurrentSemester(User $interviewer)
    {
        $semester = $interviewer->getDepartment()->getCurrentSemester();
        if ($semester === null) {
            return [];
        }

        return $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->where('application.semester = :semester')
            ->setParameter('semester', $semester)
            ->andWhere('interview.interviewer = :interviewer OR interview.coInterviewer = :interviewer')
            ->andWhere('interview.interviewed = false')
            ->setParameter('interviewer', $interviewer)
            ->orderBy('interview.scheduled')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Semester $semester
     *
     * @return User[]
     */
    public function findInterviewersInSemester(Semester $semester)
    {
        /**
         * @var $interviews Interview[]
         */
        $interviews = $this->createQueryBuilder('interview')
                    ->join('interview.application', 'application')
                    ->where('application.semester = :semester')
                    ->setParameter('semester', $semester)
                    ->getQuery()
                    ->getResult();
        $interviewers = [];
        foreach ($interviews as $interview) {
            $interviewers[] = $interview->getInterviewer();
            if ($interview->getCoInterviewer()) {
                $interviewers[] = $interview->getCoInterviewer();
            }
        }

        return array_unique($interviewers);
    }

    /**
     * Find interviews which will receive accept-interview notifications.
     * All interviews scheduled to a time after $time and having PENDING
     * interview status apply.
     *
     * @param \DateTime $time
     *
     * @return array
     */
    public function findAcceptInterviewNotificationRecipients(\DateTime $time)
    {
        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.scheduled > :time')
            ->andWhere('i.interviewStatus = :status')
            ->setParameter('time', $time)
            ->setParameter('status', InterviewStatusType::PENDING)
            ->getQuery()
            ->getResult();
    }
}
