<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use App\Utils\Paginator;

class ClubController extends AbstractController
{
    #[Route('/club', name: 'app_club_index', methods: ['GET'])]
    public function index(ClubRepository $clubRepository): Response
    {
        return $this->render('club/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
        ]);
    }

    #[Route('/club/new', name: 'app_club_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClubRepository $clubRepository, ValidatorInterface $validator): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clubRepository->save($club, true);

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('club/new.html.twig', [
            'club' => $club,
            'form' => $form,
        ]);
    }


    #[Route('/club/{id}', name: 'app_club_select', methods: ['GET'])]
    public function show(Request $request, Club $club, ClubRepository $clubRepository, PlayerRepository $playerRepository, Paginator $paginator): Response
    {
        $query = $playerRepository->createQueryBuilder('a')
            ->andWhere('a.club = :val')
            ->setParameter(	'val', $club->getId())
            ->getQuery();
        $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('player/index.html.twig', [
            'club' => $club,
            'clubs' => $clubRepository->findAll(),
            'paginator' => $paginator
        ]);
    }

    #[Route('/club/{id}/edit', name: 'app_club_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Club $club, ClubRepository $clubRepository): Response
    {
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clubRepository->save($club, true);

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('club/edit.html.twig', [
            'club' => $club,
            'form' => $form,
        ]);
    }

    #[Route('/club/{id}', name: 'app_club_delete', methods: ['POST'])]
    public function delete(Request $request, Club $club, ClubRepository $clubRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            $clubRepository->remove($club, true);
        }

        return $this->redirectToRoute('app_club_select', [], Response::HTTP_SEE_OTHER);
    }
}
