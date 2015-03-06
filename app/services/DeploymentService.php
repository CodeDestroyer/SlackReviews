<?php namespace CodeDad\Services;

use Carbon;
use CodeDad\Contracts\Deployments\IDeploymentService;
use CodeDad\Repositories\DeploymentRepository;
use Exception;
use Illuminate\Events\Dispatcher;

//Todo Refactor a base service with things like fire_event and any other methods that come up
//TODO make helper to make errors more human readable
//TODO make success messages better as well.
/**
 * Class DeploymentService
 * @package CodeDad\Services
 */
class DeploymentService implements IDeploymentService
{
    /**
     * @var DeploymentRepository
     */
    private $_deploymentRepo;
    /**
     * @var Dispatcher
     */
    private $_event;

    /**
     * @param DeploymentRepository $deploymentRepo
     * @param Dispatcher $events
     */
    function __construct(DeploymentRepository $deploymentRepo, Dispatcher $events)
    {
        $this->_deploymentRepo = $deploymentRepo;
        $this->_event = $events;
    }

    /**
     * @param $deployment
     * @return string
     */
    public function addDeployment($deployment)
    {
        try {
            $this->_deploymentRepo->createDeployment($deployment);
            $this->_fireEvent('deployment.submitted', $deployment);
            return "Your ticket is ready for staging";

        } catch (Exception $e) {
            $this->_fireEvent('deployment.exists', $deployment);
            return $e->getMessage();
        }
    }

    /**
     * @param $deployment
     * @return string
     */
    public function toggleStep($deployment)
    {
        $ticketNumber = $deployment['jira_ticket'];
        $step = $deployment['step'];
        try {
            $deployment = $this->_deploymentRepo->grabDeploymentByStep($ticketNumber, $step);
            $this->_validateStep($deployment, $step);
            $this->_toggle($deployment, $step, true);
            if($step = 'isValidated'){
                $this->_deploymentRepo->completeTicket($deployment);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return \friendlyMessage::deploymentSuccess($step);
    }

    /**
     * @return array
     */
    public function listAllSorted()
    {
        $sortedDeployments = array("production");
        $deployments = $this->_deploymentRepo->grabDeploymentForList();
        foreach ($deployments as $deployment) {
            //FIZZ BUZZ UP IN THIS BITCH
            if ($deployment['isValidated']) {
                $sortedDeployments['production'][] = $deployment;
            } else if ($deployment['isDeployed']) {
                $sortedDeployments['awaitingValidation'][] = $deployment;
            } else if ($deployment['isValidatedStaging']) {
                $sortedDeployments['readyforprod'][] = $deployment;
            } else if ($deployment['isStaged']) {
                $sortedDeployments['readyforVerfication'][] = $deployment;
            } else {
                $sortedDeployments['readyforstaging'][] = $deployment;
            }
        }
        return $sortedDeployments;
    }

    /**
     * @param $ticket
     * @return string
     */
    public function blockDeployment($ticket)
    {
        $ticketNumber = $ticket['jira_ticket'];
        $comment = $ticket['block_comment'];
        try {
            $deployment = $this->_deploymentRepo->grabDeploymentByTicket($ticketNumber);
            if (empty($deployment)) {
                throw new Exception("Jira Ticket {$ticketNumber} not found");
            }
            $this->_deploymentRepo->blockDeployment($deployment, $comment);
            $this->_toggle($deployment, 'isBlocked', true);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "Jira Ticket {$ticketNumber} is now Blocked";
    }

    /**
     * @param $ticket
     * @return string
     */
    public function unBlockDeployment($ticket)
    {
        $ticketNumber = $ticket['jira_ticket'];
        try {
            $deployment = $this->_deploymentRepo->grabDeploymentByTicket($ticketNumber);
            if (empty($deployment)) {
                throw new Exception("Jira Ticket {$ticketNumber} not found");
            }
            $this->_toggle($deployment, 'isBlocked', false);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "Jira Ticket {$ticketNumber} is now Unblocked";
    }

    public function deleteDeployment($deployment){
        $ticket = $deployment['jira_ticket'];
        try {
            $this->_deploymentRepo->dropDeploymentByTicket($ticket);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "Deployment for Ticket {$ticket} has been removed.";
    }

    /**
     * @param $name
     * @param $data
     */
    private function _fireEvent($name, $data)
    {
        $this->_event->fire($name, array($data));
    }

    /**
     * @param $deployment
     * @param $field
     * @param $direction
     */
    private function _toggle($deployment, $field, $direction)
    {
        $deployment->$field = $direction;
        $deployment->save();
        $this->_fireEvent('deployment.' . $field .".". $direction, $deployment);
    }

    /**
     * @param $deployment
     * @param $step
     * @return bool
     * @throws Exception
     */
    private function _validateStep($deployment, $step)
    {
        $ticketNumber = $deployment['jira_ticket'];
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} Not Found or is Blocked");
        } else if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$deployment['jira_ticket']} has flag {$step} already");
        } else return true;
    }

}