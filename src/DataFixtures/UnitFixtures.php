<?php

namespace App\DataFixtures;

use App\Entity\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UnitFixtures extends Fixture 
{
    public const UNIT = ['kg', 'L'];
    public const UNIT_REFERENCE_PREFIX = 'unit_';

    public function load(ObjectManager $manager): void 
    {
        foreach(self::UNIT as $unitName) {

            $unit = new Unit();
            $unit->setName($unitName);
            $manager->persist($unit);

            $this->addReference(self::UNIT_REFERENCE_PREFIX . $unitName, $unit);
        }

        $manager->flush();
    }
}
