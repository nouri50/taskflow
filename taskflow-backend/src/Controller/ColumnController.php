<?php

namespace App\Controller;

use App\Entity\Column;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/boards/{boardId}/columns')]
class ColumnController extends AbstractController
{
    // GET /api/boards/{boardId}/columns
    #[Route('', name: 'column_index', methods: ['GET'])]
    public function index(int $boardId, BoardRepository $boardRepository): JsonResponse
    {
        $board = $boardRepository->find($boardId);
        if (!$board) return $this->json(['message' => 'Board introuvable'], 404);

        $columns = $board->getColumns()->toArray();
        usort($columns, fn($a, $b) => $a->getPosition() <=> $b->getPosition());

        $data = array_map(fn(Column $c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'position' => $c->getPosition(),
        ], $columns);

        return $this->json($data);
    }

    // POST /api/boards/{boardId}/columns
    #[Route('', name: 'column_create', methods: ['POST'])]
    public function create(int $boardId, Request $request, BoardRepository $boardRepository, EntityManagerInterface $em): JsonResponse
    {
        $board = $boardRepository->find($boardId);
        if (!$board) return $this->json(['message' => 'Board introuvable'], 404);

        $data = json_decode($request->getContent(), true);
        if (empty($data['name'])) return $this->json(['message' => 'Nom requis'], 400);

        $column = new Column();
        $column->setName($data['name']);
        $column->setPosition($data['position'] ?? $board->getColumns()->count());
        $column->setBoard($board);

        $em->persist($column);
        $em->flush();

        return $this->json([
            'id' => $column->getId(),
            'name' => $column->getName(),
            'position' => $column->getPosition(),
        ], 201);
    }

    // PUT /api/boards/{boardId}/columns/{id}
    #[Route('/{id}', name: 'column_update', methods: ['PUT'])]
    public function update(int $boardId, Column $column, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!empty($data['name'])) $column->setName($data['name']);
        if (isset($data['position'])) $column->setPosition($data['position']);

        $em->flush();

        return $this->json(['message' => 'Colonne mise à jour']);
    }

    // DELETE /api/boards/{boardId}/columns/{id}
    #[Route('/{id}', name: 'column_delete', methods: ['DELETE'])]
    public function delete(Column $column, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($column);
        $em->flush();

        return $this->json(['message' => 'Colonne supprimée']);
    }
}
