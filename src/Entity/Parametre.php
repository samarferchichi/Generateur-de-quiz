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
     * @ORM\OneToOne(targetEntity="App\Entity\Question", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
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
    private $rep_form_text;

    /**
     * @ORM\Column(type="date")
     */
    private $rep_form_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $rep_form_nombre;

    /**
     * @ORM\Column(type="integer")
     */
    private $longueur_min;

    /**
     * @ORM\Column(type="integer")
     */
    private $longueur_max;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getRepFormText(): ?string
    {
        return $this->rep_form_text;
    }

    public function setRepFormText(string $rep_form_text): self
    {
        $this->rep_form_text = $rep_form_text;

        return $this;
    }

    public function getRepFormDate(): ?\DateTimeInterface
    {
        return $this->rep_form_date;
    }

    public function setRepFormDate(\DateTimeInterface $rep_form_date): self
    {
        $this->rep_form_date = $rep_form_date;

        return $this;
    }

    public function getRepFormNombre(): ?int
    {
        return $this->rep_form_nombre;
    }

    public function setRepFormNombre(int $rep_form_nombre): self
    {
        $this->rep_form_nombre = $rep_form_nombre;

        return $this;
    }

    public function getLongueurMin(): ?int
    {
        return $this->longueur_min;
    }

    public function setLongueurMin(int $longueur_min): self
    {
        $this->longueur_min = $longueur_min;

        return $this;
    }

    public function getLongueurMax(): ?int
    {
        return $this->longueur_max;
    }

    public function setLongueurMax(int $longueur_max): self
    {
        $this->longueur_max = $longueur_max;

        return $this;
    }
    public function __toString() {
        return $this->rep_form_text;
    }
}
