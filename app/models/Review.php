<?php namespace CodeDad\Models;

use Eloquent;

class Review extends Eloquent
{

    public function testThis($message){
        return "LOL --".$message;
    }

}
