<?php

namespace App\DataFixtures;

use App\Entity\CustomerOrder;
use App\Entity\User;
use App\Entity\OrderLine;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerOrderFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++) {
            $order = new CustomerOrder();
            $order->setNumber('CMD-' . date('Y') . '-' . str_pad($i, 6, '0', STR_PAD_LEFT));
            $orderDate = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-6 months', 'now')
            );
            $order->setOrderAt($orderDate);
            $manager->persist($order);
      
            // Récupérer un client aléatoire
            $customerIndex = $faker->numberBetween(1, 10);
            $order->setCustomer(
                $this->getReference(UserFixtures::CLIENT_REFERENCE_PREFIX . $customerIndex, User::class)
            );

        $nbProducts = $faker->numberBetween(1, 5);
    $total = 0;

    for ($j = 0; $j < $nbProducts; $j++) {
        // Récupère un produit aléatoire créé dans ProductFixtures
        $productIndex = $faker->numberBetween(1, 10);
        $product = $this->getReference(ProductFixtures::PRODUCT_REFERENCE_PREFIX . $productIndex, Product::class);
        $quantity = $faker->numberBetween(1, 3);

        $orderLine = new OrderLine();
        $orderLine->setOrderRef($order);
        $orderLine->setProduct($product);
        $orderLine->setQuantity($quantity);

        $manager->persist($orderLine);
        // Calcul du total
        $total += (float) $product->getPrice();
    }

    // Assigner le total
    $order->setTotal(number_format($total, 2, '.', ''));

    $manager->persist($order);
}

$manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProductFixtures::class,
        ];
    }
}