<?php namespace CodeDad\Contracts\Deployments;

interface IDeploymentService
{
    public function addDeployment($deployment);
    public function unBlockDeployment($ticket);
    public function blockDeployment($ticket);
    public function toggleStep($deployment);
    public function listAllSorted();
}