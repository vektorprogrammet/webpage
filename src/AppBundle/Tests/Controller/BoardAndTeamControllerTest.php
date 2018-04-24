<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class BoardAndTeamControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/team');
        $this->assertEquals(1, $crawler->filter('h1:contains("team")')->count());

        $teamOverview = $crawler->filter('.team-overview');
        $tabs = $teamOverview->filter('#team-overview-tabs');
        $this->assertEquals(5, $tabs->filter('a')->count());
        $this->assertGreaterThan(5, $teamOverview->filter('.team-card')->count());
    }

    public function testHideTeam()
    {
        $crawler = $this->goTo('/team#NTNU');
        $numberOfTeamsBefore = $crawler->filter('.team-card')->count();

        $client = self::createAdminClient();
        $crawler = $this->goTo('/kontrollpanel/teamadmin/update/2', $client);
        $form = $crawler->selectButton('Lagre')->form();
        $form['createTeam[isActive]']->untick();
        $client->submit($form);

        $crawler = $this->goTo('/team#NTNU');
        $numberOfTeamsAfter = $crawler->filter('.team-card')->count();

        $this->assertEquals($numberOfTeamsBefore-1, $numberOfTeamsAfter);
    }
}
