<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    const VALID_TOKEN_MINUTES = 20;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="{{ value }} n'est pas un nombre entier."
     * )
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }} doit etre une chaine de caractères."
     * )
     * @Assert\Email(
     *     message = "Veuillez renseigner votre adresse mail.",
     *     checkMX = true
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 180,
     *      minMessage = "Votre email est trop court",
     *      maxMessage = "Votre email est trop long"
     * )
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @var string
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }} doit etre une chaine de caractères."
     * )
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "Votre mot de passe doit faire au moins 8 caracteres",
     * )
     * @Assert\Regex(
     *     pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/",
     *     message = "Votre mot de passe doit faire minimum 8 caractères et contenir au moins une
      minuscule une majuscule et un chiffre"
     * )
     * @Assert\EqualTo(
     *     propertyPath="confirm_password",
     *     message="Vous avez mal ré-entré ce mot de passe dans le champ de confirmation"
     * )
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $confirmPassword;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }} doit etre une chaine de caractères."
     * )
     * @Assert\Length(
     *      min = 1,
     *      max = 180,
     *      minMessage = "Votre prénom est trop court",
     *      maxMessage = "Votre prénom est trop long"
     * )
     * @Assert\Regex("/^[- 'a-zA-ZÀ-ÖØ-öø-ÿ]{0,255}$/")
     * @var string
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }} doit etre une chaine de caractères."
     * )
     * @Assert\Length(
     *      min = 1,
     *      max = 180,
     *      minMessage = "Votre nom est trop court",
     *      maxMessage = "Votre nom est trop long"
     * )
     * @Assert\Regex("/^[- 'a-zA-ZÀ-ÖØ-öø-ÿ]{0,255}$/")
     * @var string
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Property", mappedBy="userProperty")
     * @var ArrayCollection
     */
    private $properties;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lessee", mappedBy="userLessee")
     * @var ArrayCollection
     */
    private $lessees;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }} doit etre une chaine de caractères."
     * )
     * @var string
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RentRelease", mappedBy="userRentRelease")
     * @Assert\NotBlank
     * @var ArrayCollection
     */
    private $rentReleases;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @var string A "Y-m-d H:i:s" formatted value
     */
    private $createdAtToken;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->lessees = new ArrayCollection();
        $this->rentReleases = new ArrayCollection();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = '';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Collection
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param Property $property
     * @return User
     */
    public function addProperty(Property $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties[] = $property;
            $property->setUserProperty($this);
        }

        return $this;
    }

    /**
     * @param Property $property
     * @return User
     */
    public function removeProperty(Property $property): self
    {
        if ($this->properties->contains($property)) {
            $this->properties->removeElement($property);
            // set the owning side to null (unless already changed)
            if ($property->getUserProperty() === $this) {
                $property->setUserProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Lessee[]
     */
    public function getLessees(): Collection
    {
        return $this->lessees;
    }

    /**
     * @param Lessee $lessee
     * @return User
     */
    public function addLessee(Lessee $lessee): self
    {
        if (!$this->lessees->contains($lessee)) {
            $this->lessees[] = $lessee;
            $lessee->setUserLessee($this);
        }

        return $this;
    }

    /**
     * @param Lessee $lessee
     * @return User
     */
    public function removeLessee(Lessee $lessee): self
    {
        if ($this->lessees->contains($lessee)) {
            $this->lessees->removeElement($lessee);
            // set the owning side to null (unless already changed)
            if ($lessee->getUserLessee() === $this) {
                $lessee->setUserLessee(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return User
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection|RentRelease[]
     */
    public function getRentReleases(): Collection
    {
        return $this->rentReleases;
    }

    /**
     * @param RentRelease $rentRelease
     * @return User
     */
    public function addRentRelease(RentRelease $rentRelease): self
    {
        if (!$this->rentReleases->contains($rentRelease)) {
            $this->rentReleases[] = $rentRelease;
            $rentRelease->setUserRentRelease($this);
        }

        return $this;
    }

    /**
     * @param RentRelease $rentRelease
     * @return User
     */
    public function removeRentRelease(RentRelease $rentRelease): self
    {
        if ($this->rentReleases->contains($rentRelease)) {
            $this->rentReleases->removeElement($rentRelease);
            // set the owning side to null (unless already changed)
            if ($rentRelease->getUserRentRelease() === $this) {
                $rentRelease->setUserRentRelease(null);
            }
        }

        return $this;
    }

    public function getCreatedAtToken(): ?\DateTimeInterface
    {
        return $this->createdAtToken;
    }

    public function setCreatedAtToken(?\DateTimeInterface $createdAtToken): self
    {
        $this->createdAtToken = $createdAtToken;

        return $this;
    }
}
