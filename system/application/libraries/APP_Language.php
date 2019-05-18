<?php
class APP_Language extends CI_Language {

	function APP_Language () {
		parent :: CI_Language();
	}

	function translate ($language_id, $module, $tag ) {
		
		$tag = Doctrine_Core::getTable('LanguageTag')->retrieveByTag($language_id, $module, $tag);
		
		if ($tag) {
			return $tag->language_tag_value;
		} else {
			return 'Tag does not exist!';
		}
		
	}

}