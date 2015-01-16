<?php namespace Dakshhmehta\Helpers;

interface FormHandlerInterface {
	public function beforeForm($form);
	public function beforeField($field, $errors);
	public function renderField($field, $errors);
	public function afterField($field, $errors);
	public function afterForm($form);
}