<?php namespace CodeDad\Repositories\Deployments;

use Carbon;
use CodeDad\Contracts\Deployments\IDeploymentRepository;
use CodeDad\Models\Deployment;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class DeploymentRepository implements IDeploymentRepository
{
    protected $_deployment;
    /**
     * @var Dispatcher
     */
    protected $_events;

    public function __construct(Deployment $deployment, Dispatcher $events)
    {
        $this->_deployment = $deployment;
        $this->_events = $events;
    }
 public function addDeployment($request)
    {

}
}