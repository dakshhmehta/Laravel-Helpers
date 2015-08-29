<?php namespace Dakshhmehta\Helpers;

use Illuminate\Support\ServiceProvider;
use Config;
use Form;

class HelpersServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		Form::macro('bool', function($name, $value, $other = array(), $yesValue = 'Yes', $noValue = 'No'){
			$html = '<input type="radio" name="'.$name.'" value="1"'.(($value === 1 || $value == $yesValue) ? ' checked="checked"' : '').'> '.$yesValue.'&nbsp;&nbsp;</input>';
			$html .= '<input type="radio" name="'.$name.'" value="0"'.(($value === 0 || $value == $noValue) ? ' checked="checked"' : '').'> '.$noValue.'</input>';

			if(count($other) > 0){
				foreach($other as $key => $label){
					$html .= '<input type="radio" name="'.$name.'" value="'.$key.'"'.(($value == $key || $value == $label) ? ' checked="checked"' : '').'> '.$label.'</input>';
				}
			}

			return $html;
		});

		Form::macro('dropdown', function($name, $options = array(), $value = null, $multiSelect = false, $attributes = array()){
			$values = array();
			if(is_array($value))
			{
				foreach ($value as $val) {
					$values[] = $val;
				}
			}

			$attributesStr = null;
			if(is_array($attributes) && count($attributes) > 0){
				foreach($attributes as $key => $value){
					$attributesStr .= $key.'="'.$value.'" ';
				}
			}
			
			$html = '<select'.(($multiSelect == true) ? ' multiple' : '').' class="form-control" name="'.$name.(($multiSelect == true) ? '[]' : '').'" id="'.$name.'" '.$attributesStr.'>';
			
			if($multiSelect === false)
			{
				$html .= '<option value="">-- Select --</option>';
			}

			foreach($options as $key => $label)
			{
				$html .= '<option '.(
					(
						($multiSelect == false) 
						? ($key === $value || $label == $value) 
						: (in_array($key, $values) || in_array($label, $values))
					) 
					? 'selected ' 
					: '').'value="'.$key.'">'.$label.'</option>';
			}

			$html .= '</select>';

			return $html;
		});

		Form::macro('datetime', function($name, $value = null, $includeTime = false, $attributes = array()){
			$attributesStr = null;
			if(is_array($attributes) && count($attributes) > 0){
				foreach($attributes as $key => $value){
					$attributesStr .= $key.'="'.$value.'" ';
				}
			}

			$html = '<input type="text" name="'.$name.'" value="'.$value.'" id="'.$name.'" class="form-control" '.$attributesStr.'/>';

			Template::addCSS(Config::get('helpers::plugins_path').'/jquery-ui/jquery-ui.min.css');
			Template::addJS(Config::get('helpers::plugins_path').'/jquery-ui/jquery-ui.min.js');

			Template::addRawJS('
				$("#'.$name.'").date'.(($includeTime == true) ? 'time' : '').'picker({
					dateFormat: "yy-mm-dd",
					timeFormat: "hh:mm:ss",
					changeMonth: true,
				    changeYear: true,
				    showWeek: true,
		      		firstDay: 1,
		      		defaultDate: "'.$value.'"
				});
			');

			if($includeTime == true)
			{
				Template::addCSS(Config::get('helpers::plugins_path').'/jquery-timepicker/src/jquery-ui-timepicker-addon.css');
				Template::addJS(Config::get('helpers::plugins_path').'/jquery-timepicker/src/jquery-ui-timepicker-addon.js');
			}

			return $html;
		});

		#Configs
		$this->publishes([
	    	__DIR__.'/../../../config/' => config_path()
		], 'config');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app['dax-template'] = $this->app->share(function($app){
			return new Template(Config::get('helpers::handler'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('dax-template');
	}

}
