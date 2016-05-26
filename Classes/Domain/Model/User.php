<?php
namespace Wwwision\UserLikes\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * A User in this context is anyone that can like/recommend a Subject
 *
 * @Flow\Entity(readOnly=TRUE)
 * @Flow\Proxy(false)
 */
class User
{

    /**
     * @ORM\Id
     * @var string
     */
    protected $id;

    /**
     * An array containing unique IDs of entities the user "liked"
     *
     * @ORM\Column(type="json_array")
     * @var string[]
     */
    protected $likes = [];

    /**
     * @ORM\Version
     * @var int
     */
    protected $version = -1;

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
     * @return string[]
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Whether this user likes the given subject
     *
     * @param string $subjectId
     * @return bool
     */
    public function likes($subjectId)
    {
        return in_array($subjectId, $this->likes);
    }
}