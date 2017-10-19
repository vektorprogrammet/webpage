<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageGalleryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImageGalleryController extends Controller
{
    public function createAction(Request $request)
    {
        $imageGallery = new ImageGallery();

        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            //$this->get('event_dispatcher')->dispatch(ImageGallery::CREATED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_show_individual', array('id' => $imageGallery->getId()));
        }

        $imageGalleries = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findAll();

        return $this->render('image_gallery/show_overview.html.twig', array(
            'image_galleries' => $imageGalleries,
            'form' => $form->createView(),
        ));
    }

    public function showIndividualAction(ImageGallery $imageGallery)
    {
        return $this->render('image_gallery/show_individual.html.twig', array(
            'image_gallery' => $imageGallery,
        ));
    }
}
