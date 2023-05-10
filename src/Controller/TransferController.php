<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\PlayerTransfer;
use App\Entity\Player;
use App\Entity\Club;
use App\Repository\PlayerTransferRepository;
use App\Repository\ClubRepository;
use App\Form\PlayerTransferType;

class TransferController extends AbstractController
{

    #[Route('/transfer', name: 'app_transfer_show')]
    public function index(Request $request, ClubRepository $clubRepository, PlayerTransferRepository $playerTransferRepository, PlayerRepository $playerRepository): Response
    {
        $club1 = $request->query->get('club1', '');
        $club2 = $request->query->get('club2', '');

        $transfer = new PlayerTransfer();
        if (!empty($club1)) $transfer->setClub1($clubRepository->find($club1));
        if (!empty($club2)) $transfer->setClub2($clubRepository->find($club2));

        $form = $this->createForm(PlayerTransferType::class, $transfer);
        $form->handleRequest($request);

        return $this->render('transfer/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
            'players1' => $playerRepository->findBy(array('club' => $transfer->getClub1())),
            'players2' => $playerRepository->findBy(array('club' => $transfer->getClub2())),
            'form' => $form,
        ]);
    }

    #[Route('/transfer/sell', name: 'app_transfer_sell')]
    public function sell(Request $request, ClubRepository $clubRepository, PlayerTransferRepository $playerTransferRepository, PlayerRepository $playerRepository): Response
    {
        $error = "";
        $transfer = new PlayerTransfer();
        $form = $this->createForm(PlayerTransferType::class, $transfer);
        $form->handleRequest($request);

        $transfer->setType(1);

        if ($form->isSubmitted() && $form->isValid()) {

            // check validation
            if ($transfer->getClub1()->getId() == $transfer->getClub2()->getId()) {
                $error = 'Please, select different clubs.';
            } else if (empty($transfer->getPlayer1())) {
                $error = 'Please, select player to sell';
            } else {
                // check the balance
                $newBalance1 = $transfer->getClub1()->getBalance() + $transfer->getPrice();
                $newBalance2 = $transfer->getClub2()->getBalance() - $transfer->getPrice();
                if ($newBalance2 < 0) {
                    $error = 'The balance of ' . $transfer->getClub2()->getName() . ' is not sufficient';
                } else {
                    $playerTransferRepository->save($transfer, true);

                    // update the player's club
                    $playerTransferRepository->createQueryBuilder('')
                        ->update(Player::class, 'p')
                        ->set('p.club', ':club')
                        ->setParameter('club', $transfer->getClub2())
                        ->where('p.id = :id')
                        ->setParameter('id', $transfer->getPlayer1())
                        ->getQuery()
                        ->execute();

                    // add the club1's balance
                    $clubRepository->createQueryBuilder('')
                        ->update(Club::class, 'c')
                        ->set('c.balance', ':balance')
                        ->setParameter('balance', $newBalance1)
                        ->where('c.id = :id')
                        ->setParameter('id', $transfer->getClub1())
                        ->getQuery()
                        ->execute();

                    // reduce the club2's balance
                    $playerTransferRepository->createQueryBuilder('')
                        ->update(Club::class, 'c')
                        ->set('c.balance', ':balance')
                        ->setParameter('balance', $newBalance2)
                        ->where('c.id = :id')
                        ->setParameter('id', $transfer->getClub2())
                        ->getQuery()
                        ->execute();

                    return $this->redirectToRoute('app_transfer_show', ['club1' => $transfer->getClub1()->getId(), 'club2' => $transfer->getClub2()->getId()], Response::HTTP_SEE_OTHER);
                }
            }

        }

        return $this->render('transfer/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
            'players1' => $playerRepository->findBy(array('club' => $transfer->getClub1())),
            'players2' => $playerRepository->findBy(array('club' => $transfer->getClub2())),
            'form' => $form,
            'error' => $error
        ]);
    }

    #[Route('/transfer/buy', name: 'app_transfer_buy')]
    public function buy(Request $request, ClubRepository $clubRepository, PlayerTransferRepository $playerTransferRepository, PlayerRepository $playerRepository): Response
    {
        $error = "";
        $transfer = new PlayerTransfer();
        $form = $this->createForm(PlayerTransferType::class, $transfer);
        $form->handleRequest($request);

        $transfer->setType(2);

        if ($form->isSubmitted() && $form->isValid()) {

            // check validation
            if ($transfer->getClub1()->getId() == $transfer->getClub2()->getId()) {
                $error = 'Please, select different clubs.';
            } else if (empty($transfer->getPlayer2())) {
                $error = 'Please, select player to buy';
            } else {
                // check the balance
                $newBalance1 = $transfer->getClub1()->getBalance() - $transfer->getPrice();
                $newBalance2 = $transfer->getClub2()->getBalance() + $transfer->getPrice();
                if ($newBalance1 < 0) {
                    $error = 'The balance of ' . $transfer->getClub1()->getName() . ' is not sufficient';
                } else {
                    $playerTransferRepository->save($transfer, true);

                    // update the player's club
                    $playerTransferRepository->createQueryBuilder('')
                        ->update(Player::class, 'p')
                        ->set('p.club', ':club')
                        ->setParameter('club', $transfer->getClub1())
                        ->where('p.id = :id')
                        ->setParameter('id', $transfer->getPlayer2())
                        ->getQuery()
                        ->execute();

                    // add the club1's balance
                    $clubRepository->createQueryBuilder('')
                        ->update(Club::class, 'c')
                        ->set('c.balance', ':balance')
                        ->setParameter('balance', $newBalance1)
                        ->where('c.id = :id')
                        ->setParameter('id', $transfer->getClub1())
                        ->getQuery()
                        ->execute();

                    // reduce the club2's balance
                    $playerTransferRepository->createQueryBuilder('')
                        ->update(Club::class, 'c')
                        ->set('c.balance', ':balance')
                        ->setParameter('balance', $newBalance2)
                        ->where('c.id = :id')
                        ->setParameter('id', $transfer->getClub2())
                        ->getQuery()
                        ->execute();

                    return $this->redirectToRoute('app_transfer_show', ['club1' => $transfer->getClub1()->getId(), 'club2' => $transfer->getClub2()->getId()], Response::HTTP_SEE_OTHER);
                }
            }

        }

        return $this->render('transfer/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
            'players1' => $playerRepository->findBy(array('club' => $transfer->getClub1())),
            'players2' => $playerRepository->findBy(array('club' => $transfer->getClub2())),
            'form' => $form,
            'error' => $error
        ]);
    }
}
