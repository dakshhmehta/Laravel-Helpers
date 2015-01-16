<?php namespace Dakshhmehta\Helpers\Handlers;

use Dakshhmehta\Helpers\FormHandlerInterface;

class BSTabbedHandler implements FormHandlerInterface {
	public function beforeForm($form){
		$content = null;

		if(isset($form['tabs'])){
			$content .= '<ul class="nav nav-tabs">';
			$is_first = true;
		
			foreach($form['tabs'] as $tab)
			{
				$content .= '<li class="'.(($is_first == true) ? 'active': '').'"><a href="#'.str_replace(' ', '-', strtolower($tab)).'" data-toggle="tab">'.$tab.'</a></li>';
				$is_first = false;
			}
		
			$content .= '</ul>';
			$content .= '<div class="tab-content">';
		}

		return $content;

	}

	public function beforeField($field, $errors){

	}

	public function renderField($field, $errors){
		$content = null;
		if(! isset($field['html']))
		{
			$content .= '<div class="form-group'.(($errors->has($field['name'])) ? ' has-error' : '').'">';
			$content .= '<label class="col-lg-3 control-label" for="'.$field['name'].'">'.$field['label'].' '.((isset($field['hint'])) ? '<span class="icon tip" title="'.$field['hint'].'"><i class="glyphicon glyphicon-question-sign"></i></span>' : '').'</label>';
			$content .= '<div class="col-lg-9">';
			if(isset($field['field']))
			{
				$content .= $field['field'];
			}
			else
			{

				$content .= '<input '.((isset($field['placeholder'])) ? 'placeholder="'.$field['placeholder'].'"' : '').' type="text" class="form-control" name="'.$field['name'].'" id="'.str_replace('[]', '', $field['name']).'" value="'.
				(($value == null)
				? Input::old(str_replace('[]', '', $field['name']), '')
				: 
				 	((! isset($field['value']))
						? $value->{str_replace('[]', '', $field['name'])}
						: $field['value']
					)
				).'"'.$attributes.' />';
				$content .= $errors->first($field['name'], '<span class="help-block">:message</span>');
			}
			$content .= '</div>';
			$content .= '</div>';
		}
		else
		{
			$content .= $field['html'];
		}

		return $content;
	}

	public function afterField($field, $errors){

	}

	public function afterForm($form){

	}
}