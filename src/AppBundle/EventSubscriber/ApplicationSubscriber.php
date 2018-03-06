<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Service\ApplicationData;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\NewsletterManager;
use AppBundle\Service\SlackMessenger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;
    private $slackMessenger;
    private $applicationData;
    private $router;
    private $newsletterManager;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param MailerInterface   $mailer
     * @param \Twig_Environment $twig
     * @param Session           $session
     * @param LoggerInterface   $logger
     * @param SlackMessenger    $slackMessenger
     * @param ApplicationData   $applicationData
     * @param RouterInterface   $router
     * @param NewsletterManager $newsletterManager
     */
    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, Session $session, LoggerInterface $logger, SlackMessenger $slackMessenger, ApplicationData $applicationData, RouterInterface $router, NewsletterManager $newsletterManager)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->slackMessenger = $slackMessenger;
        $this->applicationData = $applicationData;
        $this->router = $router;
        $this->newsletterManager = $newsletterManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ApplicationCreatedEvent::NAME => array(
                array('logApplication', 1),
                array('sendConfirmationMail', 0),
                array('addFlashMessage', -1),
                array('subscribeToCheckedNewsletter', -2),
            ),
        );
    }

    public function subscribeToCheckedNewsletter(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $department = $application->getUser()->getDepartment();
        $fullName = $application->getUser()->getFullName();
        $email = $application->getUser()->getEmail();
        $this->newsletterManager->subscribeToCheckedNewsletter($department, $fullName, $email);
    }

    public function sendConfirmationMail(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();

        $template = 'admission/admission_email.html.twig';
        if ($application->getUser()->hasBeenAssistant()) {
            $template = 'admission/admission_existing_email.html.twig';
        }

        // Send a confirmation email with a copy of the application
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Søknad - Vektorassistent')
            ->setFrom(array('rekruttering@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($application->getUser()->getEmail())
            ->setBody($this->twig->render($template, array('application' => $application)));

        $this->mailer->send($emailMessage);

        $this->logger->info("Application confirmation mail sent to {$application->getUser()->getEmail()}");
    }

    public function addFlashMessage(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $message = "Søknaden din er registrert. En kvittering har blitt sendt til {$application->getUser()->getEmail()}. Lykke til!";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function logApplication(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();

        $user = $application->getUser();
        $department = $user->getDepartment();
        $this->applicationData->setDepartment($department);

        $this->logger->info("$department: New application from *$user* registered. $department has *{$this->applicationData->getApplicationCount()}* applicants");

        $this->slackMessenger->notify("$department: Ny søknad fra *$user* registrert. $department har nå *{$this->applicationData->getApplicationCount()}* søkere: "
            .$this->router->generate('applications_show_by_semester', array('id' => $department->getCurrentOrLatestSemester()->getId()), RouterInterface::ABSOLUTE_URL));
    }
}
