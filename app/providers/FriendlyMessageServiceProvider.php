<?php namespace CodeDad\Helpers;

use Illuminate\Support\ServiceProvider;

class FriendlyMessageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('friendlyMessage', function()
        {
            return new FriendlyMessage();
        });
    }

}