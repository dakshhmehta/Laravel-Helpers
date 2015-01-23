<?php namespace Dakshhmehta\Helpers;

interface FormHandlerInterface {
	public function beforeForm($form);
	public function beforeGroup($group);
	public function beforeField($field);
	public function renderField($field, $value, $errors);
	public function afterField($field);
	public function afterGroup($group);
	public function afterForm($form);
}