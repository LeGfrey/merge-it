<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
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
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="author")
     */
    private $feeds;

    /**
     * @ORM\OneToMany(targetEntity="MergedFeed", mappedBy="author")
     */
    private $mergedFeeds;

    /**
     * getFeed
     * 
     * @return array userFeeds
     */
    public function getFeeds() {
    	return $this->feeds;
    }

    /**
     * getMergedFeeds
     *
     * @return array userMergedFeeds
     */
    public function getMergedFeeds() {
        return $this->mergedFeeds;
    }
}