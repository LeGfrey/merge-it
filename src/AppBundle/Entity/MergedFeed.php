<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MergedFeed
 *
 * @ORM\Table(name="merged_feed")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MergedFeedRepository")
 */
class MergedFeed
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Feed", inversedBy="mergedFeeds")
     * @ORM\JoinTable(name="feeds_mergedfeeds")
     */
    private $feeds;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mergedFeeds")
     * @ORM\JoinColumn(name="author")
     */
    private $author;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MergedFeed
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
     * Get the feeds to merge
     * 
     * @return array feeds
     */
    public function getFeeds() {
        return $this->feeds;
    }

    /**
     * Set the feeds to merge
     * 
     * @param array feeds
     * @return MergedFeed
     */
    public function setFeeds($feeds) {
        $this->feeds = $feeds;

        return $this;
    }

    /**
     * Get the author of the merged feed
     *
     * @return User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set the author of the merged feed
     *
     * @param User $author
     * @return $this
     */
    public function setAuthor($author) {
        $this->author = $author;

        return $this;
    }
}

