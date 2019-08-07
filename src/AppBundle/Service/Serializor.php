<?php

namespace AppBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

/**
 * Class Serializor
 * @package AppBundle\Service
 */
class Serializor
{
    /**
     * @return Serializer
     * @throws AnnotationException
     */
    public function getSerializer(): Serializer
    {
        $classMetaDataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetaDataFactory);
        $dateTimeNormalizer = new DateTimeNormalizer();
        $serializer = new Serializer([$dateTimeNormalizer, $normalizer]);

        return $serializer;
    }
}