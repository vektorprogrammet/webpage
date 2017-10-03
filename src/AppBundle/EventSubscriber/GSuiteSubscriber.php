<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\TeamEvent;
use AppBundle\Event\UserEvent;
use AppBundle\Event\WorkHistoryEvent;
use AppBundle\Google\GoogleAPI;
use AppBundle\Service\CompanyEmailMaker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GSuiteSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $googleAPI;
    private $emailMaker;

    public function __construct(LoggerInterface $logger, GoogleAPI $googleAPI, CompanyEmailMaker $emailMaker)
    {
        $this->logger = $logger;
        $this->googleAPI = $googleAPI;
        $this->emailMaker = $emailMaker;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            WorkHistoryEvent::CREATED => array(
                array('createGSuiteUser', 0),
            ),
            WorkHistoryEvent::EDITED  => array(
                array('updateGSuiteUser', 0),
            ),
            UserEvent::EDITED => array(
                array('updateGSuiteUser', 0),
            ),
            UserEvent::COMPANY_EMAIL_EDITED  => array(
                array('updateGSuiteUser', 0),
            ),
            TeamEvent::CREATED => array(
                array('createGSuiteTeam', 0),
            ),
            TeamEvent::EDITED => array(
                array('editGSuiteTeam', 0),
            )
        );
    }

    public function updateGSuiteUser(UserEvent $event)
    {
        $user = $event->getUser();
        $oldEmail = $event->getOldEmail();
        if ($oldEmail) {
            $this->googleAPI->updateUser($oldEmail, $user);
            $this->logger->info("G Suite account for {$user} with email {$user->getCompanyEmail()} has been updated.");
        }
    }

    public function createGSuiteUser(WorkHistoryEvent $event)
    {
        $user = $event->getWorkHistory()->getUser();
        if (!$user->getCompanyEmail()) {
            $googleUsers = $this->googleAPI->getUsers();
            $emailsInUse = array_map(function ($googleUser) {
                return $googleUser->primaryEmail;
            }, $googleUsers);

            $email = $this->emailMaker->setCompanyEmailFor($user, $emailsInUse);

            if ($email !== null) {
                $this->googleAPI->createUser($user);
                $this->logger->info("New G Suite account created for {$user} with email {$user->getCompanyEmail()}");
            }
        }
    }

    public function createGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $teamExists = $this->googleAPI->getGroup($team->getEmail()) !== null;

        if (!$teamExists) {
            $this->googleAPI->createGroup($team);
            $this->logger->info("New G Suite group created for {$team}");
        }
    }

    public function editGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $oldEmail = $event->getOldTeamEmail();
        $teamExists = $this->googleAPI->getGroup($team->getEmail()) !== null;

        if (!$teamExists) {
            $this->googleAPI->createGroup($team);
            $this->logger->info("New G Suite group created for {$team}");
        } else {
            $this->googleAPI->updateGroup($oldEmail, $team);
            $this->logger->info("G Suite group for {$team} has been updated");
        }
    }
}
