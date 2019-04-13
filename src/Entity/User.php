<?php

namespace App\Entity;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Quiz", mappedBy="user", cascade={"persist","remove"})
     */
    private $quiz;

    public function __constructa()
    {
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
     * @ORM\Column(type="string")
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




    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}