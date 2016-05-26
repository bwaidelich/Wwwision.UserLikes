<?php
namespace Wwwision\UserLikes;

use TYPO3\Flow\Annotations as Flow;
use Wwwision\Eventr\Eventr;
use Wwwision\Eventr\ExpectedVersion;
use Wwwision\UserLikes\Domain\Model\Subject;
use Wwwision\UserLikes\Domain\Model\User;
use Wwwision\UserLikes\Domain\Repository\SubjectRepository;
use Wwwision\UserLikes\Domain\Repository\UserRepository;

/**
 * Central authority to deal with users & subjects
 *
 * @Flow\Scope("singleton")
 */
class LikeService
{

    /**
     * @Flow\Inject
     * @var SubjectRepository
     */
    protected $subjectRepository;

    /**
     * @Flow\Inject
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @Flow\Inject
     * @var Eventr
     */
    protected $eventr;

    /**
     * @param string $userId
     * @param string $subjectId
     * @param int $expectVersion
     * @return void
     * @throws \RuntimeException
     */
    public function addLike($userId, $subjectId, $expectVersion = ExpectedVersion::ANY)
    {
        if ($this->getUser($userId)->likes($subjectId)) {
            throw new \RuntimeException(sprintf('User "%s" already likes subject "%s"!', $userId, $subjectId), 1464082621);
        }
        $this->eventr->getAggregate('User', $userId)->emitEvent('likeAdded', ['subjectId' => $subjectId], $expectVersion);
    }

    /**
     * @param string $userId
     * @param string $subjectId
     * @param int $expectVersion
     * @return void
     * @throws \RuntimeException
     */
    public function revokeLike($userId, $subjectId, $expectVersion = ExpectedVersion::ANY)
    {
        if (!$this->getUser($userId)->likes($subjectId)) {
            throw new \RuntimeException(sprintf('User "%s" can\'t revoke non-existing like for subject "%s"!', $userId, $subjectId), 1464082632);
        }
        $this->eventr->getAggregate('User', $userId)->emitEvent('likeRevoked', ['subjectId' => $subjectId], $expectVersion);
    }

    /**
     * @param string $userId
     * @return User
     */
    public function getUser($userId)
    {
        $user = $this->userRepository->findByIdentifier($userId);
        if ($user !== null) {
            return $user;
        }
        return new User($userId);
    }

    /**
     * @param string $subjectId
     * @return Subject
     */
    public function getSubject($subjectId)
    {
        $subject = $this->subjectRepository->findByIdentifier($subjectId);
        if ($subject !== null) {
            return $subject;
        }
        return new Subject($subjectId);
    }
}