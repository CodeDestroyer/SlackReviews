<?php
use \Carbon\Carbon;

?>
Deployments for <?php echo Carbon::now()->toFormattedDateString(); ?><?php echo "\r\n" ;?>
=======================<?php echo "\r\n" ;?>
*## In Production and Validated ##*<?php echo "\r\n" ;?>
<?php
if(isset($deployments['production'])){
foreach ($deployments['production'] as $deployment){
    echo "*{$deployment['jira_ticket']}*\r\n";
}
}?>
*## In Production Awaiting Validation ##*<?php echo "\r\n" ;?>
<?php if(isset($deployments['awaitingValidation'])){foreach ($deployments['awaitingValidation'] as $deployment){
    echo "*{$deployment['jira_ticket']}*\r\n";
}} ?>
*## Ready for Production ##*<?php echo "\r\n" ;?>
<?php if(isset($deployments['readyforprod'])){foreach ($deployments['readyforprod'] as $deployment){
    echo "*{$deployment['jira_ticket']}*\r\n";
}} ?>
*## In Staging Awating Validation ##*<?php echo "\r\n" ;?>
<?php if(isset($deployments['readyforVerfication'])){foreach ($deployments['readyforVerfication'] as $deployment){
    echo "*{$deployment['jira_ticket']}*\r\n";
}} ?>
*## Ready for Staging ##*<?php echo "\r\n" ;?>
<?php if(isset($deployments['readyforstaging'])){foreach ($deployments['readyforstaging'] as $deployment){
    echo "*{$deployment['jira_ticket']}*\r\n";
}} ?>
