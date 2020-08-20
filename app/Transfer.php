<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transfer
 * @package App
 */
class Transfer extends Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromWallet()
    {
        return $this->belongsTo('App\Wallet', 'from');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toWallet()
    {
        return $this->belongsTo('App\Wallet', 'to');
    }
}
