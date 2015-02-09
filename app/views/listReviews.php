Code Review Status<?php echo "\r\n" ;?>
-------------------------------------------------<?php echo "\r\n" ;?>
<?php if ($reviews->isEmpty()): ?>

<?php else: ?>
    <?php foreach($reviews as $review): ?>
<?php echo "Ticket: *{$review['jira_ticket']}* created by {$review['request_user']} posted ".
            \Carbon\Carbon::createFromTimeStamp(strtotime($review['submitted']))->diffForHumans().
            ($review['completion_user'] ? " *assigned to {$review['completion_user']}* " : "" )."\r\n" ; ?>
    <?php endforeach; ?>
<?php endif; ?>