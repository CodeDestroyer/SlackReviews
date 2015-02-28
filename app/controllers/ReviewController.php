<?php
use CodeDad\Services\ReviewService;
//TODO I still feel there is a way to in
class ReviewController extends BaseController
{

    protected $_review;

    public function __construct(ReviewService $review)
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
        $return = $this->_review->addCodeReview($request);
        return Response::json($return);

    }

    /**
     * Complete a Code Review.
     */
    public function completeReview()
    {
        $request = Input::all();
        $return = $this->_review->completeCodeReview($request);
        return Response::json($return);
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
        $return = $this->_review->claimCodeReview($request);
        return Response::json($return);
    }

    /**
     * @return mixed
     */
    public function dropReviewResponsibility()
    {
        $review = Input::all();
        $return = $this->_review->dropCodeReviewResponsibility($review);
        return Response::json($return);
    }

}
