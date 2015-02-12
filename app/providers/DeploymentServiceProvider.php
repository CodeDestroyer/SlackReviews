<?php namespace CodeDad\Deployments;

use Illuminate\Support\ServiceProvider;

class DeploymentServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'CodeDad\Contracts\Deployments\IDeploymentRepository',
            'CodeDad\Repositories\Deployments\DeploymentRepository'
        );
    }

}