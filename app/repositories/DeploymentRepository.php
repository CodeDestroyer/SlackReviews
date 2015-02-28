<?php namespace CodeDad\Repositories;

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
        $ticketNumber = $jTicket['jira_ticket'];
        $step = 'isStaged';
        $deployment = $this->_deployment->isNotBlocked()->where('jira_ticket', $jTicket['jira_ticket'])->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} Not Found or is Blocked");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticketNumber} is already in Staging");
        }
        $this->_events->fire('deployment.staged', array($deployment));
        $this->_toggle($deployment, $step);

    }

    public function deployDeployment($jTicket)
    {
        $ticketNumber = $jTicket['jira_ticket'];
        $step = 'isDeployed';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->isNotBlocked()->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isValidatedStaging', true)->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} is either: not found, blocked, or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticketNumber} is already in Production");
        }
        $this->_toggle($deployment, $step);

    }

    public function validateDeployment($jTicket)
    {
        $ticketNumber = $jTicket['jira_ticket'];
        $step = 'isValidated';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->isNotBlocked()->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isDeployed', true)
            ->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} is either: not found, blocked, or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticketNumber} is already validated in Production");
        }
        $this->_toggle($deployment, $step);
    }

    public function validateStaging($jTicket)
    {
        $ticketNumber = $jTicket['jira_ticket'];
        $step = 'isValidatedStaging';
        //TODO SCOPE THIS
        $deployment = $this->_deployment->isNotBlocked()->where('jira_ticket', $jTicket['jira_ticket'])
            ->where('isStaged', true)
            ->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} is either not found, blocked, or not ready");
        }
        if ($deployment[$step]) {
            throw new Exception("Jira Ticket {$ticketNumber} is already validated in Staging");
        }
        $this->_toggle($deployment, $step);

    }

    public function listAllSorted()
    {
        $sortedDeployments = array("production");
        $deployments = $this->_deployment
            ->where('submission_time', '>=', Carbon::now()->SUBDay()->startOfDay())
            ->orWhere('isValidated',false)
            ->get();
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

    public function blockDeployment($ticket)
    {
        $ticketNumber = $ticket['jira_ticket'];
        $comment = $ticket['block_comment'];
        $deployment = $this->_deployment->where('jira_ticket', $ticketNumber)->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} not found");
        }
        $deployment->blockReason = $comment;
        $deployment->save();
        $this->_toggle($deployment, 'isBlocked');
    }

    public function unBlockDeployment($ticket)
    {
        $ticketNumber = $ticket['jira_ticket'];
        $deployment = $this->_deployment->where('jira_ticket', $ticketNumber)
                                        ->where('isBlocked', true)->first();
        if (empty($deployment)) {
            throw new Exception("Jira Ticket {$ticketNumber} not found or not Blocked");
        }
        $this->_toggle($deployment, 'isBlocked', false);
    }

    private function _toggle($deployment, $field,$way = true)
    {
        $deployment->$field = $way;
        $deployment->save();
        //TODO Refactor to fire on and off events rather then this hack.
        if(!$way){
            $field = $field."off";
        }
        $this->_events->fire('deployment.' . $field, array($deployment));
    }
}