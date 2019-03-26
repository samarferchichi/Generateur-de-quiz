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
     * @ORM\Column(type="boolean")
     */
    private $form_text;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_caractere;


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
