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

    /**
     * @var int
     * @ORM\Column(name="id", type="string", length=32)
     * @ORM\Id
     * @Serializer\Exclude()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __construct(string $name, HashGenerator $hashGenerator = null)
    {
        $hashGenerator = $hashGenerator ?: new HashGenerator();
        $this->id = $hashGenerator::generate(32);
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
