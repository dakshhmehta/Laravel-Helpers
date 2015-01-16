<?php namespace Dakshhmehta\Helpers;

use Input;
use Form;

class Template {

	protected static $rawData = array();
	protected static $files = array();
	protected $handler = null;

	public function __construct()
	{
		self::$rawData['js'] = array();
		self::$rawData['css'] = array();
		self::$files['js'] = array();
		self::$files['css'] = array();
	}

	/**
	 * Add raw javascript to template before <body>
	 * @param string $data Raw javascript to be added
	 */
	public static function addRawJS($data)
	{
		if(count(self::$rawData) > 0)
		{
			if(in_array($data, self::$rawData['js']) == true)
			{
				return false; // We already have it
			}
		}

		self::$rawData['js'][] = $data;
	}

	/**
	 * Allow you to inject/add javascript file in template
	 * @param string $file Full path to the file
	 */
	public static function addJS($file)
	{
		if(count(self::$files) > 0)
		{
			if(in_array($file, self::$files['js']) == true)
			{
				return false; // We already have it
			}
		}

		self::$files['js'][] = $file;
	}

	/**
	 * Allow you to add/call external stylesheet
	 * @param string $file Full path to stylesheet, including .css
	 */
	public static function addCSS($file)
	{
		if(count(self::$files) > 0)
		{
			if(in_array($file, self::$files['js']) == true)
			{
				return false; // We already have it
			}
		}

		self::$files['css'][] = $file;
	}

	/**
	 * Print all the external CSS files
	 * @return string
	 */
	public static function renderCSS()
	{
		$html = '';
		if(isset(self::$files['css']))
		{
			foreach(self::$files['css'] as $css)
			{
				$html .= '<link rel="stylesheet" type="text/css" href="'.$css.'" />';
			}
		}

		return $html;
	}

	/**
	 * Print the external javascripts
	 * @return string
	 */
	public static function renderJS()
	{
		$html = '';
		if(isset(self::$files['js']))
		{
			foreach(self::$files['js'] as $js)
			{
				$html .= '<script language="javascript" type="text/javascript" src="'.$js.'"></script>';
			}
		}

		return $html;
	}

	/**
	 * Print the raw javascripts within script tag
	 * @return string
	 */
	public static function renderRawJS()
	{
		$html = '<script language="javascript" type="text/javascript">';

		if(isset(self::$rawData['js']))
		{
			foreach(self::$rawData['js'] as $js)
			{
				$html .= $js;
			}
		}

		$html .= '</script>';

		return $html;
	}

	public function setFormHandler(FormHandlerInterface $handler){
		$this->handler = $handler;
	}

	/**
	 * Generate the bootstrap3 form with tabs
	 *
	 * <code>
	 * <?php 
	 *     Template::form('myform', 'index.php', array(
	 *         'tabs' => array('General'),
	 *         'tabsContent' => array(
	 *             'General' => array(
	 *                 array(
	 *                     'name' => 'email',
	 *                     'label' => 'Email Address',
	 *                     // If not specified, it will output text field, with default value from current request
	 *                     // matching key as of name.
	 *                     'field' => Form::text('email', Input::old('value')),
	 *                     // Will be overrided if specified, else 'field' will be used
	 *                     'html' => '<input type="email" value="" />'
	 *                 )
	 *             )
	 *         )
	 *     ), $errors, $myData);
	 * ?>
	 * </coode>
	 * 
	 * @param  string $name Name of the form
	 * @param  string $action URL to submit the form
	 * @param  array  $data Containing tabs and it's contents to populate the markups
	 * @param  MessageBag $errors containing validation errors messages bag
	 * @param  [type] $value To fill the default value(s) of form field matching with it's name
	 * @return mixed Complete bootstrap 3 form markup
	 */
	public static function form($name, $action, $data = array(), $errors, $value = null)
	{
		$form = '<form class="form-horizontal" id="'.$name.'" enctype="multipart/form-data" action="'.$action.'" method="post">'.Form::token();

		$form .= $this->handler->beforeForm($data);

		$is_first = true;
		foreach($data['tabsContent'] as $tab => $fields)
		{
			$form .= '<div class="tab-pane fade'.(($is_first == true) ? ' active in': '').'" id="'.str_replace(' ', '-', strtolower($tab)).'">';
			foreach($fields as $field)
			{
				// Prepare input attributes
				$attributes = '';
				if(isset($field['attributes'])){
					foreach($field['attributes'] as $attribute => $v){
						$attributes .= ' '.$attribute.'="'.$v.'"';
					}
				}

				$form .= $this->handler->renderField($field, $errors);

			}
			$form .= '</div>';
			$is_first = false;
		}
		$form .= '</div>';

		$form .= '<div class="form-group"><div class="col-lg-offset-2"><button type="submit" class="btn btn-success">Submit</button></div></div>';

		$form .= '</form>';

		return $form;
	}
}