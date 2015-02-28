<?php namespace CodeDad\Repositories;

use Carbon;
use CodeDad\Contracts\Review\IReviewRepository;
use CodeDad\Models\Review;
use Exception;

/**
 * Class ReviewRepository
 * @package CodeDad\Repositories\Review
 */

//TODO class may be too abstract maybe combine some functions like grabandComplete;
class ReviewRepository implements IReviewRepository
{

    /**
     * @var Review
     */
    protected $_review;

    /**
     * @param Review $review
     * @param Dispatcher $events
     */
    public function __construct(Review $review)
    {
        $this->_review = $review;
    }

    public function listAll()
    {
        return $this->_review->where('isCompleted', 0)->get();
    }

    public function addReview($review)
    {
        if ($this->_review->validate($review)) {
            $review['submitted'] = Carbon::now();
            $this->_review->create($review);
        } else {
            throw new Exception("Code Review Already Exists!");
        }
        return $review;
    }

    public function grabReviewByName($ticket,$name){
        return $this->_review->where('jira_ticket', $ticket)
            ->where('completion_user', $name)->first();
    }
    public function grabReviewByTicketNumber($ticket){
        return $this->_review->where('jira_ticket', $ticket)->first();
    }
    public function grabUnassignedReview($ticket){
        return $this->_review->where('jira_ticket', $ticket)->unassigned()->first();
    }

    public function grabUncompletedReviewByUser($ticket,$user)
    {
        return $this->_review->where('jira_ticket', $ticket)
            ->where('completion_user', $user)
            ->where('isCompleted', false)->first();
    }

    public function completeReview($review)
    {
        $update['isCompleted'] = true;
        $update['completion_time'] = Carbon::now();
        $review->fill($update);
        $review->save();
    }

    public function claimReviewResponsibility($review,$user)
    {
        $review->completion_user = $user;
        $review->save();
    }

    public function dropReviewResponsibility($review)
    {
        $review->completion_user = null;
        $review->save();
    }

    public function dropReviewByTicketNumber($ticket)
    {
        $review = $this->_review->where('jira_ticket',$ticket)->delete();
        if(!$review){
            throw new Exception("Ticket Does Not Exist");
        }

    }


}
