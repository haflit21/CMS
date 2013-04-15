<?php
namespace CMS\ContactBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CMS\ContactBundle\Entity\Contact
 *
 * @ORM\Table("contact")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="CMS\ContactBundle\Entity\Repository\ContactRepository")
 **/
class Contact
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string lastname
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Assert\NotBlank(message="contact.lastname.not_blank")
     */
    private $lastname;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Assert\NotBlank(message="contact.firstname.not_blank")
     **/
     private $firstname;

     /**
      * @var string $sender
      *
      * @ORM\Column(name="sender", type="string", length=255)
      * @Assert\NotBlank(message="contact.sender.not_blank")
      * @Assert\Email(message="{{ value }} contact.email.not_valid")
      */
     private $sender;

     /**
      * @var string $subject
      *
      * @ORM\Column(name="subject", type="string", length=255)
      * @Assert\NotBlank(message="contact.subject.not_blank")
      */
     private $subject;

     /**
      * @var string $message
      *
      * @ORM\Column(name="message", type="text")
      * @Assert\NotBlank(message="contact.message.no_blank")
      */
     private $message;

     /**
      * @var date $created
      *
      * @ORM\Column(name="created", type="date")
      */
     private $created;

     /**
      * @var boolean $statut
      *
      * @ORM\Column(name="statut", type="boolean")
      **/
     private $statut;

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
     * Set lastname
     *
     * @param  string  $lastname
     * @return Contact
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param  string  $firstname
     * @return Contact
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set sender
     *
     * @param  string  $sender
     * @return Contact
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set subject
     *
     * @param  string  $subject
     * @return Contact
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param  string  $message
     * @return Contact
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set statut
     *
     * @param  boolean $statut
     * @return Contact
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return boolean
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Contact
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set createdValue
     *
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
}
