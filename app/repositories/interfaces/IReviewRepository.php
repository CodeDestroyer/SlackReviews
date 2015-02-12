<?php namespace CodeDad\Contracts\Review;

interface IReviewRepository
{

    public function addReview($review);
    public function completeReview($review);
    public function claimReview($review);
}