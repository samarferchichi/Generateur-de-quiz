<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
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
    private $id_quiz;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre_page;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color_titre_page;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bg_color;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdQuiz(): ?int
    {
        return $this->id_quiz;
    }

    public function setIdQuiz(int $id_quiz): self
    {
        $this->id_quiz = $id_quiz;

        return $this;
    }

    public function getTitrePage(): ?string
    {
        return $this->titre_page;
    }

    public function setTitrePage(string $titre_page): self
    {
        $this->titre_page = $titre_page;

        return $this;
    }

    public function getColorTitrePage(): ?string
    {
        return $this->color_titre_page;
    }

    public function setColorTitrePage(?string $color_titre_page): self
    {
        $this->color_titre_page = $color_titre_page;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bg_color;
    }

    public function setBgColor(?string $bg_color): self
    {
        $this->bg_color = $bg_color;

        return $this;
    }
}