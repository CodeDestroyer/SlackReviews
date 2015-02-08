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
        $name = Input::get('ticket');
        Slack::to('@pat')->send($name);
    }

}
