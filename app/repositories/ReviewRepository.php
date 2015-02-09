<?php namespace CodeDad\Repositories\Review;

use Carbon;
use CodeDad\Contracts\Review\IReviewRepository;
use CodeDad\Models\Review;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

//TODO I Know this we should have a ReviewService to interact with the repo but this is so tiny right now.
class ReviewRepository implements IReviewRepository
{

    protected $_review;
    /**
     * @var Dispatcher
     */
    protected $_events;

    public function __construct(Review $review, Dispatcher $events)
    {
        $this->_review = $review;
        $this->_events = $events;
    }

    public function all()
    {
        return $this->_review->all();
    }

    public function listAll()
    {
        return $this->_review->where('isCompleted',0)->get();
    }

    public function find($id)
    {
        return $this->_review->find($id);
    }

    public function addReview($review)
    {
        if ($this->_review->validate($review)) {
            $review['submitted'] = Carbon::now();
            $this->_review->create($review);
            $this->_events->fire('review.submitted', array($review));
        } else
        {
            $this->_events->fire('review.exists', array($review));
        }
    }

    public function completeReview($update)
    {
        $ticket = $update['jira_ticket'];
        $user = $update['completion_user'];
        try {
            $review = $this->_review->where('jira_ticket', $ticket)
                                    ->where('completion_user',$user)->first();
            if(empty($review)){
                $event = array(
                    'user' => $user,
                    'ticket' => $ticket
                );
                $this->_events->fire('review.canNotComplete', array($event));
                return;
            }
            $update['isCompleted'] = true;
            $update['completion_time'] = Carbon::now();
            $review->fill($update);
            $review->save();

            $this->_events->fire('review.completed', array($review));
        } catch (\FatalErrorException $e) {
            $event = array(
                'user' => $user,
                'ticket' => $ticket
            );
            $this->_events->fire('review.canNotComplete', array($event));
        }
    }

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
        }
    }



}
