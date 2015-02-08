<?php namespace CodeDad\Repositories\Review;

use CodeDad\Contracts\Review\IReviewRepository;
use CodeDad\Models\Review;

class ReviewRepository implements IReviewRepository{

    protected $_review;
    public function __construct(Review $review){
        $this->_review = $review;
    }

    public function all()
    {
        return $this->_review->all();
    }

    public function find($id)
    {
        return $this->_review->find($id);
    }

    public function addReview($ticket)
    {
        return $this->_review->create($ticket);
    }

    public function fooBar($message){
        return $this->_review->testThis($message);
    }

}
