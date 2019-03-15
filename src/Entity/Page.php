<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz",inversedBy="quiz", cascade={"persist","remove"})
     *
     */
    private $quiz;

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="page", cascade={"persist","remove"})
     */
    private $question;


    public function __construct()
    {
        $this->question = new ArrayCollection();
    }


    /**
     * @return Collection|Question[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }




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

    public function __toString() {
        return $this->titre_page;
    }
}
