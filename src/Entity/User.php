<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
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