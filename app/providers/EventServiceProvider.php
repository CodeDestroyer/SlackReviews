<?php namespace CodeDad\Events;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * Register
     */
    public function register()
    {
        $this->app->events->subscribe(new SlackEventHandler());
    }

}