<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page",inversedBy="page",  cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }


    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $text_question;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $type_question;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $info_bulle;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $description_question;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif): void
    {
        $this->actif = $actif;
    }



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getTextQuestion(): ?string
    {
        return $this->text_question;
    }

    public function setTextQuestion(string $text_question): self
    {
        $this->text_question = $text_question;

        return $this;
    }

    public function getTypeQuestion(): ?string
    {
        return $this->type_question;
    }

    public function setTypeQuestion(string $type_question): self
    {
        $this->type_question = $type_question;

        return $this;
    }

    public function getInfoBulle(): ?string
    {
        return $this->info_bulle;
    }

    public function setInfoBulle(?string $info_bulle)
    {
        $this->info_bulle = $info_bulle;

        return $this;
    }

    public function getDescriptionQuestion(): ?string
    {
        return $this->description_question;
    }

    public function setDescriptionQuestion(?string $description_question)
    {
        $this->description_question = $description_question;

        return $this;
    }


    public function __toString() {
        return $this->text_question;
    }
}
