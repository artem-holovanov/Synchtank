<?php
/**
 * Created by Artem Holovanov.
 * Date: 18.06.2025 20:09.
 */

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Track;
use App\Form\TrackType;
use App\Service\TrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tracks')]
class TrackController extends AbstractController
{
    public function __construct(
        private TrackService $trackService,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'track_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tracks = $this->trackService->getAll();
        return $this->json($tracks, Response::HTTP_OK);
    }

    #[Route('', name: 'track_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $track = new Track();
        $form = $this->createForm(TrackType::class, $track);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json([
                'errors' => $this->getFormErrors($form)
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->trackService->create($track);

        return $this->json($track, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'track_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $track = $this->trackService->get($id);
        if (!$track) {
            return $this->json(['error' => 'Track not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(TrackType::class, $track);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json([
                'errors' => $this->getFormErrors($form)
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->trackService->update();

        return $this->json($track);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->all() as $child) {
            foreach ($child->getErrors(true) as $error) {
                $errors[$child->getName()][] = $error->getMessage();
            }
        }
        return $errors;
    }
}