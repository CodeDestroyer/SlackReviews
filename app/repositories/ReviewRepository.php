<?php namespace CodeDad\Repositories\Review;

use Carbon;
use CodeDad\Contracts\Review\IReviewRepository;
use CodeDad\Models\Review;
use Illuminate\Events\Dispatcher;
use Exception;

//TODO I Know this we should have a ReviewService to interact with the repo but this is so tiny right now.
/**
 * Class ReviewRepository
 * @package CodeDad\Repositories\Review
 */
class ReviewRepository implements IReviewRepository
{

    /**
     * @var Review
     */
    protected $_review;
    /**
     * @var Dispatcher
     */
    protected $_events;

    /**
     * @param Review $review
     * @param Dispatcher $events
     */
    public function __construct(Review $review, Dispatcher $events)
    {
        $this->_review = $review;
        $this->_events = $events;
    }

    /**
     * @return mixed
     */
    public function listAll()
    {
        return $this->_review->where('isCompleted', 0)->get();
    }

    /**
     * Add a review
     * @param $review
     * @throws Exception
     */
    public function addReview($review)
    {
        if ($this->_review->validate($review)) {
            $review['submitted'] = Carbon::now();
            $this->_review->create($review);
            $this->_events->fire('review.submitted', array($review));
        } else {
            $this->_events->fire('review.exists', array($review));
            throw new Exception("Code Review Already Exists!");
        }
    }

    /**
     * Complete the review
     * @param $update
     * @return bool
     * @throws Exception
     */
    public function completeReview($update)
    {
        $ticket = $update['jira_ticket'];
        $user = $update['completion_user'];
        try {
            $review = $this->_review->where('jira_ticket', $ticket)
                ->where('completion_user', $user)->first();
            if (empty($review)) {
                $event = array(
                    'user' => $user,
                    'ticket' => $ticket
                );
                $this->_events->fire('review.canNotComplete', array($event));
                throw new Exception("Review does not exist or you do not have ownership of review");
            }
            $update['isCompleted'] = true;
            $update['completion_time'] = Carbon::now();
            $review->fill($update);
            $review->save();

            $this->_events->fire('review.completed', array($review));
        } catch (Exception $e) {
            $event = array(
                'user' => $user,
                'ticket' => $ticket
            );
            $this->_events->fire('review.canNotComplete', array($event));
            throw new Exception("Review does not exist or you do not have ownership of review");
        }
        return true;
    }

    /**
     * Assign a code review to a user
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public function claimReview($request)
    {
        $ticket = $request['jira_ticket'];
        $user = $request['completion_user'];
        try {
            $review = $this->_review->where('jira_ticket', $ticket)->unassigned()->first();
            $review->completion_user = $user;
            $review->save();
            $this->_events->fire('review.claimed', array($review));
        } catch (\Exception $e) {
            $event = array(
                'user' => $user,
                'ticket' => $ticket
            );
            $this->_events->fire('review.notAvail', array($event));
            throw new Exception("Ticket {$ticket} cannot be claimed. Already claimed or wrong ID");
        }
        return $review;
    }

    /**
     * Drop Review that was assigned to a user
     * @param $request
     * @throws Exception
     */
    public function dropReview($request)
    {
        $ticket = $request['jira_ticket'];
        $user = $request['completion_user'];
        try{
            $dropped = $this->_review->where('jira_ticket', $ticket)
               ->where('completion_user',$user)
               ->where('isCompleted',false)->first();
            if(empty($dropped)){
                throw new Exception("You cannot drop this ticket, not owned by you or already completed");
            }
            $dropped->completion_user = null;
            $dropped->save();
            $this->_events->fire('review.dropped', array($dropped));
        } catch (Exception $e) {
            throw new Exception("You cannot drop this ticket, not owned by you or already completed");
        }

    }


}
