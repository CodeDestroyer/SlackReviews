<?php
use CodeDad\Repositories\Review\ReviewRepository;
class ReviewController extends BaseController {

    protected $_review;
    public function __construct(ReviewRepository $review)
    {
        $this->_review = $review;
    }

    public function requestReview()
    {
        $request = Input::all();
        $this->_review->addReview($request);
    }
    public function claimReview()
    {
        $request = Input::all();
        $this->_review->claimReview($request);
    }
    public function completeReview()
    {
        $request = Input::all();
        $this->_review->completeReview($request);
    }

    public function listReviewsToUser()
    {
        $user = Input::get('user');
        $reviews = $this->_review->listAll();
        $viewData = View::make('listReviews')->with('reviews',$reviews)->render();
        $return = array(
            'user' => $user,
            'viewData' => $viewData
        );
        Event::fire('review.sendList', array($return));



    }

}
