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
		$this->package('dakshhmehta/helpers');

		// Register form macros
		Form::macro('bool', function($name, $value){
			$html = '<input type="radio" name="'.$name.'" value="1"'.(($value === 1 || $value == 'Yes') ? ' checked="checked"' : '').'> Yes&nbsp;&nbsp;</input>';
			$html .= '<input type="radio" name="'.$name.'" value="0"'.(($value === 0 || $value == 'No') ? ' checked="checked"' : '').'> No</input>';

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
			
			if($multiSelect == true)
			{
				Template::addRawJS('
					$(document).ready(function(){
						$("#'.$name.'").select2();
					});
				');
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

		Form::macro('tags', function($name, $value = null, $attributes = array()){
			Template::addJS(asset('assets/js/libs/jquery.tagsinput.js'));
			Template::addRawJS('
				$("#'.$name.'").tagsInput({
				height: 40
				});
			');
			if(! isset($attributes['id']))
			{
				$attributes['id'] = $name;
			}
			return Form::text($name, $value, $attributes);
		});

		Form::macro('uploader', function($name, $action){
			Template::addJS(asset('assets/plugins/files/plupload/plupload.js'));
			Template::addJS(asset('assets/plugins/files/plupload/plupload.html5.js'));
			Template::addJS(asset('assets/plugins/files/plupload/jquery.plupload.queue/jquery.plupload.queue.js'));
			Template::addRawJS("
				// Setup html5 version
		    	$(\"#".$name."\").pluploadQueue({
			        // General settings
			        runtimes : 'html5,flash,silverlight,html4',
			        url : \"".$action."\",
			         
			        chunk_size : '1mb',
			        rename : true,
			        dragdrop: true,
			        unique_names: true,
			         
			        filters : {
			            // Maximum file size
			            max_file_size : '1mb',
			            // Specify what files to browse for
			            mime_types: [
			                {title : \"Image files\", extensions : \"jpg,gif,png\"}
			            ]
			        },
			 
			        // Resize images on clientside if we can
			        resize: {
			            quality : 70
			        },
			 
			 
			        // Flash settings
			        flash_swf_url : 'http://rawgithub.com/moxiecode/moxie/master/bin/flash/Moxie.cdn.swf',
			 
			        // Silverlight settings
			        silverlight_xap_url : 'http://rawgithub.com/moxiecode/moxie/master/bin/silverlight/Moxie.cdn.xap',

			        init: {
			        	FileUploaded: function(up, file, info) {
			        		info = JSON.parse(info.response);
			        		console.log(info);
				        	$('#filesList').val($('#filesList').val() + '<input type=\"hidden\" name=\"".$name."[]\" value=\"'+ info.result +'\" />');
				        }
			        }
			    });
			");
			Template::addCSS(asset('assets/plugins/files/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css'));

			$html = '<div id="'.$name.'" style="width: 100%; height: 100%; margin-bottom: 20px;">File uploading is not supported in this browser.</div>
			<div id="filesList"></div>';

			return $html;
		});

		Form::macro('datetime', function($name, $value, $dataTemplate, $viewTemplate = null){
			// Prepare the JS
			Template::addJS(asset('assets/js/libs/moment.min.js'));
			Template::addJS(asset('assets/js/libs/combodate.js'));

			$html = Form::text($name, $value, array(
				'id' => $name,
				'data-format' => $dataTemplate,
				'data-template' => (($viewTemplate == null) ? $dataTemplate : $viewTemplate),
				//'data-value' => $value,
			));

			Template::addRawJS('
				$(function(){
				    $("#'.$name.'").combodate();    
				    //$("#'.$name.'").setValue("'.$value.'");
				});
			');

			return $html;
		});

		Form::macro('uidatetime', function($name, $value = null, $includeTime = false, $attributes = array()){
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
