<?php namespace CodeDad\Contracts\Review;

interface IReviewService
{
    public function addCodeReview($review);
    public function completeCodeReview($review);
    public function claimCodeReview($review);
    public function dropCodeReviewResponsibility($review);
}