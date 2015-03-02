<?php namespace CodeDad\Facades;

use Illuminate\Support\Facades\Facade;

class FriendlyMessageFacade extends Facade{
    protected static function getFacadeAccessor() { return 'friendlyMessage'; }
}