<?php namespace CodeDad\Reviews;

use Illuminate\Support\ServiceProvider;

class DeploymentServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'CodeDad\Contracts\Deployments\IDeploymentService',
            'CodeDad\Services\DeploymentService'
        );
    }

}