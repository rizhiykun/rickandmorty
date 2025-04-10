<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security\LazyFirewallContext;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Сервис по настройке сериализатора
 *
 */
class AppSerializer implements SerializerInterface
{
    /// Формат даты
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Сериализация данных
     *
     * @param mixed $data Any data
     * @param string $format Format name
     * @param array $context Options normalizers/encoders have access to
     * @return string
     */
    #[\Override]
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->getSerializer()->serialize($data, $format, $context);
    }

    /**
     * Получение сериализатора
     *
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        $encoders = [
            'json' => new JsonEncoder(),
        ];

        $defaultContext = [
            ///
            /// Атрибуты которые необходимо игнорировать всегда
            ///
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                ///
                /// Proxy методы от Doctrine
                ///
                '__initializer__',
                '__cloner__',
                '__isInitialized__',
            ],
            ///
            /// Разрешение конфликта цикрулярной зависимости
            /// Как только пойдет зависимость, на MaxDepth будет выполнена эта функция
            /// Она вернет в ответ только идентификатор
            ///
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                if (
                    !$object::class == LazyFirewallContext::class
                ) {
                    return $object->getId();
                }
                return null;
            },
        ];

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $normalizers = [
            new DateTimeNormalizer([
                DateTimeNormalizer::FORMAT_KEY => self::DATE_FORMAT,
            ]),
            new JsonSerializableNormalizer(),
            new ObjectNormalizer(
                $classMetadataFactory,
                $metadataAwareNameConverter,
                null,
                null,
                null,
                null,
                $defaultContext,
            ),
        ];

        return new Serializer($normalizers, $encoders);
    }

    #[\Override]
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->getSerializer()->deserialize($data, $type, $format, $context);
    }
}
