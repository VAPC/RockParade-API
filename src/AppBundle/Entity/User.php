<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\FormattedRegistrationDateTrait;
use AppBundle\Service\HashGenerator;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type as SerializerType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 */
class User implements UserInterface
{

    use FormattedRegistrationDateTrait;

    const TOKEN_LENGTH = 32;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
     * @Serializer\Exclude()
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="vkontakte_id", type="string", length=50, nullable=false, unique=true)
     * @Serializer\Exclude()
     */
    private $vkontakteId;

    /**
     * @var string
     * @ORM\Column(name="vk_token", type="string", length=85, nullable=false)
     * @Serializer\Exclude()
     */
    private $vkToken;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=32, nullable=false, unique=true)
     * @Serializer\Exclude()
     */
    private $token;

    /**
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime", nullable=false)
     * @SerializedName("registration_date")
     * @Accessor(getter="getRegistrationDate")
     * @SerializerType("string")
     */
    private $registrationDate;

    /**
     * @var array
     * @ORM\Column(name="roles", type="simple_array", nullable=true)
     * @Serializer\Exclude()
     */
    private $roles;

    public function __construct(
        string $login,
        string $name,
        int $vkontakteId,
        string $vkToken,
        string $email = null,
        string $description = null,
        string $token = null
    ) {
        $this->login = $login;
        $this->name = $name;
        $this->vkontakteId = $vkontakteId;
        $this->vkToken = $vkToken;
        $this->token = $token ?: HashGenerator::generate(self::TOKEN_LENGTH);
        $this->email = $email ?: null;
        $this->description = $description;
        $this->registrationDate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /** {@inheritDoc} */
    public function getRoles(): array
    {
        return array_merge(
            $this->roles,
            [
                'ROLE_USER',
            ]
        );
    }

    public function setVkToken(string $vkToken)
    {
        $this->vkToken = $vkToken;
    }

    public function updateToken()
    {
        $this->token = HashGenerator::generate(self::TOKEN_LENGTH);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    /** {@inheritDoc} */
    public function getPassword() {}

    /** {@inheritDoc} */
    public function getSalt() {}

    /** {@inheritDoc} */
    public function eraseCredentials() {}

    /** {@inheritDoc} */
    public function getUsername(): string
    {
        return $this->getLogin();
    }
}
