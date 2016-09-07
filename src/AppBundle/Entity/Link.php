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
     * @var string
     * @ORM\Column(name="url", type="string", length=255)
     * @ORM\Id
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
        $hashGenerator = $hashGenerator ?: new HashGenerator();
        $this->id = $hashGenerator::generate(self::ID_HASH_LENGTH);
        $this->url = $url;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
