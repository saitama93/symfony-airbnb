<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste des utilisateurs
     * 
     * @Route("/admin/users/{page<\d+>?1}", name="admin_user_index")
     * 
     * @param integer $page
     * @param PaginationService $pagination
     * 
     * @return  Response
     */
    public function index($page, PaginationService $pagination, UserRepository $repo)
    {

        $pagination->setEntityClass(User::class)
            ->setPage($page);


        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {

        if (count($user->getAds()) > 0 || count($user->getBookings()->slice(0)) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getFullName()}</strong> car il déjà posté / réservé des annonces !"
            );

        } else {

            $manager->remove($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimé !"
            );
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
