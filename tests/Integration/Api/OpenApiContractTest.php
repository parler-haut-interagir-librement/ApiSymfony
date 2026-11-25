<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Phil\ApiContract\OpenApi\ContractVerifier;
use Phil\ApiContract\OpenApi\Finding;
use Phil\ApiContract\OpenApi\OpenApiDocument;
use Phil\ApiContract\OpenApi\RuleLevel;
use Phil\ApiContract\OpenApi\Severity;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * The OpenAPI document API Platform generates keeps the contract of an unversioned REST and JSON API: the rules of
 * phil/api-contract at the recommended level find nothing, neither error nor warning. `castor lint:openapi` runs the
 * same check on the exported document.
 */
#[CoversNothing]
final class OpenApiContractTest extends KernelTestCase
{
    public function testTheDocumentKeepsTheContract(): void
    {
        $container = self::getContainer();
        $factory = $container->get(OpenApiFactoryInterface::class);
        $normalizer = $container->get('serializer');
        $verifier = $container->get(ContractVerifier::class);
        self::assertInstanceOf(OpenApiFactoryInterface::class, $factory);
        self::assertInstanceOf(NormalizerInterface::class, $normalizer);
        self::assertInstanceOf(ContractVerifier::class, $verifier);

        $data = $normalizer->normalize($factory(), 'json');
        self::assertIsArray($data);
        $report = $verifier->verify(OpenApiDocument::fromArray($data), RuleLevel::Recommended);

        self::assertSame([], array_map(
            static fn (Finding $finding): string => $finding->location.': '.$finding->message,
            $report->reaching(Severity::Warning),
        ));
    }

    public function testTheDocumentDescribesTheBooksAndTheirTokens(): void
    {
        $factory = self::getContainer()->get(OpenApiFactoryInterface::class);
        self::assertInstanceOf(OpenApiFactoryInterface::class, $factory);
        $openApi = $factory();

        self::assertSame('ApiSymfony', $openApi->getInfo()->getTitle());
        self::assertNotNull($openApi->getPaths()->getPath('/api/books'));
        self::assertNotNull($openApi->getPaths()->getPath('/api/books/{id}'));
        self::assertArrayHasKey('bearer', (array) $openApi->getComponents()->getSecuritySchemes());
    }
}
