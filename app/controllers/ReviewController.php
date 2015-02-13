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
        try {
            $this->_review->addReview($request);
        } catch (Exception $e) {
            return Response::json($e->getMessage());
        }
        return Response::json("Code Review Request Successful");
    }

    /**
     * Complete a Code Review.
     */
    public function completeReview()
    {
        $request = Input::all();
        try {
            $this->_review->completeReview($request);
        } catch (Exception $e) {
            return Response::json($e->getMessage());
        }
        return Response::json("Thank you for code-review!");
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

        try {
            $review = $this->_review->claimReview($request);

        } catch (Exception $e) {
            return Response::json($e->getMessage());
        }

        return Response::json("You have claimed {$review['jira_ticket']}. Notes from {$review['request_user']}: {$review['request_comments']}");
    }

    /**
     * @return mixed
     */
    public function dropReview()
    {
        $review = Input::all();
        try{
            $this->_review->dropReview($review);
        } catch(Exception $e) {
            return Response::json($e->getMessage());
        }
        return Response::json("You have successfully dropped the review");
    }

}
