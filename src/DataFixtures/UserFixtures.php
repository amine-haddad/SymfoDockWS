<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{

    private SluggerInterface $slugger;
    private $faker;
    public function __construct(SluggerInterface $slugger, private readonly UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->faker = Factory::create('fr_FR');
        
    }

    public function load(ObjectManager $manager): void
    {  
        
    // Création d’un utilisateur de type “contributeur” (= auteur)
    $contributor = new User();
    $contributor->setEmail('contributor@monsite.com');
    $contributor->setRoles(['ROLE_CONTRIBUTOR']);
    $hashedPassword = $this->passwordHasher->hashPassword(
        $contributor,
        'contributorpassword'
    );

    $contributor->setPassword($hashedPassword);
    $manager->persist($contributor);

    // Création d’un utilisateur de type “administrateur”
    $admin = new User();
    $admin->setEmail('admin@monsite.com');
    $admin->setRoles(['ROLE_ADMIN']);
    $hashedPassword = $this->passwordHasher->hashPassword(
        $admin,
        'adminpassword'
    );
    $admin->setPassword($hashedPassword);
    $manager->persist($admin);

    // Sauvegarde des 2 nouveaux utilisateurs :
    $manager->flush();
}
}
