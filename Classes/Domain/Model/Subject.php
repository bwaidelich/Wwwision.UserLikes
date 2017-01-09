<?php
namespace Wwwision\UserLikes\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * A subject is anything that can be liked/recommended (e.g. a product, company, post, ...)
 *
 * @Flow\Entity(readOnly=TRUE)
 * @Flow\Proxy(false)
 */
class Subject
{

    /**
     * @ORM\Id
     * @var string
     */
    protected $id;

    /**
     * @var integer
     */
    protected $numberOfLikes = 0;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getNumberOfLikes()
    {
        return $this->numberOfLikes;
    }

}