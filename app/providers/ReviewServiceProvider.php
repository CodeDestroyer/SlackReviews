<?php namespace CodeDad\Reviews;

use Illuminate\Support\ServiceProvider;

class ReviewServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind(
            'CodeDad\Contracts\Review\IReviewRepository',
            'CodeDad\Repositories\Review\ReviewRepository'
        );
    }

}