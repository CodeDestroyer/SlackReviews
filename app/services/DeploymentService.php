<?php namespace CodeDad\Services;

use Carbon;
use CodeDad\Contracts\Deployments\IDeploymentService;
use CodeDad\Repositories\DeploymentRepository;
use Exception;
use Illuminate\Events\Dispatcher;

//Todo Refactor a base service with things like fire_event and any other methods that come up
class DeploymentService implements IDeploymentService
{
    private $_deploymentRepo;
    private $_events;

    function __construct(DeploymentRepository $deploymentRepo, Dispatcher $events)
    {
        $this->_deploymentRepo = $deploymentRepo;
        $this->_events = $events;
    }

    public function addDeployment($review)
    {
        try {
            $this->_reviewRepo->addReview($review);
            $this->_event->fire('review.submitted', array($review));
            return "Code Review Request Successful";

        } catch (Exception $e) {
            $this->_event->fire('review.exists', array($review));
            return $e->getMessage();
        }
    }

    private function _fireEvent($name, $data)
    {
        $this->_event->fire($name, array($data));
    }

}