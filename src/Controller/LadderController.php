<?php

namespace App\Controller;

use App\Enum\DirectionName;
use App\Enum\LadderUrl;
use App\Enum\PaymentPercent;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(
    new Expression("is_granted('IS_STUDENT') or is_granted('IS_REVIEWER')")
)]
final class LadderController extends AbstractController
{
    private const LIMIT_PGAS_PERCENT = 0.1;

    public function __construct(private StudentRepository $repository)
    {
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/ladder/{type}', name: 'app_ladder')]
    public function index(LadderUrl $type): Response
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('rating', 80));

        $countAllBudget = $this->repository->matching($criteria)->count();

        $countWhoCanGetPgas = (int) round($countAllBudget * self::LIMIT_PGAS_PERCENT);

        $countByType = (int) round(
            $countWhoCanGetPgas * PaymentPercent::getPercentByUrl($type)->value / 100
        );

        $students = $this->repository->getLadderByType(
            DirectionName::getNameByUrl($type)->value,
            $countByType
        );

        return $this->render('ladder/index.html.twig', [
            'name' => DirectionName::getNameByUrl($type)->value,
            'students' => $students,
            'available' => $countByType,
        ]);
    }
}
