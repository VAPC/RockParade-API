<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\FormattedRegistrationDateTrait;
use AppBundle\Service\HashGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
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
     * @var Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_login", referencedColumnName="login")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_name", referencedColumnName="name")}
     *     )
     * @Accessor(getter="getRolesNames")
     * @SerializerType("array")
     */
    private $roles;

    public function __construct(
        string $login,
        string $name,
        int $vkontakteId,
        string $vkToken,
        string $email = null,
        string $description = null
    ) {
        $this->login = $login;
        $this->name = $name;
        $this->vkontakteId = $vkontakteId;
        $this->vkToken = $vkToken;
        $this->token = HashGenerator::generate(self::TOKEN_LENGTH);
        $this->email = $email;
        $this->description = $description;
        $this->registrationDate = new \DateTime();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return PersistentCollection|Role[]
     */
    public function getRoles(): PersistentCollection
    {
        return $this->roles;
    }

    /**
     * @return string[]
     */
    public function getRolesNames(): array
    {
        return array_map(
            function (Role $role) {
                return $role->getName();
            },
            $this->getRoles()->toArray()
        );
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);
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
