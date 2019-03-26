<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponseRepository")
 */
class Reponse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question",inversedBy="reponse")
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
     * @ORM\Column(type="string", length=2000)
     */
    private $reponse_valide;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getReponseValide(): ?string
    {
        return $this->reponse_valide;
    }

    public function setReponseValide(string $reponse_valide): self
    {
        $this->reponse_valide = $reponse_valide;

        return $this;
    }
}
