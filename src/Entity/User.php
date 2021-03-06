<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Controller\Action\GetCurrentUserAction;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ApiResource(
 *     accessControl="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "validation_groups"={"Default", "create"}
 *          },
 *     },
 *     itemOperations={
 *          "get",
 *          "put"={"security"="is_granted('ROLE_USER') and object == user"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 */
class User implements UserInterface
{
    use TimestampableTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="ulid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UlidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"user:read", "user:write", "portfolio:item:get"})
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"admin:read", "admin:write"})
     */
    private array $roles = [];

    /**
     * @Groups("user:write")
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create"})
     */
    private ?string $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private string $password;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:read", "admin:write"})
     */
    private bool $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Portfolio::class, mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"user:read", "user:write"})
     * @Assert\Valid()
     */
    private $portfolios;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"admin:read","owner:read", "user:write"})
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"admin:read","owner:read", "user:write"})
     */
    private ?string $lastname;

    public function __construct()
    {
        $this->portfolios = new ArrayCollection();
    }

    public function setId(Ulid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
        $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Portfolio[]
     */
    public function getPortfolios(): Collection
    {
        return $this->portfolios;
    }

    public function addPortfolio(Portfolio $portfolio): self
    {
        if (!$this->portfolios->contains($portfolio)) {
            $this->portfolios[] = $portfolio;
            $portfolio->setUser($this);
        }

        return $this;
    }

    public function removePortfolio(Portfolio $portfolio): self
    {
        if ($this->portfolios->removeElement($portfolio)) {
            // set the owning side to null (unless already changed)
            if ($portfolio->getUser() === $this) {
                $portfolio->setUser(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get all transactions of an user
     */
    public function getAllTransactions()
    {
        $arr = [];
        foreach ($this->getPortfolios() as $portfolio){
            foreach ($portfolio->getTransactions() as $transaction){
                array_push($arr, $transaction);
            }
        }
        return $arr;
    }
}
