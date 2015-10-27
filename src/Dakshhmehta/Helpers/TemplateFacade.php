<?php namespace Dakshhmehta\Helpers;

use Illuminate\Support\Facades\Facade;

class TemplateFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'dax-template';
    }
}
