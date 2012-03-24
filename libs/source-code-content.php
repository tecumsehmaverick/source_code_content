<?php

	/**
	 * @package content_field
	 */
	class SourceCodeContent implements ContentType {
		public function appendSettingsHeaders(HTMLPage $page) {

		}

		public function appendSettingsInterface(XMLElement $wrapper, $field_name, StdClass $settings = null, MessageStack $errors) {
			$legend = new XMLElement('legend');
			$legend->setValue(__('Source Code'));
			$wrapper->appendChild($legend);

			// Default textarea size:
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');

			$values = array(
				array('auto', false, __('Automatic')),
				array('small', false, __('Small Box')),
				array('medium', false, __('Medium Box')),
				array('large', false, __('Large Box')),
				array('huge', false, __('Huge Box'))
			);

			foreach ($values as &$value) {
				$value[1] = $value[0] == $settings->{'text-size'};
			}

			$label = Widget::Label('Default Size');
			$label->appendChild(Widget::Select(
				"{$field_name}[text-size]", $values
			));

			$group->appendChild($label);

			// Default tab size:
			$values = array(
				array(2, false, __('2 Spaces')),
				array(3, false, __('3 Spaces')),
				array(4, false, __('4 Spaces')),
				array(8, false, __('8 Spaces'))
			);

			foreach ($values as &$value) {
				$value[1] = $value[0] == $settings->{'tab-size'};
			}

			$label = Widget::Label('Default Tab Size');
			$label->appendChild(Widget::Select(
				"{$field_name}[tab-size]", $values
			));

			$group->appendChild($label);
			$wrapper->appendChild($group);

			// Enable this content type:
			$input = Widget::Input("{$field_name}[enabled]", 'yes', 'checkbox');

			if ($settings->{'enabled'} == 'yes') {
				$input->setAttribute('checked', 'checked');
			}

			$wrapper->appendChild(Widget::Label(
				__('%s Enable the Source Code content type', array(
					$input->generate()
				))
			));
		}

		public function sanitizeSettings($settings) {
			if (is_array($settings)) {
				$settings = (object)$settings;
			}

			else if (is_object($settings) === false) {
				$settings = new StdClass();
			}

			if (isset($settings->{'enabled'}) === false) {
				$settings->{'enabled'} = 'no';
			}

			if (isset($settings->{'text-size'}) === false) {
				$settings->{'text-size'} = 'auto';
			}

			if (isset($settings->{'text-formatter'}) === false) {
				$settings->{'tab-size'} = 4;
			}

			return $settings;
		}

		public function validateSettings(StdClass $data, MessageStack $errors) {
			return true;
		}

		public function appendPublishHeaders(HTMLPage $page) {
			$url = URL . '/extensions/content_field/assets';
		}

		public function appendPublishInterface(XMLElement $wrapper, $field_name, StdClass $settings, StdClass $data, MessageStack $errors, $entry_id = null) {
			$header = new XMLElement('header');
			$header->addClass('main');
			$header->appendChild(
				new XMLElement('strong', __('Source Code'))
			);
			$wrapper->appendChild($header);

			$content = new XMLElement('div');
			$wrapper->appendChild($content);

			// Syntax highlighting:
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');

			$values = array(
				array('none', false, __('None')),
				array('css', false, __('CSS')),
				array('html', false, __('HTML')),
				array('js', false, __('JavaScript')),
				array('php', false, __('PHP'))
			);

			foreach ($values as &$value) {
				$value[1] = $value[0] == $data->{'syntax'};
			}

			$label = Widget::Label('Syntax Highligter');
			$label->appendChild(Widget::Select(
				"{$field_name}[data][syntax]", $values
			));

			$group->appendChild($label);

			// Default tab size:
			$values = array(
				array(2, false, __('2 Spaces')),
				array(3, false, __('3 Spaces')),
				array(4, false, __('4 Spaces')),
				array(8, false, __('8 Spaces'))
			);

			foreach ($values as &$value) {
				$value[1] = $value[0] == $data->{'tab-size'};
			}

			$label = Widget::Label('Tab Size');
			$label->appendChild(Widget::Select(
				"{$field_name}[data][tab-size]", $values
			));

			$group->appendChild($label);
			$content->appendChild($group);

			// Source code:
			$text = Widget::Textarea(
				"{$field_name}[data][value]", 1, 50, (
					isset($data->value)
						? $data->value
						: null
				)
			);
			$text->addClass('size-' . $settings->{'text-size'});
			$content->appendChild($text);
		}

		public function processData(StdClass $settings, StdClass $data, $entry_id = null) {
			$formatted = $data->value;
			$replace_tabs = function($matches) use ($data) {
				return $matches[1] . str_repeat(
					' ', strlen($matches[2]) * $data->{'tab-size'} - (strlen($matches[1]) % $data->{'tab-size'})
				);
			};

			// Find and replace all tabs:
			while (strstr($formatted, "\t")) {
				$formatted = preg_replace_callback(
					'%^([^\t]*)(\t+)%', $replace_tabs, $formatted
				);
			}

			// Sanitize output:
			$formatted = General::sanitize($formatted);

			return (object)array(
				'handle'			=> null,
				'value'				=> $data->{'value'},
				'value_formatted'	=> $formatted,
				'tab-size'			=> $data->{'tab-size'},
				'syntax'			=> $data->{'syntax'}
			);
		}

		public function sanitizeData(StdClass $settings, $data) {
			$default = array(
				'value'				=> null,
				'value_formatted'	=> null,
				'tab-size'			=> null,
				'syntax'			=> null
			);

			if (is_object($data) && isset($data->value)) {
				$result = (array)$data;
			}

			else if (is_array($data) && isset($data['value'])) {
				$result = $data;
			}

			else {
				$result = array(
					'value'	=> $data
				);
			}

			return (object)array_merge($default, $result);
		}

		public function validateData(StdClass $settings, StdClass $data, MessageStack $errors, $entry_id = null) {
			return is_string($data->value);
		}

		public function appendFormattedElement(XMLElement $wrapper, StdClass $settings, StdClass $data, $entry_id = null) {
			$wrapper->setValue($data->value_formatted);
		}
	}