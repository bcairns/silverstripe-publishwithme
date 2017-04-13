<?php

/**
 * Class PublishWithMe_Child
 *
 * When Versioned DataObjects are deleted (ie from a $has_many relation via a GridField), all that happens is that the
 * Live/Draft records are killed.  Nothing in _versions records this delete.  This makes it impossible to figure out
 * after-the-fact (ie when attempting rollback) when the DataObject was removed from the parent.
 *
 * This Extension addresses this issue by adding a new "Deleted" field, populating it during onBeforeDelete(), and then
 * using augmentSQL() on archive requests to insert a WHERE clause (which is exactly how Versioned works).
 * It also clears this value (if set) after rollback on the new version
 */
class PublishWithMe_Child extends DataExtension
{

    private static $db = array(
    	'Deleted' => 'SS_Datetime'
    );

	/**
	 * Records the deletion time to the _versions table
	 */
	public function onBeforeDelete() {

		$table = ClassInfo::table_for_object_field($this->owner->getClassName(),'Deleted');
		$now = SS_Datetime::now()->Rfc2822(); // from DataObject::write()

		DB::prepared_query("UPDATE \"{$table}_versions\"
				SET \"Deleted\" = ?
				WHERE \"RecordID\" = ? AND \"Version\" = ?",
			array($now, $this->owner->ID, $this->owner->Version)
		);

	}

	/**
	 * For archive requests, limit the query to versions that have not been deleted prior to the archive date
	 * @see Versioned::augmentSQL()
	 * @param SQLQuery $query
	 * @param DataQuery|null $dataQuery
	 */
	public function augmentSQL(SQLQuery &$query, DataQuery &$dataQuery = null) {

		if (!$dataQuery || !$dataQuery->getQueryParam('Versioned.mode')) {
			return;
		}

		if ($dataQuery->getQueryParam('Versioned.mode') == 'archive') {
			$table = ClassInfo::table_for_object_field($this->owner->getClassName(),'Deleted');
			$date = $dataQuery->getQueryParam('Versioned.date');
			$query->addWhere(array(
				"\"{$table}_versions\".\"Deleted\" IS NULL OR \"{$table}_versions\".\"Deleted\" > ?" => $date
			));
		}

	}

	/**
	 * After rollback we need to clear the Deleted column of the new version
	 * @param $version
	 */
	public function onAfterRollback($version) {

		// at this point, a record from _versions has just been copied to Draft and _versions
		// we need to clear the Deleted value (on both) if it is set
		if (!empty($this->owner->Deleted)) {

			$table = ClassInfo::table_for_object_field($this->owner->getClassName(),'Deleted');

			DB::prepared_query("UPDATE \"{$table}\"
				SET \"Deleted\" = NULL
				WHERE \"ID\" = ?",
				array($this->owner->ID)
			);

			DB::prepared_query("UPDATE \"{$table}_versions\"
				SET \"Deleted\" = NULL
				WHERE \"RecordID\" = ?
				ORDER BY \"Version\" DESC
				LIMIT 1",
				array($this->owner->ID)
			);

			$this->owner->Deleted = null;

		}

	}

}
