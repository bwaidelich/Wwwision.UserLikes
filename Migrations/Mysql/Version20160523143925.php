<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use TYPO3\Eel\Helper\ArrayHelper;
use Wwwision\Eventr\Adapters\Doctrine\ProjectionHandler;
use Wwwision\Eventr\Domain\Dto\ProjectionConfiguration;
use Wwwision\Eventr\Migrations\AbstractEventrMigration;
use Wwwision\UserLikes\Domain\Model\Subject;
use Wwwision\UserLikes\Domain\Model\User;

/**
 * Register Wwwision.Eventr AggregateTypes, EventTypes and projections
 */
class Version20160523143925 extends AbstractEventrMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->registerTypes();
		$this->registerProjections();
	}

	/**
	 * @return void
	 */
	private function registerTypes() {
		$this->output->outputLine('<b>Registering AggregateTypes & Events</b>');
		$userType = $this->registerOrGetAggregateType('User');

		$this->registerOrUpdateEventType($userType, 'likeAdded', [
			'subjectId' => ['type' => 'string', 'required' => TRUE],
		]);
		$this->registerOrUpdateEventType($userType, 'likeRevoked', [
			'subjectId' => ['type' => 'string', 'required' => TRUE],
		]);
		$this->output->outputLine('Done.');
	}

	/**
	 * @return void
	 */
	private function registerProjections() {
		$this->output->outputLine('<b>Registering Projections</b>');
		$this->registerUsersProjection();
		$this->registerSubjectsProjection();
		$this->output->outputLine('Done.');
	}

	/**
	 * @return void
	 */
	private function registerUsersProjection() {
		$projectionName = 'Users';
		$this->output->outputLine(' Registering Projection "%s"', [$projectionName]);

		$aggregateType = $this->eventr->getAggregateType('User');

		$projectionConfiguration = new ProjectionConfiguration($aggregateType, ProjectionHandler::class);
		$projectionConfiguration->handlerOptions = [
			'readModelClassName' => User::class,
			'eelHelper' => [
				'Array' => ArrayHelper::class
			],
			'mapping' => [
				'likeAdded' => [
					'likes' => 'state.likes ? Array.push(state.likes, event.data.subjectId) : [event.data.subjectId]',
				],
				'likeRevoked' => [
					'likes' => 'state.likes ? Array.splice(state.likes, Array.indexOf(state.likes, event.data.subjectId)) : []',
				],
			],
		];
		$projectionConfiguration->synchronous = TRUE;
		$this->registerOrUpdateProjection($projectionName, $projectionConfiguration);
	}

	/**
	 * @return void
	 */
	private function registerSubjectsProjection() {
		$projectionName = 'Subjects';
		$this->output->outputLine(' Registering Projection "%s"', [$projectionName]);

		$aggregateType = $this->eventr->getAggregateType('User');

		$projectionConfiguration = new ProjectionConfiguration($aggregateType, ProjectionHandler::class);
		$projectionConfiguration->handlerOptions = [
			'readModelClassName' => Subject::class,
			'mapping' => [
				'likeAdded' => [
					'id' => 'event.data.subjectId',
					'numberOfLikes' => 'state.numberOfLikes ? state.numberOfLikes + 1 : 1'
				],
				'likeRevoked' => [
					'id' => 'event.data.subjectId',
					'numberOfLikes' => 'state.numberOfLikes > 0 ? state.numberOfLikes - 1 : 0'
				],
			],
		];
		$projectionConfiguration->synchronous = TRUE;
		$this->registerOrUpdateProjection($projectionName, $projectionConfiguration);
	}
}