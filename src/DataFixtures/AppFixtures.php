<?php

namespace App\DataFixtures;

use App\Entity\ApiKey;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private $entityManager;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * LOAD FIXTURE
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // USER FIXTURE

        $user = [
            1 => [
                'email' => 'admin@bilmo.com',
                'role' => 'ROLE_ADMIN',
                'password' => '$argon2id$v=19$m=65536,t=4,p=1$MXl4dVhUbDZwck5LTTg4aQ$vLCd1a/SFOe8ZSHU6yY8LhlVJ94TCvzGN7QiT+8YDsw',
                'name' => 'Willis',
            ],
        ];

        foreach ($user as $value) {
            $user = new User();
            $user->setEmail($value['email']);
            $user->setRoles(array($value['role']));
            $user->setPassword($value['password']);
            $user->setName($value['name']);
            $manager->persist($user);
        }

        $manager->flush();

        // API KEY

        $apykey = [
            1 => [
                'api_key' => 'gCNsDqidW3LfZbHg1WCu',
            ]
        ];

        foreach ($apykey as $value) {
            $apykey = new ApiKey();
            $apykey->setUser($user);
            $apykey->setApiKey($value['api_key']);
            $manager->persist($apykey);
        }

        $manager->flush();

        // CUSTOMER

        $customer = [
            1 => [
                'email' => 'CoretteMathieu@teleworm.us',
                'zip_code' => 17100,
                'adress' => '93, Rue de Strasbourg',
                'firstname' => 'Corette',
                'lastname' => 'Mathieu',
                'phone' => '+33523103505',
                'city' => 'SAINTES',
                'country' => 'France',
            ],
            2 => [
                'email' => 'ArminaBriard@teleworm.us',
                'zip_code' => 93390,
                'adress' => '11, Rue Hubert de Lisle',
                'firstname' => 'Christian',
                'lastname' => 'Dervin',
                'phone' => '+33612244205',
                'city' => 'CLICHY-SOUS-BOIS',
                'country' => 'France',
            ],
            3 => [
                'email' => 'LothairBinet@rhyta.com',
                'zip_code' => 59120,
                'adress' => '41, rue Sébastopo',
                'firstname' => 'Chris',
                'lastname' => 'Brown',
                'phone' => '+33143005850',
                'city' => 'LOOS',
                'country' => 'France',
            ],
            4 => [
                'email' => 'ChristianCyr@armyspy.com',
                'zip_code' => 93400,
                'adress' => '97, rue Gouin de Beauchesne',
                'firstname' => 'John',
                'lastname' => 'Franco',
                'phone' => '+3352319823',
                'city' => 'LIVRY-GARGAN',
                'country' => 'France',
            ],
            5 => [
                'email' => 'FayeSt-Jacques@armyspy.com',
                'zip_code' => 13140,
                'adress' => '1, rue Bonneterie',
                'firstname' => 'Jule',
                'lastname' => 'Merguez',
                'phone' => '+33522991305',
                'city' => 'MIRAMAS',
                'country' => 'France',
            ],
        ];

        foreach ($customer as $value) {
            $customer = new Customer();
            $customer->setUser($user);
            $customer->setEmail($value['email']);
            $customer->setZipCode($value['zip_code']);
            $customer->setAdress($value['adress']);
            $customer->setFirstname($value['firstname']);
            $customer->setLastname($value['lastname']);
            $customer->setPhone($value['phone']);
            $customer->setCity($value['city']);
            $customer->setCountry($value['country']);
            $manager->persist($customer);
        }

        $manager->flush();

        // PRODUCT

        $product = [
            1 => [
                'name' => 'Galaxy S13',
                'price' => 90000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Samsung',
                'quantity' => '22',
            ],
            2 => [
                'name' => 'Iphone 13',
                'price' => 120000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Apple',
                'quantity' => '15',
            ],
            3 => [
                'name' => 'Black shark 4',
                'price' => 30000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Xiaomi',
                'quantity' => '32',
            ],
            4 => [
                'name' => '3310',
                'price' => 3000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Nokia',
                'quantity' => '12',
            ],
            5 => [
                'name' => 'Galaxy zoom',
                'price' => 10000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Samsung',
                'quantity' => '54',
            ],
        ];

        foreach ($product as $value) {
            $product = new Product();
            $product->setName($value['name']);
            $product->setPrice($value['price']);
            $product->setDescription($value['description']);
            $product->setBrand($value['brand']);
            $product->setQuantity($value['quantity']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
