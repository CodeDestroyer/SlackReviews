<?php namespace CodeDad\Repositories\Deployments;

use Carbon;
use CodeDad\Contracts\Deployments\IDeploymentRepository;
use CodeDad\Models\Deployment;
use Illuminate\Events\Dispatcher;
use Exception;

//Todo refactor querys in to scopes
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

    //TODO VALIDATE THAT THIS HAS BEEN CODE REVIEWED
    public function addDeployment($jTicket)
    {
        if ($this->_deployment->validate($jTicket)) {
            $jTicket['submission_time'] = Carbon::now();
            $this->_deployment->create($jTicket);
            $this->_events->fire('deployment.submitted', array($jTicket));
        } else {
            throw new Exception("Deployment Already Exists!");
        }
    }

    public function stageDeployment($jTicket)
    {
        $ticket = $jTicket['jira_ticket'];
        $step = 'isStaged';
        $deployment = $this->_deployment->where('jira_ticket', $jTicket['jira_ticket'])->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticket} Not Found");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticket} is already in Staging");
        }
        $this->_events->fire('deployment.staged', array($deployment));
        $this->_toggleOn($deployment, $step);

    }

    public function deployDeployment($jTicket)
    {
        $ticket = $jTicket['jira_ticket'];
        $step = 'isDeployed';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isValidatedStaging', true)->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticket} Not Found or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticket} is already in Production");
        }
        $this->_toggleOn($deployment, $step);

    }

    public function validateDeployment($jTicket)
    {
        $ticket = $jTicket['jira_ticket'];
        $step = 'isValidated';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isDeployed', true)
            ->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticket} Not Found or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticket} is already validated in Production");
        }
        $this->_toggleOn($deployment, $step);
    }

    public function validateStaging($jTicket)
    {
        $ticket = $jTicket['jira_ticket'];
        $step = 'isValidatedStaging';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isStaged', true)
            ->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticket} Not Found or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticket} is already validated in Staging");
        }
        $this->_toggleOn($deployment, $step);

    }

    private function _toggleOn($deployment, $field)
    {
        $deployment->$field = true;
        $deployment->save();
        $this->_events->fire('deployment.' . $field, array($deployment));
    }

    public function listAllSorted()
    {
        $sortedDeployments = array("production");
        $deployments = $this->_deployment->get();
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
            } else  {
                $sortedDeployments['readyforstaging'][] = $deployment;
            }
        }
        return $sortedDeployments;
    }
}