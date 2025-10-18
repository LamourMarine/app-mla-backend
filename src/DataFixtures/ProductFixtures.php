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
                'Légumes' => ['Carottes', 'Pommes de terre', 'Poireaux', 'Salades'],
                'Fruits' => ['Pommes', 'Poires', 'Fraises', 'Rhubarbe'],
                'Produits laitiers' => ['Lait', 'Yaourt nature', 'Yaourt aux fruits', 'Fromage'],
            ];

            $imagesMap = [
                'Pommes' => '/images/fruits/pommes.jpg',
                'Fraises' => '/images/fruits/fraises.jpg',
                'Poires' => '/images/fruits/poires.jpg',
                'Carottes' => '/images/legumes/carottes.jpg',
                'Poireaux' => '/images/legumes/poireaux.jpg',
                'Salades' =>'/images/legumes/salades.jpg',
                'Pommes de terre' => '/images/legumes/pommes_de_terre.jpg',
                'Fromage' => '/images/produits_laitiers/fromage.jpg',
                'Yaourt nature' => '/images/produits_laitiers/yaourt_nature.jpg',
                'Yaourt aux fruits' => '/images/produits_laitiers/yaourt_aux_fruits.jpg',
                'Rhubarbe' => '/images/fruits/rhubarbe.jpg',
                'Lait' => '/images/produits_laitiers/lait.jpg'
                
            ];

            $categoryToUnit = [
                'Légumes' => 'kg',
                'Fruits' => 'kg',
                'Produits laitiers' => 'L',
            ];

        for ($i = 1; $i <= 50; $i++) {
            $product = new Product();
            
            $categorie = $faker->randomElement($categories);
            $nomProduit = $faker->randomElement($produits[$categorie]);

            $product->setName($nomProduit);
            $product->setDescriptionProduct($faker->sentence());
            $product->setIsBio($faker->boolean(70));
            $product->setPrice($faker->randomFloat(2, 1.50, 15));
            $product->setAvailability($faker->boolean(90));
            $this->addReference(self::PRODUCT_REFERENCE_PREFIX . $i , $product);


            $product->setImageProduct($imagesMap[$nomProduit] ?? '/images/default.jpg');
            // Récupération de la catégorie créée dans CategoryFixtures
            $product->setCategory(
                $this->getReference('category_' . strtolower(str_replace(' ', '_', $categorie)), Category::class)
            );

            $unitName = $categoryToUnit[$categorie];
            $product->setUnit(
                $this->getReference(UnitFixtures::UNIT_REFERENCE_PREFIX . $unitName, Unit::class)
            );
            $producteurIndex = $faker->numberBetween(1, 10);
            $product->setSeller($this->getReference(UserFixtures::PRODUCTEUR_REFERENCE_PREFIX . $producteurIndex, user::class));
            $manager->persist($product);
        }
         $manager->flush();
    }

}