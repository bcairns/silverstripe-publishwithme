<?php

/**
 * Class PublishWithMe_GridField
 */
class PublishWithMe_GridField extends Extension
{

	/**
	 * Add "publish-with-me" class to GridField, if its ModelClass is being managed by PublishWithMe
	 * This is used in PublishWithMe.js to determine if deleting items should activate "Save & Publish" button
	 *
	 * @param $attributes
	 */
	public function updateAttributes(&$attributes){
		if (singleton($this->owner->getModelClass())->has_extension('PublishWithMe_Child')) {
			$attributes['class'] .= ' publish-with-me';
		}
	}

}
