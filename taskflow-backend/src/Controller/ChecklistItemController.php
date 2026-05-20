<?php

namespace App\Controller;

use App\Entity\ChecklistItem;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cards/{cardId}/checklist')]
class ChecklistItemController extends AbstractController
{
    // POST /api/cards/{cardId}/checklist
    #[Route('', name: 'checklist_create', methods: ['POST'])]
    public function create(int $cardId, Request $request, CardRepository $cardRepository, EntityManagerInterface $em): JsonResponse
    {
        $card = $cardRepository->find($cardId);
        if (!$card) return $this->json(['message' => 'Carte introuvable'], 404);

        $data = json_decode($request->getContent(), true);
        if (empty($data['content'])) return $this->json(['message' => 'content requis'], 400);

        $item = new ChecklistItem();
        $item->setContent($data['content']);
        $item->setIsDone(false);
        $item->setPosition($card->getChecklistItems()->count());
        $item->setCard($card);

        $em->persist($item);
        $em->flush();

        return $this->json([
            'id' => $item->getId(),
            'content' => $item->getContent(),
            'isDone' => $item->$item->isDone(),
            'position' => $item->getPosition(),
        ], 201);
    }

    // PUT /api/cards/{cardId}/checklist/{id}
    #[Route('/{id}', name: 'checklist_update', methods: ['PUT'])]
    public function update(ChecklistItem $item, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!empty($data['content'])) $item->setContent($data['content']);
        if (isset($data['isDone'])) $item->setIsDone($data['isDone']);
        if (isset($data['position'])) $item->setPosition($data['position']);

        $em->flush();

        return $this->json([
            'id' => $item->getId(),
            'content' => $item->getContent(),
            'isDone' => $item->isDone(),
        ]);
    }

    // DELETE /api/cards/{cardId}/checklist/{id}
    #[Route('/{id}', name: 'checklist_delete', methods: ['DELETE'])]
    public function delete(ChecklistItem $item, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($item);
        $em->flush();

        return $this->json(['message' => 'Item supprimé']);
    }
}
