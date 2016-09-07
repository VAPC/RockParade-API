<?php

namespace AppBundle\Entity;

use AppBundle\Service\HashGenerator;
use Doctrine\ORM\Mapping as ORM;

/**
 * URL Link
 * @ORM\Table(name="links")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\LinkRepository")
 */
class Link
{

    const ID_HASH_LENGTH = 32;

    /**
     * @var int
     * @ORM\Column(name="id", type="string", length=32)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @param string $url
     * @param string $description
     */
    public function __construct(string $url, string $description = null, HashGenerator $hashGenerator = null)
    {
        $this->id = $hashGenerator
            ? $hashGenerator::generate(self::ID_HASH_LENGTH)
            : HashGenerator::generate(self::ID_HASH_LENGTH);
        $this->url = $url;
        $this->description = $description;
    }
}
