<?php

namespace App\Controller;

use App\Entity\Card;
use App\Repository\ColumnRepository;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cards')]
class CardController extends AbstractController
{
    // GET /api/cards?columnId=1
    #[Route('', name: 'card_index', methods: ['GET'])]
    public function index(Request $request, ColumnRepository $columnRepository): JsonResponse
    {
        $columnId = $request->query->get('columnId');
        if (!$columnId) return $this->json(['message' => 'columnId requis'], 400);

        $column = $columnRepository->find($columnId);
        if (!$column) return $this->json(['message' => 'Colonne introuvable'], 404);

        $cards = $column->getCards()->toArray();
        usort($cards, fn($a, $b) => $a->getPosition() <=> $b->getPosition());

        $data = array_map(fn(Card $c) => [
            'id' => $c->getId(),
            'title' => $c->getTitle(),
            'description' => $c->getDescription(),
            'priority' => $c->getPriority(),
            'dueDate' => $c->getDueDate()?->format('Y-m-d'),
            'position' => $c->getPosition(),
            'assignedTo' => $c->getAssignedTo() ? [
                'id' => $c->getAssignedTo()->getId(),
                'firstName' => $c->getAssignedTo()->getFirstName(),
                'lastName' => $c->getAssignedTo()->getLastName(),
            ] : null,
        ], $cards);

        return $this->json($data);
    }

    // POST /api/cards
    #[Route('', name: 'card_create', methods: ['POST'])]
    public function create(Request $request, ColumnRepository $columnRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['columnId'])) {
            return $this->json(['message' => 'title et columnId requis'], 400);
        }

        $column = $columnRepository->find($data['columnId']);
        if (!$column) return $this->json(['message' => 'Colonne introuvable'], 404);

        $card = new Card();
        $card->setTitle($data['title']);
        $card->setDescription($data['description'] ?? null);
        $card->setPriority($data['priority'] ?? 'medium');
        $card->setPosition($data['position'] ?? $column->getCards()->count());
        $card->setBoardColumn($column);
        $card->setCreatedAt(new \DateTimeImmutable());

        if (!empty($data['dueDate'])) {
            $card->setDueDate(new \DateTime($data['dueDate']));
        }

        $em->persist($card);
        $em->flush();

        return $this->json([
            'id' => $card->getId(),
            'title' => $card->getTitle(),
            'description' => $card->getDescription(),
            'priority' => $card->getPriority(),
            'position' => $card->getPosition(),
        ], 201);
    }

    // GET /api/cards/{id}
    #[Route('/{id}', name: 'card_show', methods: ['GET'])]
    public function show(Card $card): JsonResponse
    {
        return $this->json([
            'id' => $card->getId(),
            'title' => $card->getTitle(),
            'description' => $card->getDescription(),
            'priority' => $card->getPriority(),
            'dueDate' => $card->getDueDate()?->format('Y-m-d'),
            'position' => $card->getPosition(),
            'assignedTo' => $card->getAssignedTo() ? [
                'id' => $card->getAssignedTo()->getId(),
                'firstName' => $card->getAssignedTo()->getFirstName(),
                'lastName' => $card->getAssignedTo()->getLastName(),
            ] : null,
            'labels' => $card->getLabels()->map(fn($l) => [
                'id' => $l->getId(),
                'name' => $l->getName(),
                'color' => $l->getColor(),
            ])->toArray(),
            'checklistItems' => $card->getChecklistItems()->map(fn($i) => [
                'id' => $i->getId(),
                'content' => $i->getContent(),
                'isDone' => $i->isIsDone(),
                'position' => $i->getPosition(),
            ])->toArray(),
            'comments' => $card->getComments()->map(fn($c) => [
                'id' => $c->getId(),
                'content' => $c->getContent(),
                'createdAt' => $c->getCreatedAt()?->format('Y-m-d H:i:s'),
                'author' => [
                    'id' => $c->getAuthor()->getId(),
                    'firstName' => $c->getAuthor()->getFirstName(),
                ],
            ])->toArray(),
        ]);
    }

    // PUT /api/cards/{id}
    #[Route('/{id}', name: 'card_update', methods: ['PUT'])]
    public function update(Card $card, Request $request, ColumnRepository $columnRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!empty($data['title'])) $card->setTitle($data['title']);
        if (array_key_exists('description', $data)) $card->setDescription($data['description']);
        if (!empty($data['priority'])) $card->setPriority($data['priority']);
        if (isset($data['position'])) $card->setPosition($data['position']);
        if (array_key_exists('dueDate', $data)) {
            $card->setDueDate($data['dueDate'] ? new \DateTime($data['dueDate']) : null);
        }

        // Déplacement entre colonnes (drag & drop)
        if (!empty($data['columnId'])) {
            $column = $columnRepository->find($data['columnId']);
            if ($column) $card->setBoardColumn($column);
        }

        $em->flush();

        return $this->json(['message' => 'Carte mise à jour']);
    }

    // DELETE /api/cards/{id}
    #[Route('/{id}', name: 'card_delete', methods: ['DELETE'])]
    public function delete(Card $card, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($card);
        $em->flush();

        return $this->json(['message' => 'Carte supprimée']);
    }
}
