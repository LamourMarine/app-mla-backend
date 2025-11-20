<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CategoryFixtures extends Fixture 
{
    public const CATEGORIES = ['Entrées', 'Légumes', 'Viandes', 'Produits laitiers', 'Fruits', 'Féculents', 'Épicerie'];


    public function load(ObjectManager $manager):void
    {
        foreach(self::CATEGORIES as $catName) {
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);

            $this->addReference('category_' . strtolower(str_replace(' ', '_', $catName)), $category);
        }

        $manager->flush();
    }

}