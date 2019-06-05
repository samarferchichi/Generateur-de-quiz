<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */


class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Quiz", mappedBy="user", cascade={"persist","remove"})
     */
    private $quiz;

    public function __construct()
    {
        $this->quiz = new ArrayCollection();
        $this->quiz = new ArrayCollection();
    }
    /**
     * @return Collection|QUiz[]
     */
    public function getQuiz(): Collection
    {
        return $this->quiz;
    }

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     */
    protected $avatar;

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }


    /**
     * @ORM\Column(type="string")
     *
     * @ORM\Column(name="cellphone", type="string", length=25, nullable=true)
     *
     * @Assert\Length(
     *     min=8,
     *     max=8,
     *     minMessage="Your cell phone is too short.",
     *     maxMessage="Your cell phone is too long.",
     *     groups={"Registration", "Profile"}
     * )
     *
     *
     */
    private $numerotel;

    /**
     * @return mixed
     */
    public function getNumerotel()
    {
        return $this->numerotel;
    }

    /**
     * @param mixed $numerotel
     */
    public function setNumerotel($numerotel): void
    {
        $this->numerotel = $numerotel;
    }


}