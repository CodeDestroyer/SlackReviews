<?php namespace CodeDad\Services;

use Carbon;
use CodeDad\Contracts\Review\IReviewService;
use CodeDad\Repositories\ReviewRepository;
use Exception;
use Illuminate\Events\Dispatcher;

//TODO Should validate that array keys exist before casting.
/**
 * Class ReviewRepository
 * @package CodeDad\Repositories\Review
 */
class ReviewService implements IReviewService
{
    /**
     * @var ReviewRepository
     */
    protected $_reviewRepo;
    /**
     * @var
     */
    protected $_events;

    /**
     * @param ReviewRepository $reviewRepo
     * @param Dispatcher $events
     */
    public function __construct(ReviewRepository $reviewRepo, Dispatcher $events)
    {
        $this->_reviewRepo = $reviewRepo;
        $this->_event = $events;
    }

    /**
     * @param $review
     * @return string
     */
    public function addCodeReview($review)
    {
        try {
            $this->_reviewRepo->addReview($review);
            $this->_fireEvent('review.submitted', $review);
            return "Code Review Request Successful";

        } catch (Exception $e) {
            $this->_fireEvent('review.exists',$review);
            return $e->getMessage();
        }
    }

    //TODO stop completing same ticket, check if isComplete and fire new error.
    /**
     * @param $review
     * @return string
     */
    public function completeCodeReview($review)
    {
        $ticket = $review['jira_ticket'];
        $user = $review['completion_user'];
        try {
            $review = $this->_reviewRepo->grabReviewByName($ticket, $user);
            if (empty($review)) {
                $this->_fireEvent('review.canNotComplete', array('user' => $user, 'ticket' => $ticket));
                return "Review does not exist or you do not have ownership of review";
            }
            $this->_reviewRepo->completeReview($review);
            $this->_fireEvent('review.completed', $review);
        } catch (Exception $e) {
            $this->_fireEvent('review.canNotComplete', array('user' => $user, 'ticket' => $ticket));
            return "Review does not exist or you do not have ownership of review";
        }
        return "Thank you for code-review!";
    }

    /**
     * @param $review
     * @return string
     */
    public function claimCodeReview($review)
    {
        $ticket = $review['jira_ticket'];
        $user = $review['completion_user'];
        try {
            $review = $this->_reviewRepo->grabUnassignedReview($ticket);
            $this->_reviewRepo->claimReviewResponsibility($review, $user);
            $this->_fireEvent('review.claimed', $review);
        } catch (Exception $e) {
            $this->_fireEvent('review.notAvail', array('user' => $user, 'ticket' => $ticket));
            return "Ticket {$ticket} cannot be claimed. Already claimed or wrong ID";
        }
        return "You have claimed {$review['jira_ticket']}. You can get more info here {$review['repo_link']}";
    }

    /**
     * @param $review
     * @return string
     */
    public function dropCodeReviewResponsibility($review)
    {
        $ticket = $review['jira_ticket'];
        $user = $review['completion_user'];
        try {
            $droppedReview = $this->_reviewRepo->grabUncompletedReviewByUser($ticket, $user);
            if (empty($droppedReview)) {
                return "You cannot drop this ticket, not owned by you or already completed";
            }
            $this->_reviewRepo->dropReviewResponsibility($droppedReview);
            $this->_fireEvent('review.dropped', $droppedReview);
        } catch (Exception $e) {
            return "You cannot drop this ticket, not owned by you or already completed";
        }
        return "You have been removed from the ticket";
    }

    /**
     * @param $review
     * @return string
     */
    public function dropCodeReview($review)
    {
        $ticket = $review['jira_ticket'];
        try {
            $this->_reviewRepo->dropReviewByTicketNumber($ticket);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "Ticket {$ticket} has been removed.";
    }

    /**
     * @return mixed
     */
    public function listCodeReviews(){
        return $this->_reviewRepo->listAll();
    }

    /**
     * @param $review
     * @return string
     */
    public function getCodeReviewDetails($review){
        $ticket = $review['jira_ticket'];
        try {
           $ticketDetails =  $this->_reviewRepo->grabReviewByTicketNumber($ticket);
           $formattedTime = Carbon::createFromTimestamp(strtotime($ticketDetails['submitted']))->toFormattedDateString();
            if (empty($ticketDetails)) {
                return "Jira Ticket {$ticket} does not exist";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "Ticket {$ticket} - {$ticketDetails['repo_link']} submitted by {$ticketDetails['request_user']} on {$formattedTime}";
    }
    /**
     * @param $name
     * @param $data
     */
    private function _fireEvent($name, $data)
    {
        $this->_event->fire($name, array($data));
    }

}