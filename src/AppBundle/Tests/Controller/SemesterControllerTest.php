<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class SemesterControllerTest extends BaseWebTestCase
{
    public function testSuperadminCreateSemester()
    {
        // ADMIN
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/semesteradmin/opprett');

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createSemester[semesterTime]']->select('Vår');
        $form['createSemester[year]']->select('2017');

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/semesteradmin'));
    }

    public function testShow()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/semesteradmin');

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('td:contains("Vår 2013")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Høst 2015")')->count());
    }
}
