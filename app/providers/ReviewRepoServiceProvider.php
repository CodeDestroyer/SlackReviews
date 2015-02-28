<?php namespace CodeDad\Reviews;

use Illuminate\Support\ServiceProvider;

class ReviewRepoServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'CodeDad\Contracts\Review\IReviewRepository',
            'CodeDad\Repositories\ReviewRepository'
        );
    }

}