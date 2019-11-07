<?php

namespace AppBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use FOS\RestBundle\Context\Context;
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
class Serializor implements \FOS\RestBundle\Serializer\Serializer
{
    /**
     * @param $data
     * @param $format
     * @param $context
     * @return string
     * @throws AnnotationException
     */
    public function serialize($data, $format, Context $context): string
    {
        $classMetaDataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetaDataFactory);
        $dateTimeNormalizer = new DateTimeNormalizer();
        $serializer = new Serializer([$dateTimeNormalizer, $normalizer]);

        return $serializer->serialize($data, $format, $context->getGroups());
    }

    public function deserialize($data, $type, $format, Context $context)
    {
        // TODO: Implement deserialize() method.
    }
}