<?php namespace CodeDad\Helpers;


class FriendlyMessage{
    public function deploymentSuccess($step){
        switch ($step) {
            case 'isStaged':
                return "Ticket has been deployed to Staging";
            break;
            case 'isDeployed':
                return "Ticket has been deployed to Production";
                break;
            case 'isValidatedStaging':
                return "Ticket is ready for Production";
                break;
            case 'isValidated':
                return "Ticket can be moved to Production in Jira";
                break;
            default:
                return "How did I get here step does not exist";
        }
    }

    public function deploymentAlreadyThere($step){

    }
}