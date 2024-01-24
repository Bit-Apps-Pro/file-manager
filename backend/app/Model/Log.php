<?php

namespace BitApps\FM\Model;

use BitApps\WPDatabase\Model;

// use BitApps\WPKit\Database\Model

/**
 * Model for log
 *
 * @property string $created_at
 * @property string $command
 * @property string $details
 * @property int    $user_id
 */
class Log extends Model
{
    public $timestamps = false;

    public $casts = ['details' => 'object'];

    protected $fillable = [
        'user_id',
        'command',
        'details',
        'created_at',
    ];
}
