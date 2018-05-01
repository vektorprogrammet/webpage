<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends Controller
{
    public function showAction($id)
    {
        $team = $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);

        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
        ));
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }
}
