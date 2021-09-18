<?php
declare(strict_types=1);


/**
 * Files_FullTextSearch - Index the content of your files
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OCA\Files_FullTextSearch\Events;


use ArtificialOwl\MySmallPhpTools\Traits\TArrayTools;
use OC\AppFramework\Bootstrap\Coordinator;
use OCA\Files_FullTextSearch\Service\ConfigService;
use OCA\Files_FullTextSearch\Service\FilesService;
use OCA\Files_FullTextSearch\Service\MiscService;
use OCP\App\IAppManager;
use OCP\FullTextSearch\IFullTextSearchManager;
use OCP\FullTextSearch\Model\IIndex;


/**
 * Class FilesEvents
 *
 * @package OCA\Files_FullTextSearch\Events
 */
class FilesEvents {


	use TArrayTools;


	/** @var string */
	private $userId;

	/** @var IAppManager */
	private $appManager;

	/** @var Coordinator */
	private $coordinator;

	/** @var IFullTextSearchManager */
	private $fullTextSearchManager;

	/** @var FilesService */
	private $filesService;

	/** @var ConfigService */
	private $configService;

	/** @var MiscService */
	private $miscService;


	/**
	 * FilesEvents constructor.
	 *
	 * @param string $userId
	 * @param IAppManager $appManager
	 * @param Coordinator $coordinator
	 * @param IFullTextSearchManager $fullTextSearchManager
	 * @param FilesService $filesService
	 * @param ConfigService $configService
	 * @param MiscService $miscService
	 */
	public function __construct(
		$userId, IAppManager $appManager, Coordinator $coordinator,
		IFullTextSearchManager $fullTextSearchManager,
		FilesService $filesService, ConfigService $configService, MiscService $miscService
	) {
		$this->userId = $userId;
		$this->appManager = $appManager;
		$this->coordinator = $coordinator;
		$this->fullTextSearchManager = $fullTextSearchManager;
		$this->filesService = $filesService;
		$this->configService = $configService;
		$this->miscService = $miscService;
	}


	/**
	 * @return bool
	 */
	private function registerFullTextSearchServices() {
		$this->coordinator->bootApp('fulltextsearch');

<<<<<<< HEAD
		return true;
	}


	/**
	 * @param array $params
	 *
	 * @throws InvalidPathException
	 * @throws NotFoundException
	 */
	public function onNewFile(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$path = $this->get('path', $params, '');
		if ($path === '' || $this->userId === null) {
			return;
		}

		$file = $this->filesService->getFileFromPath($this->userId, $path);
		if ($this->configService->isCloudVersionAtLeast(15, 0, 1) && !$this->configService->isPathExcluded($path)) {
			$this->fullTextSearchManager->createIndex(
				'files', (string)$file->getId(), $this->userId, IIndex::INDEX_FULL
			);
		}
	}


	/**
	 * @param array $params
	 *
	 * @throws InvalidPathException
	 * @throws NotFoundException
	 */
	public function onFileUpdate(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$path = $this->get('path', $params, '');
		if ($path === '' || $this->userId === null) {
			return;
		}

		$file = $this->filesService->getFileFromPath($this->userId, $path);
		$this->fullTextSearchManager->updateIndexStatus(
			'files', (string)$file->getId(), IIndex::INDEX_CONTENT
		);
	}


	/**
	 * @param array $params
	 *
	 * @throws NotFoundException
	 * @throws InvalidPathException
	 */
	public function onFileRename(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$target = $this->get('newpath', $params, '');
		if ($target === '' || $this->userId === null) {
			return;
		}

		$file = $this->filesService->getFileFromPath($this->userId, $target);
		$this->fullTextSearchManager->updateIndexStatus(
			'files', (string)$file->getId(), IIndex::INDEX_META
		);
	}


	/**
	 * @param array $params
	 *
	 * @throws InvalidPathException
	 * @throws NotFoundException
	 */
	public function onFileTrash(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		// check if trashbin does not exist. -> onFileDelete
		// we do not index trashbin
		$path = $this->get('path', $params, '');
		if ($path === '' || $this->userId === null) {
			return;
		}

		$file = $this->filesService->getFileFromPath($this->userId, $path);
		$this->fullTextSearchManager->updateIndexStatus(
			'files', (string)$file->getId(), IIndex::INDEX_REMOVE, true
		);
	}


	/**
	 * @param array $params
	 *
	 * @throws InvalidPathException
	 * @throws NotFoundException
	 */
	public function onFileRestore(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$path = $this->get('filePath', $params, '');
		if ($path === '' || $this->userId === null) {
			return;
		}

		$file = $this->filesService->getFileFromPath($this->userId, $path);
		$this->fullTextSearchManager->updateIndexStatus(
			'files', (string)$file->getId(), IIndex::INDEX_FULL
		);
	}


	/**
	 * @param array $params
	 */
	public function onFileDelete(array $params) {
		//		if (!$this->registerFullTextSearchServices()) {
//			return;
//		}

//		$path = $this->get('path', $params, '');
//		if ($path === '') {
//			return;
//		}

//		$file = $this->filesService->getFileFromPath($this->userId, $path);
//		$this->fullTextSearchManager->updateIndexStatus('files', (string) $file->getId(), Index::INDEX_REMOVE);
	}


	/**
	 * @param array $params
	 */
	public function onFileShare(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$fileId = $this->get('itemSource', $params, '');
		if ($fileId === '' || $this->userId === null) {
			return;
		}

		$this->fullTextSearchManager->updateIndexStatus('files', $fileId, IIndex::INDEX_META);
=======
		return $this->fullTextSearchManager->isAvailable();
>>>>>>> v20.0.0
	}


	/**
	 * @param array $params
	 */
	public function onFileUnshare(array $params) {
		if (!$this->registerFullTextSearchServices()) {
			return;
		}

		$fileId = $this->get('itemSource', $params, '');
		if ($fileId === '' || $this->userId === null) {
			return;
		}

		$this->fullTextSearchManager->updateIndexStatus('files', $fileId, IIndex::INDEX_META);
	}


}

