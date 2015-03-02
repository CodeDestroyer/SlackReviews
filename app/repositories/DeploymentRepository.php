<?php namespace CodeDad\Repositories;

use Carbon;
use CodeDad\Contracts\Deployments\IDeploymentRepository;
use CodeDad\Models\Deployment;
use Exception;

//Todo refactor querys in to scopes
/**
 * Class DeploymentRepository
 * @package CodeDad\Repositories
 */
class DeploymentRepository implements IDeploymentRepository
{
    /**
     * @var Deployment
     */
    protected $_deployment;

    /**
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->_deployment = $deployment;
    }

    /**
     * @param $deployment
     * @return mixed
     * @throws Exception
     */
    public function createDeployment($deployment)
    {
        if ($this->_deployment->validate($deployment)) {
            $deployment['submission_time'] = Carbon::now();
            $this->_deployment->create($deployment);
        } else {
            throw new Exception("Deployment Already Exists");
        }
        return $deployment;
    }

    /**
     * @param $ticket
     * @return mixed
     */
    public function grabDeploymentByTicket($ticket)
    {
        return $this->_deployment->where('jira_ticket', $ticket)->first();
    }
    /**
     * @param $deployment
     * @param $field
     * @param $direction
     */
    public function toggleField($deployment, $field, $direction)
    {
        $deployment->$field = $direction;
        $deployment->save();
    }

    /**
     * @param $ticket
     * @param $step
     * @return mixed
     */
    public function grabDeploymentByStep($ticket, $step)
    {
        return $this->_deployment->isNotBlocked()->$step()->where('jira_ticket', $ticket)->first();
    }

    /**
     * @return mixed
     */
    public function grabDeploymentForList()
    {
        return $this->_deployment
            ->where('submission_time', '>=', Carbon::now()->SUBDay()->startOfDay())
            ->orWhere('isValidated', false)
            ->get();
    }

    /**
     * @param $deployment
     * @param $comment
     */
    public function blockDeployment($deployment, $comment)
    {
        $deployment->blockReason = $comment;
        $deployment->save();
    }

    /**
     * @param $ticket
     * @throws Exception
     */
    public function dropDeploymentByTicket($ticket)
    {
        $deployment = $this->_deployment->where('jira_ticket', $ticket)->delete();
        if (!$deployment) {
            throw new Exception("Ticket Does Not Exist");
        }

    }
}