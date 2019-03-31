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
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="parametre")
     *
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
