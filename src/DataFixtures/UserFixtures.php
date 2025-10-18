<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{

    public const ADMIN_REFERENCE = 'admin-user';
    public const PRODUCTEUR_REFERENCE_PREFIX = 'producteur-';
    public const STRUCTURE_REFERENCE_PREFIX = 'structure-';
    public const CLIENT_REFERENCE_PREFIX = 'client_';


    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. USERS

        // Admin 
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName('Administrateur Général');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setAddress($faker->address);
        $admin->setPhoneNumber($faker->numerify('06########'));
        $manager->persist($admin);
        $this->addReference(self::ADMIN_REFERENCE, $admin); 

        // Producteurs
        for ($i = 1; $i <= 10; $i++) {
            $producteur = new User();
            $producteur->setEmail($faker->email);
            $producteur->setRoles(['ROLE_PRODUCTEUR']);
            $producteur->setName($faker->company());
            $producteur->setPassword($this->passwordHasher->hashPassword($producteur, 'prod123'));
            $producteur->setAddress($faker->address);
            $producteur->setPhoneNumber($faker->numerify('06########'));
            $producteur->setPhoto('/images/producers/producteur_' .$i . '.jpg');
            $manager->persist($producteur);
            $this->addReference(self::PRODUCTEUR_REFERENCE_PREFIX . $i, $producteur);
        }

        // STructures
        for ($i = 1; $i <= 10; $i++) {
            $structure = new User();
            $structure->setEmail($faker->email);
            $structure->setRoles(['ROLE_STRUCTURE']);
            $structure->setName($faker->company());
            $structure->setPassword($this->passwordHasher->hashPassword($producteur, 'struct123'));
            $structure->setAddress($faker->address);
            $structure->setPhoneNumber($faker->numerify('06########'));
            $manager->persist($structure);
            $this->addReference(self::STRUCTURE_REFERENCE_PREFIX . $i, $structure);
            $this->addReference(self::CLIENT_REFERENCE_PREFIX . $i, $structure);
            
        }

        $manager->flush();
    }
}