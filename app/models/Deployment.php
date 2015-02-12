<?php namespace CodeDad\Models;

use Eloquent;
use Validator;

/**
 * Class Deployment
 * @package CodeDad\Models
 */
class Deployment extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $guarded = array('id');
    /**
     * @var array
     */
    private $_rules = array(
        'jira_ticket' => 'unique:deploys'
    );


    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        // make a new validator object
        $v = Validator::make($data, $this->_rules);

        // check for failure
        if ($v->fails()) {
            return false;
        }

        return true;
    }

}
