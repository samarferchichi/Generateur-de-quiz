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
     * @ORM\ManyToOne(targetEntity="App\Entity\Resultat",inversedBy="reponseparticipant")
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
