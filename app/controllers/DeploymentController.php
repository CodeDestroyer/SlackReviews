<?php
use CodeDad\Repositories\DeploymentRepository;
//TODO refactor out the input from the methods
class DeploymentController extends BaseController
{

    protected $_deployment;

    public function __construct(DeploymentRepository $deployment)
    {
        $this->_deployment = $deployment;
    }

    public function addDeployment(){
        $request = Input::all();
        try {
            $this->_deployment->addDeployment($request);
        } catch (Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("Your Ticket is now in the Staging Queue.");
    }

    public function stageDeployment(){
        $request = Input::all();
        try {
            $this->_deployment->stageDeployment($request);
        } catch (Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("The Ticket is now ready to be validated in staging");
    }

    public function deployDeployment(){
        $request = Input::all();
        try {
            $this->_deployment->deployDeployment($request);
        } catch (Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("The Ticket is ready for Production Validation");
    }

    public function validateStaging(){
        $request = Input::all();
        try {
            $this->_deployment->validateStaging($request);
        } catch (Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("Your Ticket is ready for Production");
    }

    public function validateDeployment(){
        $request = Input::all();
        try {
            $this->_deployment->validateDeployment($request);
        } catch (Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("The ticket can now be moved to production in Jira");
    }

    public function listDeployments()
    {
        $deployments = $this->_deployment->listAllSorted();
        $viewData = View::make('listDeployments')->with('deployments', $deployments)->render();
        return Response::json($viewData);
    }

    public function blockDeployment()
    {
        $request = Input::all();
        try
        {
            $this->_deployment->blockDeployment($request);
        } catch(Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("The deployment has been blocked");
    }

    public function unblockDeployment()
    {
        $request = Input::all();
        try
        {
            $this->_deployment->unblockDeployment($request);
        } catch(Exception $e){
            return Response::json($e->getMessage());
        }
        return Response::json("The deployment has been unblocked");
    }
}
