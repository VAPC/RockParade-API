<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\CreatorLoginTrait;
use AppBundle\Service\HashGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Table(name="events", uniqueConstraints={@ORM\UniqueConstraint(name="unique_events_date_name", columns={"date", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 * @author Vehsamrak
 */
class Event
{

    use CreatorLoginTrait;

    /**
     * @var string
     * @ORM\Column(name="id", type="string", length=8)
     * @ORM\Id
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="creator", referencedColumnName="login")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */

    /**
     * @var Image[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Image", orphanRemoval=true)
     * @ORM\JoinTable(name="event_images",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_name", referencedColumnName="name", unique=true)}
     *      )
     */
    private $images;

    public function __construct(
        string $name,
        User $creator,
        \DateTime $date,
        string $description,
        HashGenerator $hashGenerator = null
    )
    {
        $this->name = $name;
        $this->creator = $creator;
        $this->date = $date;
        $this->description = $description;
        $this->id = $hashGenerator ? $hashGenerator::generate() : HashGenerator::generate();
        $this->images = new ArrayCollection();
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return Image[]|PersistentCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return Image|null
     */
    public function getImageWithName(string $imageName)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', $imageName));
        $criteria->setMaxResults(1);

        /** @var ArrayCollection $imagesCollection */
        $imagesCollection = $this->images->matching($criteria);

        return $imagesCollection->first() ?: null;
    }

    public function addImage(Image $image)
    {
        $this->images->add($image);
    }

    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }
}
