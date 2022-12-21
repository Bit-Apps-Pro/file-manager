<?php

namespace BitApps\FM\Model;

use BitApps\FM\Core\Database\Model;

/**
 * Undocumented class
 */
class Flow extends Model
{
    protected $casts = [
        'details' => 'object'
    ];

    protected $fillable = [
        'name',
        'details',
        'status',
    ];

    public function logs()
    {
        return $this->hasOne(FlowLog::class, 'flow_id');
    }
}
