<?php namespace CodeDad\Reviews;

use Illuminate\Support\ServiceProvider;

class ReviewServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'CodeDad\Contracts\Reviews\IReviewService',
            'CodeDad\Services\ReviewService'
        );
    }

}