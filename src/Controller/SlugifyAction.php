<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\StringHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestHeader;
use Symfony\Component\Routing\Attribute\Route;

/**
 * This is an action implementing the ADR pattern.
 *
 * @see SlugifyActionTest
 * @see https://symfony.com/doc/current/controller/service.html#invokable-controllers
 */
final class SlugifyAction extends AbstractController
{
    /**
     * An endpoint beside the resources of API Platform, for what is no resource: a plain controller returning JSON.
     * The MapQueryParameter attribute injects a GET parameter, and the MapRequestHeader attribute a request header.
     * Its errors are problem details too: Symfony renders them so for a client that accepts JSON.
     */
    #[Route(path: '/api/slugify', name: self::class, methods: ['GET'])]
    public function __invoke(
        StringHelper $stringHelper,
        #[MapQueryParameter]
        string $title,
        #[MapRequestHeader('Accept-Language')]
        ?string $locale = null,
    ): JsonResponse {
        return $this->json([
            'slug' => $stringHelper->slugify($title),
            'locale' => $locale,
        ]);
    }
}
