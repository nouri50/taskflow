<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\BoardMember;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Column;

#[Route('/api/boards')]
class BoardController extends AbstractController
{
    // GET /api/boards — liste des boards de l'utilisateur
    #[Route('', name: 'board_index', methods: ['GET'])]
    public function index(BoardRepository $boardRepository): JsonResponse
    {
        $user = $this->getUser();
        $boards = $boardRepository->findByUser($user);

        $data = array_map(fn(Board $b) => [
            'id' => $b->getId(),
            'name' => $b->getName(),
            'description' => $b->getDescription(),
            'createdAt' => $b->getCreatedAt()?->format('Y-m-d H:i:s'),
        ], $boards);

        return $this->json($data);
    }

    // POST /api/boards — créer un board
    #[Route('', name: 'board_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['message' => 'Le nom est requis'], 400);
        }

        $board = new Board();
        $board->setName($data['name']);
        $board->setDescription($data['description'] ?? null);
        $board->setCreatedAt(new \DateTimeImmutable());
        $board->setOwner($user);

        // Créer les 3 colonnes par défaut
        $defaultColumns = ['À faire', 'En cours', 'Terminé'];
        foreach ($defaultColumns as $index => $colName) {
            $column = new Column();
            $column->setName($colName);
            $column->setPosition($index);
            $column->setBoard($board);
            $em->persist($column);
        }

        // Ajouter le créateur comme membre
        $member = new BoardMember();
        $member->setBoard($board);
        $member->setUser($user);
        $member->setRole('owner');
        $member->setJoinedAt(new \DateTimeImmutable());

        $em->persist($board);
        $em->persist($member);
        $em->flush();

        return $this->json([
            'id' => $board->getId(),
            'name' => $board->getName(),
            'description' => $board->getDescription(),
        ], 201);
    }

    // GET /api/boards/{id} — détail d'un board
    #[Route('/{id}', name: 'board_show', methods: ['GET'])]
    public function show(Board $board): JsonResponse
    {
        return $this->json([
            'id' => $board->getId(),
            'name' => $board->getName(),
            'description' => $board->getDescription(),
            'createdAt' => $board->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }
    // PUT /api/boards/{id} — modifier un board
    #[Route('/{id}', name: 'board_update', methods: ['PUT'])]
    public function update(Board $board, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($board->getOwner() !== $user) {
            return $this->json(['message' => 'Accès refusé'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!empty($data['name'])) $board->setName($data['name']);
        if (array_key_exists('description', $data)) $board->setDescription($data['description']);

        $em->flush();

        return $this->json(['message' => 'Board mis à jour']);
    }

    // DELETE /api/boards/{id} — supprimer un board
    #[Route('/{id}', name: 'board_delete', methods: ['DELETE'])]
    public function delete(Board $board, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($board->getOwner() !== $user) {
            return $this->json(['message' => 'Accès refusé'], 403);
        }

        $em->remove($board);
        $em->flush();

        return $this->json(['message' => 'Board supprimé'], 200);
    }

    // POST /api/boards/{id}/invite
    #[Route('/{id}/invite', name: 'board_invite', methods: ['POST'])]
    public function invite(Board $board, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($board->getOwner() !== $user) {
            return $this->json(['message' => 'Seul le propriétaire peut inviter'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['email'])) {
            return $this->json(['message' => 'email requis'], 400);
        }

        $userRepo = $em->getRepository(\App\Entity\User::class);
        $invitedUser = $userRepo->findOneBy(['email' => $data['email']]);

        if (!$invitedUser) {
            return $this->json(['message' => 'Utilisateur introuvable'], 404);
        }

        // Vérifier s'il est déjà membre
        $memberRepo = $em->getRepository(\App\Entity\BoardMember::class);
        $existing = $memberRepo->findOneBy(['board' => $board, 'user' => $invitedUser]);

        if ($existing) {
            return $this->json(['message' => 'Déjà membre'], 400);
        }

        $member = new \App\Entity\BoardMember();
        $member->setBoard($board);
        $member->setUser($invitedUser);
        $member->setRole('member');
        $member->setJoinedAt(new \DateTimeImmutable());

        $em->persist($member);
        $em->flush();

        return $this->json(['message' => 'Membre ajouté avec succès'], 201);
    }

    // GET /api/boards/{id}/members
    #[Route('/{id}/members', name: 'board_members', methods: ['GET'])]
    public function members(Board $board): JsonResponse
    {
        $members = $board->getBoardMembers()->map(fn($m) => [
            'id' => $m->getId(),
            'role' => $m->getRole(),
            'user' => [
                'id' => $m->getUser()->getId(),
                'firstName' => $m->getUser()->getFirstName(),
                'lastName' => $m->getUser()->getLastName(),
                'email' => $m->getUser()->getEmail(),
            ]
        ])->toArray();

        return $this->json($members);
    }
}
