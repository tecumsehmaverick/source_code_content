<?php

	/**
	 * @package source_code_content
	 */

	class Extension_Source_Code_Content extends Extension {
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '*',
					'delegate'	=> 'AppendContentType',
					'callback'	=> 'appendContentType'
				)
			);
		}

		public function appendContentType(&$context) {
			require_once __DIR__ . '/libs/source-code-content.php';

			$context['items']->{'source-code'} = new SourceCodeContent();
		}
	}