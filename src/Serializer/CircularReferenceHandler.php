<?php

namespace App\Serializer;

use App\Entity\Country;
use Symfony\Component\Routing\RouterInterface;

class CircularReferenceHandler
{
    /**
     * @var RouterInterface
     */
    private $router;
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function __invoke($object)
    {
        switch ($object) {
            case $object instanceof Country:
                return $this->router->generate('get_countries', ['countries' => $object->getId()]);
        }
        return $object->getId();
    }
}