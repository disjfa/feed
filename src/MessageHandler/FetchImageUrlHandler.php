<?php

namespace App\MessageHandler;

use App\Message\ItemWasCreated;
use App\Repository\ItemRepository;
use Disjfa\MediaBundle\Service\UploadService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FetchImageUrlHandler implements MessageHandlerInterface
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var UploadService
     */
    private $uploadService;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * FetchImageUrlHandler constructor.
     *
     * @param ItemRepository         $itemRepository
     * @param UploadService          $uploadService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ItemRepository $itemRepository, UploadService $uploadService, EntityManagerInterface $entityManager)
    {
        $this->itemRepository = $itemRepository;
        $this->uploadService = $uploadService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ItemWasCreated $itemWasCreated
     *
     * @return bool
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function __invoke(ItemWasCreated $itemWasCreated)
    {
        $item = $this->itemRepository->find($itemWasCreated->getItemId());

        if ($item->getImageUrl()) {
            return true;
        }

        $url = $item->getGuid();
        if ($item->getLink()) {
            $url = $item->getLink();
        }

        try {
            $client = HttpClient::create();
            $response = $client->request('GET', $url);
            $image = $this->fetchImageFromResponse($response);
        } catch (ClientException $exception) {
            // nope
            return false;
        } catch (TransportException $exception) {
            // nope
            return false;
        } catch (RedirectionException $exception) {
            // nope
            return false;
        }

        if (!$image) {
            return true;
        }
        if (false === filter_var($image, FILTER_VALIDATE_URL)) {
            return true;
        }
        try {
            $response = $client->request('GET', $image);
            $headers = $response->getHeaders();
        } catch (ClientException $exception) {
            // nope
            return false;
        } catch (TransportException $exception) {
            // nope
            return false;
        } catch (RedirectionException $exception) {
            // nope
            return false;
        }

        $contentType = current($headers['content-type']) ?? null;
        if ('image/jpeg' !== $contentType && 'image/png' !== $contentType) {
            return false;
        }

        $path = tempnam(sys_get_temp_dir(), 'itm');
        $handle = fopen($path, 'w');
        fwrite($handle, $response->getContent());
        fclose($handle);

        $file = new File($path);
        $request = Request::create($image);
        $filename = basename($request->getPathInfo());
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        $media = $this->uploadService->saveFile($file, $filename);
        $item->setImageUrl($media->getUrl());

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return true;
    }

    public function fetchImageFromResponse(ResponseInterface $response)
    {
        $crawler = new Crawler($response->getContent());
        $ogImage = $crawler->filterXPath('//meta[contains(@name, "og:image")]');
        if ($ogImage->count()) {
            return $ogImage->attr('content');
        }
        $ogImage = $crawler->filterXPath('//meta[contains(@name, "twitter:image")]');
        if ($ogImage->count()) {
            return $ogImage->attr('content');
        }

        return null;
    }
}
