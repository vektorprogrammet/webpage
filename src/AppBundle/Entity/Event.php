<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 */
class Event
{
    ## TODO : RENAME THIS "EVENT" confusing name




    // // // // // // // --------------- // // // // // // //
    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->getFieldOfStudy()->getDepartment();
    }

    public function setDepartment($department): void
    {
        $this->department = $department;
    }


    // // // // // // // --------------- // // // // // // //


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $description;


    /**
     * @ORM\Column(type="datetime")
     */
    private $start_time;


    /**
     * @ORM\Column(type="datetime")
     */
    private $end_time;

    /**
     * Constructor.
     */
    public function __construct()
    {

    }

    ## TODO: Burde det være en location?

    ## TODO: Legg til i kalender?

    ## TODO: Stand event?

    ## TODO: E-mail event.

    ## TODO : ICAL SUPPORT.

    ## TODO : LEGG TIL HVEM SOM SKAL INVITERES

    ## TODO : LISTE FOR PÅMELDE

    ## TODO: BILDE

    ## TODO: OVERSIKT OVER PÅMELDTE OG IKKE PÅMELDTE
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param \DateTime $start_time
     */
    public function setStartTime($start_time): void
    {
        $this->start_time = $start_time;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param \DateTime $end_time
     */
    public function setEndTime($end_time): void
    {
        $this->end_time = $end_time;
    }






}
