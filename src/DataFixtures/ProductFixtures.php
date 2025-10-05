<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Unit;
use App\DataFixtures\UserFixtures;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PRODUCT_REFERENCE_PREFIX = 'produit_';

    public function getDependencies(): array
    {
        return [UserFixtures::class,
                CategoryFixtures::class,
                UnitFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

          $categories = ['Légumes', 'Fruits', 'Produits laitiers'];
            $produits = [
                'Légumes' => ['Tomates', 'Carottes', 'Pommes de terre'],
                'Fruits' => ['Pommes', 'Poires', 'Fraises'],
                'Produits laitiers' => ['Lait', 'Yaourt', 'Fromage'],
            ];

        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            
            $categorie = $faker->randomElement($categories);
            $nomProduit = $faker->randomElement($produits[$categorie]);

            $product->setName($nomProduit);
            $product->setDescriptionProduct($faker->sentence());
            $product->setImageProduct($faker->url());
            $product->setIsBio($faker->boolean(70));
            $product->setPrice($faker->randomFloat(2, 1.50, 15));
            $product->setAvailability($faker->boolean(90));
            $this->addReference(self::PRODUCT_REFERENCE_PREFIX . $i , $product);


            // Récupération de la catégorie créée dans CategoryFixtures
            $product->setCategory(
                $this->getReference('category_' . strtolower(str_replace(' ', '_', $categorie)), Category::class)
            );

            $product->setUnit(
                $this->getReference(UnitFixtures::UNIT_REFERENCE_PREFIX . $faker->randomElement(UnitFixtures::UNIT), Unit::class)
            );

            $producteurIndex = $faker->numberBetween(1, 10);
            $product->setSeller($this->getReference(UserFixtures::PRODUCTEUR_REFERENCE_PREFIX . $producteurIndex, user::class));
            $manager->persist($product);
        }
         $manager->flush();
    }

}