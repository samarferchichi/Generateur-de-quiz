<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantQuizRepository")
 */
class ParticipantQuiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;












    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReponseParticipant", mappedBy="participantquiz", cascade={"persist","remove"})
     */
    private $reponseparticipant;



    public function __construct()
    {
        $this->reponseparticipant = new ArrayCollection();
    }
    /**
     * @return Collection|ReponseParticipant[]
     */
    public function getreponseparticipant(): Collection
    {
        return $this->reponseparticipant;
    }

















    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @param mixed $quiz
     */
    public function setQuiz($quiz): void
    {
        $this->quiz = $quiz;
    }


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="participantquiz")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="participantquiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;




}
