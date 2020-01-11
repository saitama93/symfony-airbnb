<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Permet d'afficher les statistiques du site
     *
     * @return void
     */
    public function getStats()
    {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $comments = $this->getCommentsCount();
        $bookings = $this->getBookingsCount();

        return compact('users', 'ads', 'comments', 'bookings');
    }

    /**
     * Permet d'afficher les meilleures / pires annonces
     *
     * @param string $direction
     * @return void
     */
    public function getAdsStats(string $direction)
    {
        return  $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
        FROM App\Entity\Comment c
        JOIN c.ad a
        JOIN a.author u
        GROUP BY a
        ORDER BY note ' . $direction
        )->setMaxResults(5)->getResult();
    }

    /**
     * Permet d'afficher le nombre d'utilisateurs 
     *
     * @return void
     */
    public function getUsersCount()
    {
        return  $users = $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    /**
     * Permet d'afficher le nombre d'annonces
     *
     * @return void
     */
    public function getAdsCount()
    {
        return  $ads = $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    /**
     * Permet d'fficher le nombre de réservations
     *
     * @return void
     */
    public function getBookingsCount()
    {
        return  $bookings = $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Booking c')->getSingleScalarResult();
    }

    /**
     * Permet d'afficher le nombre de commentaires
     *
     * @return void
     */
    public function getCommentsCount()
    {
        return  $comments = $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Comment b')->getSingleScalarResult();
    }
}
