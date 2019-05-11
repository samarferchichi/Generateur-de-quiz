<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ParticipantQuiz", mappedBy="participant", cascade={"persist","remove"})
     */
    private $participantquiz;

    /**
     * @return mixed
     */
    public function getParticipantquiz()
    {
        return $this->participantquiz;
    }

    /**
     * @param mixed $participantquiz
     */
    public function setParticipantquiz($participantquiz): void
    {
        $this->participantquiz = $participantquiz;
    }


    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }



    public function __toString()
    {
        return $this->getEmail();
    }

}
