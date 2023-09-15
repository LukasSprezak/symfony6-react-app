<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\{
    HttpFoundation\Request,
    HttpKernel\Controller\ValueResolverInterface,
    HttpKernel\ControllerMetadata\ArgumentMetadata,
    HttpKernel\Exception\BadRequestHttpException,
    PropertyInfo\Extractor\ReflectionExtractor
};
use Symfony\Component\Serializer\{
    Encoder\JsonEncoder,
    Exception\ExceptionInterface,
    Normalizer\PropertyNormalizer,
    Serializer,
    SerializerInterface
};
use function str_starts_with;

final class DTOArgumentResolver implements ValueResolverInterface
{
    private const NAMESPACE = 'App\DTO';

    private const TYPE = 'json';

    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([
            new PropertyNormalizer(
                classMetadataFactory: null,
                nameConverter: null,
                propertyTypeExtractor: new ReflectionExtractor()
            )], [new JsonEncoder()]
        );
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        return str_starts_with($argument->getType(), self::NAMESPACE);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            yield $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                self::TYPE
            );
        } catch (ExceptionInterface $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }
    }
}
