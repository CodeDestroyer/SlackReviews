<?php
use CodeDad\Repositories\Deployments\DeploymentRepository;

class DeploymentController extends BaseController
{

    protected $_deployment;

    public function __construct(DeploymentRepository $deployment)
    {
        $this->_deployment = $deployment;
    }

    public function addDeployment(){
        echo "test";
    }
}
