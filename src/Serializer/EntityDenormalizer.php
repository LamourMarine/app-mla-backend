<?php
// src/Serializer/EntityDenormalizer.php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class EntityDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'ENTITY_DENORMALIZER_ALREADY_CALLED';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;

        // Si c'est un ID numérique, récupère l'entité existante
        if (is_numeric($data)) {
            return $this->entityManager->getRepository($type)->find($data);
        }

        // Sinon, utilise le denormalizer par défaut
        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        // Évite les boucles infinies
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        // Supporte les entités Doctrine avec des ID numériques
        return is_numeric($data) && class_exists($type) && str_starts_with($type, 'App\\Entity\\');
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => false,
        ];
    }
}