<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cards/{cardId}/comments')]
class CommentController extends AbstractController
{
    // POST /api/cards/{cardId}/comments
    #[Route('', name: 'comment_create', methods: ['POST'])]
    public function create(int $cardId, Request $request, CardRepository $cardRepository, EntityManagerInterface $em): JsonResponse
    {
        $card = $cardRepository->find($cardId);
        if (!$card) return $this->json(['message' => 'Carte introuvable'], 404);

        $data = json_decode($request->getContent(), true);
        if (empty($data['content'])) return $this->json(['message' => 'content requis'], 400);

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setCard($card);
        $comment->setAuthor($this->getUser());

        $em->persist($comment);
        $em->flush();

        return $this->json([
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'author' => [
                'id' => $comment->getAuthor()->getId(),
                'firstName' => $comment->getAuthor()->getFirstName(),
                'lastName' => $comment->getAuthor()->getLastName(),
            ],
        ], 201);
    }

    // DELETE /api/cards/{cardId}/comments/{id}
    #[Route('/{id}', name: 'comment_delete', methods: ['DELETE'])]
    public function delete(Comment $comment, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($comment->getAuthor() !== $user) {
            return $this->json(['message' => 'Accès refusé'], 403);
        }

        $em->remove($comment);
        $em->flush();

        return $this->json(['message' => 'Commentaire supprimé']);
    }
}
