<?php namespace CodeDad\Contracts\Review;

interface IReviewRepository
{
    public function addReview($review);
    public function completeReview($review);
    public function grabUnassignedReview($review);
    public function claimReviewResponsibility($review,$user);
    public function dropReviewResponsibility($review);
    public function grabUncompletedReviewByUser($ticket,$user);
    public function grabReviewByName($review,$user);
    public function dropReviewByTicketNumber($ticket);
    public function grabReviewByTicketNumber($ticket);
    public function listAll();
}