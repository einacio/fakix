<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    const AS_OBJECT = 1;
    const AS_IDLIST = 2;

    public function __construct()
    {
    }

    public function normalize($user, $format = null, array $context = array()): array
    {

        if ($format == self::AS_OBJECT) {
            $data = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'isAdmin' => $user->getIsAdmin(),
                'groups' => $user->getGroups(),
            ];
        } else {
            $data = ['id'=>$user->getId()];
        }


        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
