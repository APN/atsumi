<?php

class session_DatabaseStorage extends session_AbstractStorage {

	protected $database = null;

	public function __construct($options) {
		if(!isset($options['database']))
			throw new Exception('A database is needed for this storage method');

		$this->database = $options['database'];

		ini_set('session.gc_divisor', 100);
		ini_set('session.gc_maxlifetime', (isset($options['life']) ? $options['life'] : 7200));
		ini_set('session.gc_probability', 1);

		parent::__construct($options);
	}

	public function read($id) {
		try {
			$result = $this->database->select_1('SELECT * FROM session WHERE checksum = %l::bigint AND session_id = %s', crc32($id), $id);

		}
		catch(Exception $e) {

			atsumi_Debug::record(
				'Session Failed to load from DB',
				sf('The session(%s) session could not load', $id),
				$e,
				false,
				atsumi_Debug::AREA_SESSION
			);
			return serialize(array($e->getMessage()));
		}

		if(is_null($result))
			return '';

		atsumi_Debug::record(
			'Session loaded from DB',
			sf('The session loaded from the db.' ),
			$result,
			false,
			atsumi_Debug::AREA_SESSION
		);
		return base64_decode($result->s_data);
	}

	public function write($id, $sessionData) {
		try {
			if($this->database->exists('session', 'checksum = %l::bigint AND session_id = %s', crc32($id), $id)) {
				atsumi_Debug::record(
					'Updating Session',
					sf('The session(%s) is being updated to the DB', $id ),
					$sessionData,
					false,
					atsumi_Debug::AREA_SESSION
				);

				$this->database->update(
					'session',
					'checksum = %l::bigint AND session_id = %s', crc32($id), $id,
					'data = %s', base64_encode($sessionData),
					'last_active	= NOW()'
				);
			}
			else {
				atsumi_Debug::record(
					'Inserting Session',
					sf('The atsumi(%s) is being inserted to the DB: ', $id ),
					$sessionData,
					false,
					atsumi_Debug::AREA_SESSION
				);
				$this->database->insert(
					'session',
					'checksum		= %l::bigint', crc32($id),
					'session_id		= %s', $id,
					'data			= %s', base64_encode($sessionData),
					'last_active	= NOW()'
				);
			}
			return true;

		/* this is a little drastic but will most likley segfault if Exception bubbles up */

		} catch (Exception $e) { Atsumi::error__listen($e); die('Could not write session to database - '.$e->getMessage()); }

	}

	public function destroy($id) {
		$this->database->delete(
			'session',
			'checksum = %l::bigint AND session_id = %s', crc32($id), $id
		);

		return true;
	}

	public function gc($maxlifetime) {
		$this->database->delete(
			'session',
			'last_active < %t',(time() - $maxlifetime)
		);

		return true;
	}
}
