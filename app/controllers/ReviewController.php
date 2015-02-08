<?php
use CodeDad\Repositories\Review\ReviewRepository;

class ReviewController extends BaseController {

    protected $_review;
    public function __construct(ReviewRepository $review)
    {
        $this->_review = $review;
    }

    public function testThis()
    {
        echo $this->_review->fooBar("i win");
    }

}
