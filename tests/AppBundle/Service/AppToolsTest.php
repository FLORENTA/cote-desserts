<?php

namespace tests\Service;

use AppBundle\Service\AppTools;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class AppToolsTest
 * @package tests\Service
 */
class AppToolsTest extends KernelTestCase
{
    /** @var AppTools $appTools */
    private $appTools;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->appTools = $container->get(AppTools::class);
    }

    public function testSlugify(): void
    {
        $this->assertEquals("l-entremets-au-chocolat", $this->appTools->slugify("L'entremets au chôcolat"));
        $this->assertEquals("la-tarte-au-citron", $this->appTools->slugify("La Tarte au Citron"));
        $this->assertEquals("foret-noire", $this->appTools->slugify("Forêt Noire"));
        $this->assertEquals("saint-honore", $this->appTools->slugify("Saint-Honoré"));
        $this->assertEquals("mille-feuilles-vanille", $this->appTools->slugify("Mille-feuilles Vanille"));
    }
}