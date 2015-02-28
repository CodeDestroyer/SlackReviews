<?php namespace CodeDad\Repositories;

use Carbon;
use CodeDad\Contracts\Review\IReviewRepository;
use CodeDad\Models\Review;
use Exception;


//TODO class may be too abstract maybe combine some functions like grabandComplete;
/**
 * Class ReviewRepository
 * @package CodeDad\Repositories
 */
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

    /**
     * @return mixed
     */
    public function listAll()
    {
        return $this->_review->where('isCompleted', 0)->get();
    }

    /**
     * @param $review
     * @return mixed
     * @throws Exception
     */
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

    /**
     * @param $ticket
     * @param $name
     * @return mixed
     */
    public function grabReviewByName($ticket, $name)
    {
        return $this->_review->where('jira_ticket', $ticket)
            ->where('completion_user', $name)->first();
    }

    /**
     * @param $ticket
     * @return mixed
     */
    public function grabReviewByTicketNumber($ticket)
    {
        return $this->_review->where('jira_ticket', $ticket)->first();
    }

    /**
     * @param $ticket
     * @return mixed
     */
    public function grabUnassignedReview($ticket)
    {
        return $this->_review->where('jira_ticket', $ticket)->unassigned()->first();
    }

    /**
     * @param $ticket
     * @param $user
     * @return mixed
     */
    public function grabUncompletedReviewByUser($ticket, $user)
    {
        return $this->_review->where('jira_ticket', $ticket)
            ->where('completion_user', $user)
            ->where('isCompleted', false)->first();
    }

    /**
     * @param $review
     */
    public function completeReview($review)
    {
        $update['isCompleted'] = true;
        $update['completion_time'] = Carbon::now();
        $review->fill($update);
        $review->save();
    }

    /**
     * @param $review
     * @param $user
     */
    public function claimReviewResponsibility($review, $user)
    {
        $review->completion_user = $user;
        $review->save();
    }

    /**
     * @param $review
     */
    public function dropReviewResponsibility($review)
    {
        $review->completion_user = null;
        $review->save();
    }

    /**
     * @param $ticket
     * @throws Exception
     */
    public function dropReviewByTicketNumber($ticket)
    {
        $review = $this->_review->where('jira_ticket', $ticket)->delete();
        if (!$review) {
            throw new Exception("Ticket Does Not Exist");
        }

    }


}
