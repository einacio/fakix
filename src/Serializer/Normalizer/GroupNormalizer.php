<?php

namespace App\Serializer\Normalizer;

use App\Entity\Group;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GroupNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    const AS_OBJECT = 1;
    const AS_IDLIST = 2;


    /**
     * @param Group $group
     * @param int $format
     * @param array $context
     * @return array
     */
    public function normalize($group, $format = null, array $context = array()): array
    {

        if ($format == self::AS_OBJECT) {
            $data = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'isAdmin' => $group->getIsAdmin(),
                'users' => $group->getUsers(),
            ];
        } else {
            $data = ['id'=>$group->getId()];
        }


        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Group;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
