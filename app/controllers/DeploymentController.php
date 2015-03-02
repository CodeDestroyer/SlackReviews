<?php
use CodeDad\Services\DeploymentService;
//TODO refactor out the input from the methods
class DeploymentController extends BaseController
{

    protected $_deploymentService;

    public function __construct(DeploymentService $deploymentService)
    {
        $this->_deploymentService = $deploymentService;
    }

    public function addDeployment(){
        $request = Input::all();
        $return = $this->_deploymentService->addDeployment($request);
        return Response::json($return);
    }

    public function toggleStepOfDeployment(){
        $request = Input::all();
        $return = $this->_deploymentService->toggleStep($request);
        return Response::json($return);
    }

    public function listDeployments()
    {
        $deployments = $this->_deploymentService->listAllSorted();
        $viewData = View::make('listDeployments')->with('deployments', $deployments)->render();
        return Response::json($viewData);
    }

    public function blockDeployment()
    {
        $request = Input::all();
        $return = $this->_deploymentService->blockDeployment($request);
        return Response::json($return);
    }

    public function unblockDeployment()
    {
        $request = Input::all();
        $return = $this->_deploymentService->unBlockDeployment($request);
        return Response::json($return);
    }

    public function removeDeployment()
    {
        $request = Input::all();
        $return = $this->_deploymentService->deleteDeployment($request);
        return Response::json($return);
    }
}
