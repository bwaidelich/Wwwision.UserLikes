<?php
namespace Wwwision\UserLikes\ViewHelpers;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use Wwwision\UserLikes\LikeService;

/**
 * A ViewHelper that checks if a given subject has likes/recommendations
 *
 * = Examples =
 *
 * <code>
 * <x:ifHasLikes subject="{someEntity}">
 *   has {someEntity -> x:numberOfLikes()} likes
 * </x:ifHasLikes>
 * </code>
 */
class IfHasLikesViewHelper extends AbstractConditionViewHelper {

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
	 * Renders <f:then> child if $subject has at least 1 like/recommendation
	 *
	 * @param object|string $subject
	 * @return string the rendered string
	 */
	public function render($subject) {
		if (is_object($subject)) {
			$subject = $this->persistenceManager->getIdentifierByObject($subject);
		}
		if ($this->likeService->getNumberOfLikesBySubject($subject)) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
