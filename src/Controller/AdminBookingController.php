<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminBookingController extends AbstractController
{
    /**
     * Permet d'afficher la liste des réservations
     * 
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     * 
     * @param PaginationService $pagination
     * @param integer $page
     * 
     * @return Response
     */
    public function index(PaginationService $pagination, $page)
    {

        $pagination->setEntityClass(Booking::class)
            ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier une réservation 
     * 
     * @Route("/admin/booking/{id}/edit", name="admin_bookings_edit")
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°{$booking->getId()} a bien été modifiée !"
            );

            return $this->redirectToRoute("admin_bookings_index");
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }
    /**
     * Permet de supprimer une réservation
     * 
     * @Route("/admin/booking/{id}/delete", name="admin_bookings_delete")
     *
     * @param EntityManagerInterface $manager
     * @param Booking $booking
     * 
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Booking $booking)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation a bien été supprimé"
        );

        return $this->redirectToRoute("admin_bookings_index");
    }
}
