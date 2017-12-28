<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Image;
use AppBundle\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ImageSubscriber implements EventSubscriber
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::preRemove,
            Events::prePersist,
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Image) {
            $path = $entity->getPath();
            $this->fileUploader->deleteGalleryImage($path);
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Image) {
            $file = $entity->getUploadedFile();
            if ($file) {
                $path = $this->fileUploader->uploadGalleryImage($file);
                $entity->setPath($path);
            }
        }
    }
}
