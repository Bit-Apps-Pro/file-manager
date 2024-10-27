<?php

namespace BitApps\FM\Model;

use BitApps\FM\Config;
use BitApps\WPDatabase\Model;

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

    protected $prefix = Config::VAR_PREFIX;

    public $casts = ['details' => 'object'];

    protected $fillable = [
        'user_id',
        'command',
        'details',
        'created_at',
    ];

    public function user() {
        return $this->hasOne(User::class, 'ID', 'user_id');
    }
}
