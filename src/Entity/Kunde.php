<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\KundeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: KundeRepository::class)]
#[ORM\Table(name: 'std.tbl_kunden')]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/kunden/{id}'),
        new Put(uriTemplate: '/kunden/{id}'),
        new Delete(uriTemplate: '/kunden/{id}'),
        new Post(uriTemplate: '/kunden'),
        new GetCollection(uriTemplate: '/kunden'),
    ],
    normalizationContext: ['groups' => ['kunde']],
    denormalizationContext: ['groups' => ['post']],
)]
class Kunde
{
    private const GESCHLAECHT = ['männlich', 'weiblich', 'divers'];

    public function __construct()
    {
        $this->adressen = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::STRING, length: 36, nullable: true)]
    #[Groups('kunde')]
    #[ApiProperty(
        identifier: true,
        openapiContext: [
            'type' => 'string',
            'format' => 'uuid',
            'description' => 'UUID des Kunden',
            'example' => 'E97DEF37',
            'required' => true,
        ]
    )]
    private ?string $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['kunde', 'post'])]
    #[Assert\NotBlank]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'Nachname des Kunden',
            'example' => 'Mustermann',
            'required' => true,
        ]
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['kunde', 'post'])]
    #[Assert\NotBlank]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'Vorname des Kunden',
            'example' => 'Max',
            'required' => true,
        ]
    )]
    private ?string $vorname = null;

    #[Groups(['post'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'Firma des Kunden',
            'example' => 'Max Musterman GmbH',
            'required' => false,
        ]
    )]
    private ?string $firma = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['kunde', 'post'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotBlank]
    #[ApiProperty(
        openapiContext: [
            'type' => 'date',
            'description' => 'Geburtsdatum des Kunden',
            'example' => '2001-01-01',
            'required' => false,
        ]
    )]
    private ?\DateTimeImmutable $geburtsdatum = null;

    #[ORM\Column(nullable: true)]
    private ?int $geloescht = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['post'])]
    #[Assert\Choice(choices: self::GESCHLAECHT)]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'Geschlecht des Kunden',
            'example' => 'männlich',
            'enum' => self::GESCHLAECHT,
            'required' => false,
        ]
    )]
    private ?string $geschlecht = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['kunde', 'post'])]
    #[Assert\Email]
    #[ApiProperty(
        openapiContext: [
            'type' => 'date',
            'description' => 'Email des Kunden',
            'example' => 'max.musterman@foo.de',
            'required' => false,
        ]
    )]
    private ?string $email = null;

    #[Groups('kunde')]
    private ?string $vermittlerId = null;

    #[ORM\ManyToOne(targetEntity: Vermittler::class, inversedBy: 'kunden')]
    #[ORM\JoinTable(name: 'std.vermittler')]
    #[ORM\JoinColumn(name: 'vermittler_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['post'])]
    private ?Vermittler $vermittler = null;

    #[ORM\JoinTable(name: 'std.kunde_adresse')]
    #[ORM\JoinColumn(name: 'kunde_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'adresse_id', referencedColumnName: 'adresse_id')]
    #[ORM\ManyToMany(targetEntity: Adresse::class)]
    #[Groups('kunde')]
    private Collection $adressen;

    #[ORM\OneToOne(mappedBy: 'kundenId', cascade: ['persist', 'remove'])]
    #[Groups('kunde')]
    private ?User $user = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVorname(): ?string
    {
        return $this->vorname;
    }

    public function setVorname(?string $vorname): self
    {
        $this->vorname = $vorname;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->firma;
    }

    public function setFirma(?string $firma): self
    {
        $this->firma = $firma;

        return $this;
    }

    public function getGeburtsdatum(): ?\DateTimeImmutable
    {
        return $this->geburtsdatum;
    }

    public function setGeburtsdatum(?\DateTimeImmutable $geburtsdatum): self
    {
        $this->geburtsdatum = $geburtsdatum;

        return $this;
    }

    public function getGeloescht(): ?int
    {
        return $this->geloescht;
    }

    public function setGeloescht(?int $geloescht): self
    {
        $this->geloescht = $geloescht;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGeschlecht(): ?string
    {
        return $this->geschlecht;
    }

    /**
     * @param string|null $geschlecht
     */
    public function setGeschlecht(?string $geschlecht): void
    {
        $this->geschlecht = $geschlecht;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getVermittlerId(): int
    {
        return $this->vermittler->getId();
    }

    public function getVermittler(): ?Vermittler
    {
        return $this->vermittler;
    }

    public function setVermittler(?Vermittler $vermittlerId): self
    {
        $this->vermittler = $vermittlerId;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAdressen(): Collection
    {
        return $this->adressen;
    }

    /**
     * @param Collection $adressen
     */
    public function setAdressen(Collection $adressen): void
    {
        $this->adressen = $adressen;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setKundenid(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getKundenid() !== $this) {
            $user->setKundenid($this);
        }

        $this->userAccount = $user;

        return $this;
    }
}