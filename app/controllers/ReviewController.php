<?php
use CodeDad\Repositories\Review\ReviewRepository;

class ReviewController extends BaseController
{

    protected $_review;

    public function __construct(ReviewRepository $review)
    {
        $this->_review = $review;
    }

    /**
     * Start a code review request
     * @return JSON
     */
    public function requestReview()
    {
        $request = Input::all();
        if ($this->_review->addReview($request)) {
            return Response::json("Code Review Request Successful");
        } else {
            return Response::json("Code Review Already Exists!");
        }
    }

    /**
     * Complete a Code Review.
     */
    public function completeReview()
    {
        $request = Input::all();
        if($this->_review->completeReview($request)){
            return Response::json("Thank you for code-review!");
        } else {
            return Response::json("Review does not exist or you do not have ownership of review");
        }
    }

    /**
     * Returns a list of available reviews formatted for Slack
     * @return JSON response of the reviews available
     */
    public function listReviewsToUser()
    {
        $reviews = $this->_review->listAll();
        $viewData = View::make('listReviews')->with('reviews', $reviews)->render();
        Event::fire('review.sendList', array($viewData));
        return Response::json($viewData);

    }

    /**
     * Claim a Code Review
     * @return JSON either confirmation or rejection of claim
     */
    public function claimReview()
    {
        $request = Input::all();
        $review = $this->_review->claimReview($request);
        if ($review) {
            return Response::json("You have claimed {$review['jira_ticket']}. Notes from {$review['request_user']}: {$review['request_comments']}");
        }
        else {
            return Response::json("Ticket {$request['jira_ticket']} cannot be claimed. Already claimed or wrong ID");
        }

    }

    public function dropReview(){

    }

}
