<?php
use CodeDad\Services\ReviewService;
//TODO I still feel there is a way to in
class ReviewController extends BaseController
{

    protected $_reviewService;

    public function __construct(ReviewService $review)
    {
        $this->_reviewService = $review;
    }

    /**
     * Start a code review request
     * @return JSON
     */
    public function requestReview()
    {
        $request = Input::all();
        $return = $this->_reviewService->addCodeReview($request);
        return Response::json($return);

    }

    /**
     * Complete a Code Review.
     */
    public function completeReview()
    {
        $request = Input::all();
        $return = $this->_reviewService->completeCodeReview($request);
        return Response::json($return);
    }

    /**
     * Returns a list of available reviews formatted for Slack
     * @return JSON response of the reviews available
     */
    public function listReviewsToUser()
    {
        $request = Input::all();
        $viewName = ($request['verbose'] ? 'listReviewsVerbose' :'listReviews');
        $reviews = $this->_reviewService->listCodeReviews();
        $viewData = View::make($viewName)->with('reviews', $reviews)->render();
        Event::fire('review.sendList', array($viewData));
        return Response::json($viewData);

    }

    public function listReviewDetails()
    {
        $request = Input::all();
        $return = $this->_reviewService->getCodeReviewDetails($request);
        return Response::json($return);

    }
    /**
     * Claim a Code Review
     * @return JSON either confirmation or rejection of claim
     */
    public function claimReview()
    {
        $request = Input::all();
        $return = $this->_reviewService->claimCodeReview($request);
        return Response::json($return);
    }

    /**
     * @return mixed
     */
    public function dropReviewResponsibility()
    {
        $review = Input::all();
        $return = $this->_reviewService->dropCodeReviewResponsibility($review);
        return Response::json($return);
    }

    public function dropReview()
    {
        $review = Input::all();
        $return = $this->_reviewService->dropCodeReview($review);
        return Response::json($return);
    }

}
