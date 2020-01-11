<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(StatsService $statsService)
    {
        // Récupération des stats
        $stats = $statsService->getStats();

        // Meilleures annonces
        $bestAds = $statsService->getAdsStats("DESC");

        // Pires annonces
        $worstAds = $statsService->getAdsStats("ASC");


        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
