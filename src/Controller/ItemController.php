<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Service\ItemService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends AbstractController
{
    private $messageIdAbsent = 'No id parameter';
    private $messageDataAbsent = 'No data parameter';
    private $messageItemNotExist = 'No item';

    /**
     * @Route("/item", name="item_list", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function list(): JsonResponse
    {
        $items = $this->getDoctrine()
            ->getRepository(Item::class)
            ->findBy(['user' => $this->getUser()]);

        $allItems = [];
        foreach ($items as $item) {
            $oneItem['data'] = $item->getData();
            $oneItem['created_at'] = $item->getCreatedAt();
            $oneItem['updated_at'] = $item->getUpdatedAt();
            $allItems[] = $oneItem;
        }

        return $this->json($allItems);
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, ItemService $itemService)
    {
        $data = $request->get('data');

        if (empty($data)) {
            return  $this->createErrorResponse($this->messageDataAbsent);
        }

        $itemService->create($this->getUser(), $data);

        return $this->json([]);
    }

    /**
     * @Route("/item", name="item_update", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function update(Request $request, ItemService $itemService)
    {
        $id = $request->get('id');

        if (empty($id)) {
            return  $this->createErrorResponse($this->messageIdAbsent);
        }

        $item = $this->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        if (!$item || $item->getUser()->getId() != $this->getUser()->getId()) {
            return  $this->createErrorResponse($this->messageItemNotExist);
        }

        $data = $request->get('data');

        if (empty($data)) {
            return  $this->createErrorResponse($this->messageDataAbsent);
        }

        $itemService->update($item, $data);

        return $this->json([]);
    }

    /**
     * @Route("/item", name="items_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, ItemService $itemService)
    {
        $id = $request->get('id');

        if (empty($id)) {
            return $this->createErrorResponse($this->messageIdAbsent);
        }

        $item = $this->getDoctrine()
            ->getRepository(Item::class)
            ->find($id);

        if ($item === null || $item->getUser()->getId() != $this->getUser()->getId()) {
            return  $this->createErrorResponse($this->messageItemNotExist);
        }

        $itemService->delete($item);

        return $this->json([]);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    private function createErrorResponse(string $message)
    {
        return $this->json(['error' => $message], Response::HTTP_BAD_REQUEST);
    }
}
