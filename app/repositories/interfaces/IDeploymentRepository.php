<?php namespace CodeDad\Contracts\Deployments;

interface IDeploymentRepository
{

    public function createDeployment($deployment);
    public function grabDeploymentByTicket($ticket);
    public function toggleField($deployment,$field,$direction);
    public function grabDeploymentByStep($ticket,$step);
    public function grabDeploymentForList();
    public function blockDeployment($deployment,$comment);
}