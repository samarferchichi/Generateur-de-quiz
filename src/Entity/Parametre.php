<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametreRepository")
 */
class Parametre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * One Cart has One Customer.
     * @ORM\OneToOne(targetEntity="Question", inversedBy="parametre")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $form_text;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pdf;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $doc;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $png;

    /**
     * @return mixed
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param mixed $pdf
     */
    public function setPdf($pdf): void
    {
        $this->pdf = $pdf;
    }

    /**
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $doc
     */
    public function setDoc($doc): void
    {
        $this->doc = $doc;
    }

    /**
     * @return mixed
     */
    public function getPng()
    {
        return $this->png;
    }

    /**
     * @param mixed $png
     */
    public function setPng($png): void
    {
        $this->png = $png;
    }

    /**
     * @return mixed
     */
    public function getJpg()
    {
        return $this->jpg;
    }

    /**
     * @param mixed $jpg
     */
    public function setJpg($jpg): void
    {
        $this->jpg = $jpg;
    }


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jpg;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $gif;

    /**
     * @return mixed
     */
    public function getGif()
    {
        return $this->gif;
    }

    /**
     * @param mixed $gif
     */
    public function setGif($gif): void
    {
        $this->gif = $gif;
    }




    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_caractere;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_chiffre;

    /**
     * @return mixed
     */
    public function getNbChiffre()
    {
        return $this->nb_chiffre;
    }

    /**
     * @param mixed $nb_chiffre
     */
    public function setNbChiffre($nb_chiffre): void
    {
        $this->nb_chiffre = $nb_chiffre;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFormText()
    {
        return $this->form_text;
    }

    /**
     * @param mixed $form_text
     */
    public function setFormText($form_text): void
    {
        $this->form_text = $form_text;
    }

    /**
     * @return mixed
     */
    public function getNbCaractere()
    {
        return $this->nb_caractere;
    }

    /**
     * @param mixed $nb_caractere
     */
    public function setNbCaractere($nb_caractere): void
    {
        $this->nb_caractere = $nb_caractere;
    }




}
