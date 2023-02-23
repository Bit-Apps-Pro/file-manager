<?php

namespace BitApps\FM\Model;

use BitApps\FM\Core\Database\Model;

/**
 * Undocumented class.
 */
class Flow extends Model
{
    protected $casts = [
        'map'  => 'object',
        'data' => 'object'
    ];

    protected $fillable = [
        'title',
        'run_count',
        'is_active',
        'map',
        'data',
        'tag',
        'tag_id',
    ];

    public function logs()
    {
        return $this->hasOne(FlowLog::class, 'flow_id');
    }
}
