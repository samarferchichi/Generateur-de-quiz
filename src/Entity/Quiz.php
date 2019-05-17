<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\HttpFoundation\File\File;
/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="quiz", cascade={"persist","remove"})
     * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $page;

    public function __construct()
    {
        $this->page = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->terminer = false;
    }
    /**
     * @return Collection|Page[]
     */
    public function getPage(): Collection
    {
        return $this->page;
    }



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ParticipantQuiz", mappedBy="quiz", cascade={"persist","remove"})
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecreerquiz = null;

    /**
     * @return mixed
     */
    public function getDatecreerquiz()
    {
        return $this->datecreerquiz;
    }

    /**
     * @param mixed $datecreerquiz
     */
    public function setDatecreerquiz($datecreerquiz): void
    {
        $this->datecreerquiz = $datecreerquiz;
    }


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="quiz")
     *
     */
    private $user;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $terminer;

    /**
     * @return mixed
     */
    public function getTerminer()
    {
        return $this->terminer;
    }

    /**
     * @param mixed $terminer
     */
    public function setTerminer($terminer): void
    {
        $this->terminer = $terminer;
    }


    /**
     * @ORM\Column(type="string")
     *
     */
    private $brochure;

    public function getBrochure()
    {
        return $this->brochure;
    }

    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }

    /**
     * @ORM\Column(type="string")
     *
     *
     */
private $categorie;

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     */
    public function setCategorie($categorie): void
    {
        $this->categorie = $categorie;
    }


    /**
     * @ORM\Column(type="string", length=300)
     */
    private $titre;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $color_titre;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $police;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $gras;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $italique;




    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $description;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mode_correction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $temps_dispo;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_question;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_page;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_tentative;
    /**
     * @return mixed
     */
    public function getNbTentative()
    {
        return $this->nb_tentative;
    }
    /**
     * @param mixed $nb_tentative
     */
    public function setNbTentative($nb_tentative): void
    {
        $this->nb_tentative = $nb_tentative;
    }
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $melange_question;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $ouvrire_quiz = null;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fermer_quiz = null;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $send_mail;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $imprime_pdf;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $entete;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $pied;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $message_s;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $message_e;




    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timequiz;

    /**
     * @return mixed
     */
    public function getTimequiz()
    {
        return $this->timequiz;
    }

    /**
     * @param mixed $timequiz
     */
    public function setTimequiz($timequiz): void
    {
        $this->timequiz = $timequiz;
    }


    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTitre(): ?string
    {
        return $this->titre;
    }
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }
    public function getColorTitre(): ?string
    {
        return $this->color_titre;
    }
    public function setColorTitre(?string $color_titre): self
    {
        $this->color_titre = $color_titre;
        return $this;
    }
    public function getPolice(): ?string
    {
        return $this->police;
    }
    public function setPolice(?string $police): self
    {
        $this->police = $police;
        return $this;
    }
    public function getGras(): ?bool
    {
        return $this->gras;
    }
    public function setGras(?bool $gras): self
    {
        $this->gras = $gras;
        return $this;
    }
    public function getItalique(): ?bool
    {
        return $this->italique;
    }
    public function setItalique(?bool $italique): self
    {
        $this->italique = $italique;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function getModeCorrection(): ?bool
    {
        return $this->mode_correction;
    }
    public function setModeCorrection(bool $mode_correction): self
    {
        $this->mode_correction = $mode_correction;
        return $this;
    }

    public function getTempsDispo()
    {
        return $this->temps_dispo;
    }
    public function setTempsDispo( $temps_dispo): self
    {
        $this->temps_dispo = $temps_dispo;
        return $this;
    }
    public function getNbQuestion(): ?int
    {
        return $this->nb_question;
    }
    public function setNbQuestion(int $nb_question): self
    {
        $this->nb_question = $nb_question;
        return $this;
    }
    public function getNbPage(): ?int
    {
        return $this->nb_page;
    }
    public function setNbPage(int $nb_page): self
    {
        $this->nb_page = $nb_page;
        return $this;
    }
    public function getMelangeQuestion(): ?bool
    {
        return $this->melange_question;
    }
    public function setMelangeQuestion(?bool $melange_question): self
    {
        $this->melange_question = $melange_question;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOuvrireQuiz()
    {
        return $this->ouvrire_quiz;
    }

    /**
     * @param mixed $ouvrire_quiz
     */
    public function setOuvrireQuiz($ouvrire_quiz): void
    {
        $this->ouvrire_quiz = $ouvrire_quiz;
    }

    /**
     * @return mixed
     */
    public function getFermerQuiz()
    {
        return $this->fermer_quiz;
    }

    /**
     * @param mixed $fermer_quiz
     */
    public function setFermerQuiz($fermer_quiz): void
    {
        $this->fermer_quiz = $fermer_quiz;
    }


    public function getSendMail(): ?bool
    {
        return $this->send_mail;
    }
    public function setSendMail(?bool $send_mail): self
    {
        $this->send_mail = $send_mail;
        return $this;
    }
    public function getImprimePdf(): ?bool
    {
        return $this->imprime_pdf;
    }
    public function setImprimePdf(?bool $imprime_pdf): self
    {
        $this->imprime_pdf = $imprime_pdf;
        return $this;
    }
    public function getEntete(): ?string
    {
        return $this->entete;
    }
    public function setEntete(?string $entete): self
    {
        $this->entete = $entete;
        return $this;
    }
    public function getPied(): ?string
    {
        return $this->pied;
    }
    public function setPied(string $pied): self
    {
        $this->pied = $pied;
        return $this;
    }
    public function getMessageS(): ?string
    {
        return $this->message_s;
    }
    public function setMessageS(string $message_s): self
    {
        $this->message_s = $message_s;
        return $this;
    }
    public function getMessageE(): ?string
    {
        return $this->message_e;
    }
    public function setMessageE(string $message_e): self
    {
        $this->message_e = $message_e;
        return $this;
    }





    public function __toString() {
        return $this->titre;
    }

}
