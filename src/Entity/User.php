<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * Clé primaire
     * @ORM\Id()
     * auto-increment
     * @ORM\GeneratedValue()
     * champ de type integer en bdd
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * lastname : varchar(100) NOT NULL en bdd
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;

    /**
     * fistname : varchar(100) NOT NULL en bdd
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * email : varchar(255) NOT NULL unique en bdd
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var \DateTime
     * birthdate : date NOT NULL en bdd
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @var Collection
     * le OneToMany (facultatif) permet d'accéder aux publications
     * depuis un objet User dans cet attribut
     * mappedBy dit quel attribut dans Publication définit la clé étrangere avec ManyToOne
     *
     * cascade={"persis"} fait que quand on enregistre un User,
     * ses publications sont enregistrées en même temps
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="author",cascade={"persist"},     fetch="EAGER")
     * //
     */
    private $publications;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="users")
     */
    private $teams;

    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
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

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }


    /**
     * @return Collection
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    /**
     * @param Collection $publications
     * @return User
     */
    public function setPublications(Collection $publications): User
    {
        $this->publications = $publications;
        return $this;
    }

    public function addPublication(Publication $publication)
    {
        // on ajoute la publication au user
        $this->publications->add($publication);
        // éq. $this->publications[] = $publication;

        // on sette l'auteur de la publication avec l'objet User
        // qui appelle la méthode
        $publication->setAuthor($this);
    }

    /**
     * @return Collection
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @param Collection $teams
     * @return User
     */
    public function setTeams(Collection $teams): User
    {
        $this->teams = $teams;
        return $this;
    }




    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }





}
