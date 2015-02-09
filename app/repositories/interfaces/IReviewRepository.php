<?php namespace CodeDad\Contracts\Review;

interface IReviewRepository
{

    public function all();

    public function find($hash);

    public function addReview($ticket);

}