<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Newsletter;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNewsletterData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $newsletter = new Newsletter();
        $newsletter->setName('Interesseliste');
        $newsletter->setDepartment($this->getReference('dep-1'));
        $manager->persist($newsletter);

        $newsletter2 = new Newsletter();
        $newsletter2->setName('Testliste');
        $newsletter2->setDepartment($this->getReference('dep-1'));
        $manager->persist($newsletter2);

        $manager->flush();

        $this->addReference('newsletter-interesseliste', $newsletter);
        $this->addReference('newsletter-testliste', $newsletter2);
    }

    public function getOrder()
    {
        return 3;
    }
}
