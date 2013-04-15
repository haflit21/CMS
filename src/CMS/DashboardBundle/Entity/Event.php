<?php
namespace CMS\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="CMS\DashboardBundle\Entity\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_debut;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_fin;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date_debut
     *
     * @param  \DateTime $dateDebut
     * @return Event
     */
    public function setDateDebut($dateDebut)
    {
        if (!is_object($dateDebut)) {
            $dateDebut = new \Datetime($dateDebut);
        }
        $this->date_debut = $dateDebut;

        return $this;
    }

    /**
     * Get date_debut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->date_debut;
    }

    /**
     * Set date_fin
     *
     * @param  \DateTime $dateFin
     * @return Event
     */
    public function setDateFin($dateFin)
    {

         if (!is_object($dateFin)) {
            $dateFin = new \Datetime($dateFin);
        }
        $this->date_fin = $dateFin;

        return $this;
    }

    /**
     * Get date_fin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->date_fin;
    }
}
