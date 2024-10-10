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
class User extends Model
{
    public $timestamps = false;

    protected $prefix = '';

    protected $fillable = [];
}
