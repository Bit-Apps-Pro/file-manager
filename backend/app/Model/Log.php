<?php

namespace BitApps\FM\Model;

use BitApps\FM\Core\Database\Model;

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

    protected $fillable = [
        'user_id',
        'command',
        'details',
        'created_at',
    ];
}
