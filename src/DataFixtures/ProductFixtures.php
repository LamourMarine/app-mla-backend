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
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            UnitFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $produitsParCategorie = [
            'Entrées' => [
                [
                    'name' => 'Carottes râpées',
                    'unit' => 'kg',
                    'price' => 3.50,
                    'description' => 'Carottes fraîches râpées, idéales pour vos salades. Croquantes et sucrées, cultivées sans pesticides.'
                ],
                [
                    'name' => 'Radis',
                    'unit' => 'kg',
                    'price' => 2.80,
                    'description' => 'Radis roses croquants et piquants, parfaits en apéritif ou en salade. Fraîchement récoltés.'
                ],
                [
                    'name' => 'Salade',
                    'unit' => 'kg',
                    'price' => 4.50,
                    'description' => 'Salade verte tendre et croquante, récoltée le matin même. Idéale pour accompagner tous vos plats.'
                ],
                [
                    'name' => 'Endives',
                    'unit' => 'kg',
                    'price' => 5.20,
                    'description' => 'Endives du Nord, légèrement amères et croquantes. Parfaites en salade ou braisées au jambon.'
                ],
            ],
            'Légumes' => [
                [
                    'name' => 'Carottes',
                    'unit' => 'kg',
                    'price' => 2.30,
                    'description' => 'Carottes fraîches de saison, sucrées et croquantes. Idéales en jus, crues ou cuites.'
                ],
                [
                    'name' => 'Pommes de terre',
                    'unit' => 'kg',
                    'price' => 1.50,
                    'description' => 'Pommes de terre polyvalentes, parfaites pour toutes vos recettes : purée, frites, gratins.'
                ],
                [
                    'name' => 'Epinards',
                    'unit' => 'kg',
                    'price' => 6.50,
                    'description' => 'Jeunes pousses d\'épinards tendres et savoureuses. Riches en fer, délicieux crus ou cuits.'
                ],
                [
                    'name' => 'Poireaux',
                    'unit' => 'kg',
                    'price' => 3.20,
                    'description' => 'Poireaux frais, parfaits pour vos soupes, quiches et gratins. Goût doux et fondant.'
                ],
                [
                    'name' => 'Choux',
                    'unit' => 'kg',
                    'price' => 2.80,
                    'description' => 'Choux verts de nos jardins, savoureux et nutritifs. Idéal pour potées et plats mijotés.'
                ],
                [
                    'name' => 'Navets',
                    'unit' => 'kg',
                    'price' => 2.20,
                    'description' => 'Navets tendres au goût légèrement sucré. Délicieux glacés, en purée ou dans les pot-au-feu.'
                ],
                [
                    'name' => 'Courges',
                    'unit' => 'kg',
                    'price' => 2.50,
                    'description' => 'Courges variées de saison, chair fondante et légèrement sucrée. Parfaites pour soupes et gratins.'
                ],
            ],
            'Viandes' => [
                [
                    'name' => 'Bœuf',
                    'unit' => 'kg',
                    'price' => 18.50,
                    'description' => 'Viande de bœuf de nos élevages locaux. Tendre et savoureuse, élevage en plein air garanti.'
                ],
                [
                    'name' => 'Poulet',
                    'unit' => 'kg',
                    'price' => 12.80,
                    'description' => 'Poulet fermier élevé en plein air, nourri aux céréales. Chair tendre et goûteuse.'
                ],
                [
                    'name' => 'Porc',
                    'unit' => 'kg',
                    'price' => 14.50,
                    'description' => 'Viande de porc de qualité supérieure, élevage traditionnel. Idéale pour rôtis et grillades.'
                ],
            ],
            'Œufs' => [
                [
                    'name' => 'Œufs',
                    'unit' => 'unité',
                    'price' => 0.45,
                    'description' => 'Œufs frais de poules élevées en plein air. Jaune orangé et coquille solide, ramassés quotidiennement.'
                ],
            ],
            'Produits laitiers' => [
                [
                    'name' => 'Lait',
                    'unit' => 'L',
                    'price' => 1.80,
                    'description' => 'Lait entier frais de nos vaches. Non homogénéisé, riche et crémeux, en bouteille consignée.'
                ],
                [
                    'name' => 'Yaourt nature',
                    'unit' => 'unité',
                    'price' => 0.90,
                    'description' => 'Yaourt au lait entier, onctueux et nature. Sans additifs, fermentation traditionnelle.'
                ],
                [
                    'name' => 'Yaourt aux fruits',
                    'unit' => 'unité',
                    'price' => 1.10,
                    'description' => 'Yaourt aux fruits de saison, morceaux généreux. Peu sucré, préparation artisanale.'
                ],
                [
                    'name' => 'Fromage blanc',
                    'unit' => 'kg',
                    'price' => 5.20,
                    'description' => 'Fromage blanc onctueux au lait entier. Texture crémeuse, parfait nature ou sucré.'
                ],
                [
                    'name' => 'Vieux Lille',
                    'unit' => 'unité',
                    'price' => 7.80,
                    'description' => 'Fromage de caractère au lait pasteurisé de vache. Affiné 5 mois minimum.'
                ],
                [
                    'name' => 'Beurre',
                    'unit' => 'unité',
                    'price' => 4.80,
                    'description' => 'Beurre demi-sel baratté traditionnellement. Crémeux et fondant, 250g. Idéal tartines et pâtisseries.'
                ],
            ],
            'Fruits' => [
                [
                    'name' => 'Pommes',
                    'unit' => 'kg',
                    'price' => 3.50,
                    'description' => 'Pommes variées de nos vergers : croquantes et juteuses. Conservation naturelle, sans traitement.'
                ],
                [
                    'name' => 'Poires',
                    'unit' => 'kg',
                    'price' => 4.20,
                    'description' => 'Poires fondantes et sucrées, cueillies à maturité. Parfaites à croquer ou en compote.'
                ],
                [
                    'name' => 'Fraises',
                    'unit' => 'kg',
                    'price' => 6.50,
                    'description' => 'Fraises de pleine terre, gorgées de soleil. Parfumées et sucrées, idéales pour desserts et confitures.'
                ],
                [
                    'name' => 'Rhubarbe',
                    'unit' => 'kg',
                    'price' => 4.80,
                    'description' => 'Tiges de rhubarbe fraîches et acidulées. Parfaites pour tartes, compotes et confitures.'
                ],
                [
                    'name' => 'Pruneaux',
                    'unit' => 'kg',
                    'price' => 8.20,
                    'description' => 'Pruneaux séchés naturellement, moelleux et savoureux. Riche en fibres, sans sucre ajouté.'
                ],
            ],
            'Féculents' => [
                [
                    'name' => 'Pâtes',
                    'unit' => 'kg',
                    'price' => 2.50,
                    'description' => 'Pâtes artisanales au blé dur, séchées lentement. Texture ferme et goût authentique.'
                ],
                [
                    'name' => 'Semoule',
                    'unit' => 'kg',
                    'price' => 2.40,
                    'description' => 'Semoule fine de blé dur, moulue localement. Idéale pour couscous, pâtisseries orientales et desserts.'
                ],
                [
                    'name' => 'Farine',
                    'unit' => 'kg',
                    'price' => 2.20,
                    'description' => 'Farine de blé T65, moulue sur meule de pierre. Idéale pour pains, pâtisseries et crêpes.'
                ],
                [
                    'name' => 'Pain',
                    'unit' => 'unité',
                    'price' => 2.80,
                    'description' => 'Pain de campagne au levain naturel, cuit au four à bois. Croûte croustillante, mie moelleuse. 800g.'
                ],
            ],
            'Épicerie' => [
                [
                    'name' => 'Miel',
                    'unit' => 'unité',
                    'price' => 4.50,
                    'description' => 'Miel toutes fleurs de nos ruches. Récolté et mis en pot à la ferme. Pot de 500g.'
                ],
                [
                    'name' => 'Confiture',
                    'unit' => 'unité',
                    'price' => 2.80,
                    'description' => 'Confiture artisanale aux fruits de saison. Recette traditionnelle, peu sucrée. Pot de 350g.'
                ],
                [
                    'name' => 'Huile de colza',
                    'unit' => 'L',
                    'price' => 6.50,
                    'description' => 'Huile de colza vierge pressée à froid, issue de nos cultures des Hauts-de-France. Riche en oméga-3, goût doux et subtil. Bouteille 75cl.'
                ],
                [
                    'name' => 'Jus de pomme',
                    'unit' => 'L',
                    'price' => 3,
                    'description' => 'Pur jus de pomme pressé à la ferme. Sans sucre ajouté, trouble et naturel. Bouteille 1L.'
                ],

            ],
        ];

        $imagesMap = [
            'Pommes' => '/images/fruits/pommes.jpg',
            'Fraises' => '/images/fruits/fraises.jpg',
            'Poires' => '/images/fruits/poires.jpg',
            'Carottes' => '/images/legumes/carottes.jpg',
            'Carottes râpées' => '/images/entrees/salade_de_carottes_rapees.jpg',
            'Endives' => '/images/entrees/endives.jpg',
            'Radis' => '/images/entrees/radis.jpg',
            'Salade' => '/images/entrees/salades.jpg',
            'Poireaux' => '/images/legumes/poireaux.jpg',
            'Pommes de terre' => '/images/legumes/pommes_de_terre.jpg',
            'Yaourt nature' => '/images/produits_laitiers/yaourt_nature.jpg',
            'Yaourt aux fruits' => '/images/produits_laitiers/yaourt_aux_fruits.jpg',
            'Rhubarbe' => '/images/fruits/rhubarbe.jpg',
            'Lait' => '/images/produits_laitiers/lait.jpg',
            'Confiture' => '/images/epicerie/confiture.jpg',
            'Huile de colza' => '/images/epicerie/huile_de_colza.jpg',
            'Jus de pomme' => '/images/epicerie/jus_de_pomme.jpg',
            'Miel' => '/images/epicerie/miel.jpg',
            'Farine' => '/images/feculents/farine.jpg',
            'Pain' => '/images/feculents/pain.jpg',
            'Pâtes' => '/images/feculents/pâtes.jpg',
            'Semoule' => '/images/feculents/semoule.jpg',
            'Pruneaux' => '/images/fruits/pruneaux-ecoproduits-.jpg',
            'Choux' => '/images/legumes/choux_verts.jpg',
            'Epinards' => '/images/legumes/epinards.jpg',
            'Courges' => '/images/legumes/courges.jpg',
            'Œufs' => '/images/oeufs/oeufs.jpg',
            'Beurre' => '/images/produits_laitiers/beurre.jpg',
            'Fromage blanc' => '/images/produits_laitiers/fromage_blanc.jpg',
            'Vieux Lille' => '/images/produits_laitiers/vieux_lille.jpg',
            'Bœuf' => '/images/viandes/boeuf.jpg',
            'Porc' => '/images/viandes/porc.jpg',
            'Poulet' => '/images/viandes/poulet.jpg',
            'Navets' => '/images/legumes/navets.jpg'
        ];

        $productIndex = 1;

        foreach ($produitsParCategorie as $categorieNom => $produits) {
            foreach ($produits as $produitData) {
                $product = new Product();
                
                $product->setName($produitData['name']);
                $product->setDescriptionProduct($produitData['description']);
                $product->setIsBio($faker->boolean(70));
                $product->setPrice($produitData['price']);
                $product->setAvailability($faker->boolean(90));
                
                $product->setImageProduct($imagesMap[$produitData['name']] ?? '/images/default.jpg');
                
                $product->setCategory(
                    $this->getReference('category_' . strtolower(str_replace(' ', '_', $categorieNom)), Category::class)
                );
                
                $product->setUnit(
                    $this->getReference(UnitFixtures::UNIT_REFERENCE_PREFIX . $produitData['unit'], Unit::class)
                );
                
                $producteurIndex = $faker->numberBetween(1, 10);
                $product->setSeller($this->getReference(UserFixtures::PRODUCTEUR_REFERENCE_PREFIX . $producteurIndex, User::class));
                
                $this->addReference(self::PRODUCT_REFERENCE_PREFIX . $productIndex, $product);
                
                $manager->persist($product);
                $productIndex++;
            }
        }
        
        $manager->flush();
    }
}