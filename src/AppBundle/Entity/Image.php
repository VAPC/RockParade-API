<?php

namespace AppBundle\Entity;

use AppBundle\Service\HashGenerator;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Images files links
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ImageRepository")
 */
class Image
{

    const ID_HASH_LENGTH = 32;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __construct(string $name, HashGenerator $hashGenerator = null)
    {
        $hashGenerator = $hashGenerator ?: new HashGenerator();
        $this->name = sprintf('%s-%s', $hashGenerator::generate(self::ID_HASH_LENGTH), $name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
