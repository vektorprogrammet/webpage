<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="work_history")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\WorkHistoryRepository")
 */
class WorkHistory implements GroupMemberInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workHistories")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $startSemester;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $endSemester;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deletedTeamName;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="workHistories")
     * @ORM\JoinColumn(onDelete="SET NULL")
     **/
    protected $team;

    /**
     * @ORM\ManyToOne(targetEntity="Position")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", onDelete="SET NULL")
     * @Assert\Valid
     **/
    protected $position;

    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return WorkHistory
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set team.
     *
     * @param \AppBundle\Entity\Team $team
     *
     * @return WorkHistory
     */
    public function setTeam(\AppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team.
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set position.
     *
     * @param \AppBundle\Entity\Position $position
     *
     * @return WorkHistory
     */
    public function setPosition(\AppBundle\Entity\Position $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * Set startSemester.
     *
     * @param \AppBundle\Entity\Semester $startSemester
     *
     * @return WorkHistory
     */
    public function setStartSemester(\AppBundle\Entity\Semester $startSemester = null)
    {
        $this->startSemester = $startSemester;

        return $this;
    }

    /**
     * Get startSemester.
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getStartSemester()
    {
        return $this->startSemester;
    }

    /**
     * Set endSemester.
     *
     * @param \AppBundle\Entity\Semester $endSemester
     *
     * @return WorkHistory
     */
    public function setEndSemester(\AppBundle\Entity\Semester $endSemester = null)
    {
        $this->endSemester = $endSemester;

        return $this;
    }

    /**
     * Get endSemester.
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    /**
     * @param Semester $semester
     *
     * @return bool
     */
    public function isActiveInSemester(Semester $semester)
    {
        $semesterStartLaterThanWorkHistory = $semester->getSemesterStartDate() >= $this->getStartSemester()->getSemesterStartDate();
        $semesterEndsBeforeWorkHistory = $this->getEndSemester() === null || $semester->getSemesterEndDate() <= $this->getEndSemester()->getSemesterEndDate();

        return $semesterStartLaterThanWorkHistory && $semesterEndsBeforeWorkHistory;
    }

    /**
     * @return string
     */
    public function getTeamName(): string
    {
        if ($this->deletedTeamName !== null) {
            return $this->deletedTeamName;
        }

        return $this->team->getName();
    }

    /**
     * @param string $deletedTeamName
     */
    public function setDeletedTeamName(string $deletedTeamName)
    {
        $this->deletedTeamName = $deletedTeamName;
    }
}
