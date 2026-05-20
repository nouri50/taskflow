<?php

namespace App\Controller;

use App\Entity\Label;
use App\Repository\BoardRepository;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class LabelController extends AbstractController
{
    // GET /api/boards/{boardId}/labels
    #[Route('/boards/{boardId}/labels', name: 'label_index', methods: ['GET'])]
    public function index(int $boardId, BoardRepository $boardRepository): JsonResponse
    {
        $board = $boardRepository->find($boardId);
        if (!$board) return $this->json(['message' => 'Board introuvable'], 404);

        $data = $board->getLabels()->map(fn(Label $l) => [
            'id' => $l->getId(),
            'name' => $l->getName(),
            'color' => $l->getColor(),
        ])->toArray();

        return $this->json($data);
    }

    // POST /api/boards/{boardId}/labels
    #[Route('/boards/{boardId}/labels', name: 'label_create', methods: ['POST'])]
    public function create(int $boardId, Request $request, BoardRepository $boardRepository, EntityManagerInterface $em): JsonResponse
    {
        $board = $boardRepository->find($boardId);
        if (!$board) return $this->json(['message' => 'Board introuvable'], 404);

        $data = json_decode($request->getContent(), true);
        if (empty($data['name']) || empty($data['color'])) {
            return $this->json(['message' => 'name et color requis'], 400);
        }

        $label = new Label();
        $label->setName($data['name']);
        $label->setColor($data['color']);
        $label->setBoard($board);

        $em->persist($label);
        $em->flush();

        return $this->json([
            'id' => $label->getId(),
            'name' => $label->getName(),
            'color' => $label->getColor(),
        ], 201);
    }

    // POST /api/cards/{cardId}/labels/{labelId}
    #[Route('/cards/{cardId}/labels/{labelId}', name: 'card_label_add', methods: ['POST'])]
    public function addToCard(int $cardId, int $labelId, CardRepository $cardRepository, EntityManagerInterface $em): JsonResponse
    {
        $card = $cardRepository->find($cardId);
        if (!$card) return $this->json(['message' => 'Carte introuvable'], 404);

        $label = $em->find(Label::class, $labelId);
        if (!$label) return $this->json(['message' => 'Label introuvable'], 404);

        $card->addLabel($label);
        $em->flush();

        return $this->json(['message' => 'Label ajouté à la carte']);
    }

    // DELETE /api/cards/{cardId}/labels/{labelId}
    #[Route('/cards/{cardId}/labels/{labelId}', name: 'card_label_remove', methods: ['DELETE'])]
    public function removeFromCard(int $cardId, int $labelId, CardRepository $cardRepository, EntityManagerInterface $em): JsonResponse
    {
        $card = $cardRepository->find($cardId);
        if (!$card) return $this->json(['message' => 'Carte introuvable'], 404);

        $label = $em->find(Label::class, $labelId);
        if (!$label) return $this->json(['message' => 'Label introuvable'], 404);

        $card->removeLabel($label);
        $em->flush();

        return $this->json(['message' => 'Label retiré de la carte']);
    }
}
