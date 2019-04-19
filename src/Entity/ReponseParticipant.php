<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponseParticipantRepository")
 */
class ReponseParticipant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ParticipantQuiz",inversedBy="reponseparticipant")
     */
    private $participantquiz;

    /**
     * @return mixed
     */
    public function getParticipantquiz()
    {
        return $this->participantquiz;
    }

    /**
     * @param mixed $participantquiz
     */
    public function setParticipantquiz($participantquiz): void
    {
        $this->participantquiz = $participantquiz;
    }









    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question",inversedBy="rep")
     *
     */
    private $id_question;

    /**
     * @return mixed
     */
    public function getIdQuestion()
    {
        return $this->id_question;
    }

    /**
     * @param mixed $id_question
     */
    public function setIdQuestion($id_question): void
    {
        $this->id_question = $id_question;
    }


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
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $reponse;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }
}
