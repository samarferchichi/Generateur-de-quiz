<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantQuizRepository")
 */
class ParticipantQuiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;






    /**
     * @ORM\Column(type="integer")
     */
    private $tentative;

    /**
     * @return mixed
     */
    public function getTentative()
    {
        return $this->tentative;
    }

    /**
     * @param mixed $tentative
     */
    public function setTentative($tentative): void
    {
        $this->tentative = $tentative;
    }






    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReponseParticipant", mappedBy="participantquiz", cascade={"persist","remove"})
     */
    private $reponseparticipant;



    public function __construct()
    {
        $this->reponseparticipant = new ArrayCollection();
    }
    /**
     * @return Collection|ReponseParticipant[]
     */
    public function getreponseparticipant(): Collection
    {
        return $this->reponseparticipant;
    }







    /**
     * @ORM\Column(type="integer")
     */
    private $resultat;

    /**
     * @return mixed
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * @param mixed $resultat
     */
    public function setResultat($resultat): void
    {
        $this->resultat = $resultat;
    }




    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param mixed $user
     */
    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return mixed
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @param mixed $quiz
     */
    public function setQuiz($quiz): void
    {
        $this->quiz = $quiz;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Participant", inversedBy="participantquiz")
     * @ORM\JoinColumn(name="participant", referencedColumnName="id")
     */
    private $participant;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="participantquiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;



    public function __toString()
    {
        return $this->getQuiz()->getTitre();
    }


}
