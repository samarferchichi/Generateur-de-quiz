<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultatRepository")
 */
class Resultat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;




    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ParticipantQuiz",inversedBy="resultat")
     *
     */
    private $participantquiz;

    public function getParticipantQuiz(): ?ParticipantQuiz
    {
        return $this->participantquiz;
    }

    public function setParticipantQuiz(?ParticipantQuiz $participantquiz): self
    {
        $this->participantquiz = $participantquiz;

        return $this;
    }




    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReponseParticipant", mappedBy="resultat", cascade={"persist","remove"})
     *
     */
    private $reponseparticipant;

    public function __construct()
    {
        $this->reponseparticipant = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->terminer = false;
    }
    /**
     * @return Collection|ReponseParticipant[]
     */
    public function getReponseParticipant(): Collection
    {
        return $this->reponseparticipant;
    }



    /**
     * @ORM\Column(type="integer")
     */
    private $tentative;

    /**
     * @ORM\Column(type="integer")
     */
    private $resultat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTentative(): ?int
    {
        return $this->tentative;
    }

    public function setTentative(int $tentative): self
    {
        $this->tentative = $tentative;

        return $this;
    }

    public function getResultat(): ?int
    {
        return $this->getResultat();
    }

    public function setResultat(int $resultat): self
    {
        $this->resultat = $resultat;

        return $this;
    }
}
