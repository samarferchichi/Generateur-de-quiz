<?php

namespace App\Entity;

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
     * @ORM\Column(type="integer")
     */
    private $id_page;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $text_question;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $type_question;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $info_bulle;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $description_question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPage(): ?int
    {
        return $this->id_page;
    }

    public function setIdPage(int $id_page): self
    {
        $this->id_page = $id_page;

        return $this;
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

    public function setInfoBulle(string $info_bulle): self
    {
        $this->info_bulle = $info_bulle;

        return $this;
    }

    public function getDescriptionQuestion(): ?string
    {
        return $this->description_question;
    }

    public function setDescriptionQuestion(string $description_question): self
    {
        $this->description_question = $description_question;

        return $this;
    }
}
