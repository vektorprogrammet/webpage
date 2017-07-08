<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Form\Type\SchoolCapacityEditType;
use AppBundle\SchoolAllocation\Assistant;
use AppBundle\SchoolAllocation\School;
use AppBundle\SchoolAllocation\Allocation;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityType;

class SchoolAllocationController extends Controller
{
    public function showVueAction()
    {
        return $this->render('school_allocation/index.html.twig');
    }

    public function createAction(Request $request)
    {
        $user = $this->getUser();
        $department = $user->getDepartment();
	    $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $form = $this->createForm(new SchoolCapacityType(), $schoolCapacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schoolCapacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_create.html.twig', array(
            'message' => '',
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, SchoolCapacity $capacity)
    {
        $form = $this->createForm(new SchoolCapacityEditType(), $capacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($capacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_edit.html.twig', array(
            'capacity' => $capacity,
            'form' => $form->createView(),
        ));
    }

    public function getName()
    {
        return 'SchoolAllocation'; // This must be unique
    }
}
