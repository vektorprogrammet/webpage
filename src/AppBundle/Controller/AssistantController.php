<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends Controller
{
    /**
     * @Route("/opptak")
     * @Route("/opptak/avdeling/{id}", name="admission_show_specific_department",
     *     requirements={"id"="\d+"})
     * @Route("/opptak/{shortName}", name="admission_show_by_short_name",
     *     requirements={"shortName"="\w+"})
     * @Route("/avdeling/{shortName}", name="admission_show_specific_department_by_name",
     *     requirements={"shortName"="\w+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     */
    public function admissionAction(Request $request, Department $department = null)
    {
        return $this->indexAction($request, $department, true);
    }

    /**
     * @param Request $request
     * @param Department $specificDepartment
     * @param bool $scrollToAdmissionForm
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Department $specificDepartment = null, $scrollToAdmissionForm = false)
    {
        $admissionManager = $this->get('app.application_admission');
        $em = $this->getDoctrine()->getManager();
        $departments = $em->getRepository('AppBundle:Department')->findAll();
        if (null === $specificDepartment) {
            $specificDepartment = $this->get('app.geolocation')->findNearestDepartment($departments);
        }

        $teams = $em->getRepository('AppBundle:Team')->findByOpenApplicationAndDepartment($specificDepartment);

        $application = new Application();

        $formViews = array();

        /** @var Department $department */
        foreach ($departments as $department) {
            $form = $this->get('form.factory')->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
                'validation_groups' => array('admission'),
                'departmentId' => $department->getId(),
                'environment' => $this->get('kernel')->getEnvironment(),
            ))->getForm();

            if ($form->isSubmitted()) {
                $scrollToAdmissionForm = true;
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $admissionManager->setCorrectUser($application);

                if ($application->getUser()->hasBeenAssistant()) {
                    return $this->redirectToRoute('admission_existing_user');
                }

                $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);
                $application->setSemester($semester);
                $em->persist($application);
                $em->flush();

                $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

                return $this->redirectToRoute('application_confirmation');
            }

            $formViews[$department->getCity()] = $form->createView();
        }


        return $this->render('assistant/assistants.html.twig', array(
            'specific_department' => $specificDepartment,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ));
    }

    /**
     * @Route("/assistenter/opptak/bekreftelse", name="application_confirmation")
     * @return Response
     */
    public function confirmationAction()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }
}
