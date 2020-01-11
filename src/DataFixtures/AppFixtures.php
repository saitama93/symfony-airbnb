<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');
        $slugify = new Slugify();

        $adminRole = new Role();

        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Igali')
            ->setLastName('ILMI AMIR')
            ->setEmail('igali@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://randomuser.me/api/portraits/men/54.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription(('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>'))
            ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // Gestion des utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for ($z = 1; $z <= 10; $z++) {

            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = "https://randomuser.me/api/portraits/";
            $picture_id = $faker->numberBetween(1, 99) . '.jpg';

            //If
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $picture_id;

            // if ($genre == "male") {
            //     $picutre = $picture . 'men/' . $picture_id;
            // }else {
            //     $picutre = $picture . 'women/' . $picture_id;
            // }

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }

        // Gestion des annonces
        for ($i = 1; $i <= 30; $i++) {

            $ad = new Ad();

            $title      = $faker->sentence();
            $slug       = $slugify->slugify($title);
            $coverImage = $faker->imageUrl(350, 100);
            $introduction = $faker->paragraph(2);
            $content    = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setSlug($slug)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

            for ($j = 0; $j < mt_rand(2, 5); $j++) {

                $image = new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);
            }

            // Gestion des réservations

            for ($h = 1; $h <= mt_rand(0, 10); $h++) {

                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                // Gestion de la date de fin
                $duration  = mt_rand(3, 10);
                $endDate   = (clone $startDate)->modify("+$duration days");
                $amount    = $ad->getPrice() * $duration;

                // Attribution des utilisateurs
                $booker    = $users[mt_rand(0, count($users) - 1)];

                // Création du commentaire
                $comment   = $faker->paragraph();

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setCreatedAt($createdAt)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setComment($comment)
                    ->setAmount($amount);

                $manager->persist($booking);

                // Gestion des commentaires
                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(1, 5))
                        ->setAuthor($booker)
                        ->setAd($ad);
                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
