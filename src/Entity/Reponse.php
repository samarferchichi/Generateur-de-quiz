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
    private $question;

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question): void
    {
        $this->question = $question;
    }


    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $formule;


    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $destext;



    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $desnumber;

    /**
     * @return mixed
     */
    public function getDesnumber()
    {
        return $this->desnumber;
    }

    /**
     * @param mixed $desnumber
     */
    public function setDesnumber($desnumber): void
    {
        $this->desnumber = $desnumber;
    }


    /**
     * @return mixed
     */
    public function getDestext()
    {
        return $this->destext;
    }

    /**
     * @param mixed $destext
     */
    public function setDestext($destext): void
    {
        $this->destext = $destext;
    }


    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $resultatformule;

    /**
     * @return mixed
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * @param mixed $formule
     */
    public function setFormule($formule): void
    {
        $this->formule = $formule;
    }

    /**
     * @return mixed
     */
    public function getResultatformule()
    {
        return $this->resultatformule;
    }

    /**
     * @param mixed $resultatformule
     */
    public function setResultatformule($resultatformule): void
    {
        $this->resultatformule = $resultatformule;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $descriptiondate;

    /**
     * @return mixed
     */
    public function getDescriptiondate()
    {
        return $this->descriptiondate;
    }

    /**
     * @param mixed $descriptiondate
     */
    public function setDescriptiondate($descriptiondate): void
    {
        $this->descriptiondate = $descriptiondate;
    }



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $descriptionformule;

    /**
     * @return mixed
     */
    public function getDescriptionformule()
    {
        return $this->descriptionformule;
    }

    /**
     * @param mixed $descriptionformule
     */
    public function setDescriptionformule($descriptionformule): void
    {
        $this->descriptionformule = $descriptionformule;
    }

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $reponse_valide;


    /**
     * @ORM\Column(type="boolean", nullable=true, nullable=true)
     */
    private $etatvf;

    /**
     * @ORM\Column(type="boolean", nullable=true, nullable=true)
     */
    private $etatlist;

    /**
     * @return mixed
     */
    public function getEtatlist()
    {
        return $this->etatlist;
    }

    /**
     * @param mixed $etatlist
     */
    public function setEtatlist($etatlist): void
    {
        $this->etatlist = $etatlist;
    }



    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatcaseacocher;

    /**
     * @return mixed
     */
    public function getEtatcaseacocher()
    {
        return $this->etatcaseacocher;
    }

    /**
     * @param mixed $etatcaseacocher
     */
    public function setEtatcaseacocher($etatcaseacocher): void
    {
        $this->etatcaseacocher = $etatcaseacocher;
    }


    /**
     * @return mixed
     */
    public function getEtatvf()
    {
        return $this->etatvf;
    }

    /**
     * @param mixed $etatvf
     */
    public function setEtatvf($etatvf): void
    {
        $this->etatvf = $etatvf;
    }



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
