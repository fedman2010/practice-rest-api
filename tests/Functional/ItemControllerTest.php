<?php

namespace App\Tests\Functional;

use App\Repository\ItemRepository;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class ItemControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        /** @var ItemRepository $itemRepository */
        $itemRepository = static::$container->get(ItemRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $data = 'very secure new item data';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $items = $itemRepository->findAll();


        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals($data, $items[0]->getData());
    }

    public function testList()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');
        $data = 'very secure test data for testList';

        $client->loginUser($user);

        static::$container->get(ItemService::class)->create($user, $data);

        $client->request('GET', '/item');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertStringContainsString($data, $client->getResponse()->getContent());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        /** @var ItemRepository $itemRepository */
        $itemRepository = static::$container->get(ItemRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        static::$container->get(ItemService::class)->create($user, 'very secure old test data');

        $item = $itemRepository->findAll()[0];

        $data = 'very secure new item data';

        $newItemData = ['data' => $data, 'id' => $item->getId()];

        $client->request('PUT', '/item', $newItemData);

        $updatedItem = $itemRepository->find($item->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals($data, $updatedItem->getData());
    }

    public function testDelete()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        /** @var ItemRepository $itemRepository */
        $itemRepository = static::$container->get(ItemRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        static::$container->get(ItemService::class)->create($user, 'very secure test data');

        $item = $itemRepository->findAll()[0];

        $id = $item->getId();

        $client->request('DELETE', '/item', ['id' => $id]);

        $deletedItem = $itemRepository->find($id);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertNull($deletedItem);
    }

    public function testThatItemResorceIsGuarded()
    {
        $client = static::createClient();

        $client->request('POST', '/item');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
        $client->request('PUT', '/item', ['id' => 1]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
        $client->request('GET', '/item', ['id' => 1]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
        $client->request('DELETE', '/item', ['id' => 1]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testCreateItemWithoutDataParameterProvided()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('POST', '/item');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertArrayHasKey('error', json_decode($client->getResponse()->getContent(), true));
    }

    public function testUserCannotUpdateNotHisItems()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        /** @var ItemRepository $itemRepository */
        $itemRepository = static::$container->get(ItemRepository::class);

        $john = $userRepository->findOneByUsername('john');
        $bob = $userRepository->findOneByUsername('bob');

        $client->loginUser($john);

        static::$container->get(ItemService::class)->create($bob, 'very secure bob\'s data');

        $bobItem = $itemRepository->findOneBy(['user' => $bob]);

        $client->request('DELETE', '/item', ['id' => $bobItem->getId()]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('Current user don\'t own the entity.', $data['error']);
    }
}
