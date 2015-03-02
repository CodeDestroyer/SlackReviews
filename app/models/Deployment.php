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
    protected $table = 'deploys';
    /**
     * @var array
     */
    private $_rules = array(
        'jira_ticket' => 'unique:deploys'
    );

    public function scopeisNotBlocked($query)
    {
        return $query->where('isBlocked', false);
    }

    //Shell for Staging since its first step
    public function scopeisStaged($query)
    {
        return $query;
    }
    public function scopeisValidatedStaging($query)
    {
        return $query->where('isStaged', true);
    }
    public function scopeisDeployed($query)
    {
        return $query->where('isValidatedStaging', true);
    }
    public function scopeisValidated($query)
    {
        return $query->where('isDeployed', true);
    }
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
