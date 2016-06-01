<?php
namespace Wwwision\UserLikes\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use Wwwision\UserLikes\LikeService;

/**
 * A ViewHelper that renders the number of likes for a given subject
 *
 * = Examples =
 *
 * <code>
 * {someEntity -> x:numberOfLikes()} likes
 * </code>
 */
class NumberOfLikesViewHelper extends AbstractViewHelper {

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var LikeService
	 */
	protected $likeService;

	/**
	 * @var bool
	 */
	protected $escapeChildren = false;

	/**
	 * Renders the number of likes/recommmendations of the given $subject
	 *
	 * @return integer the number of likes for the given $subject
	 */
	public function render() {
		$subject = $this->renderChildren();
		if (is_object($subject)) {
			$subject = $this->persistenceManager->getIdentifierByObject($subject);
		}
		return $this->likeService->getNumberOfLikesBySubject($subject);
	}
}
