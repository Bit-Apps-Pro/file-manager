<?php

namespace BitApps\FM\Model;

use BitApps\FM\Core\Database\Model;

/**
 * Undocumented class
 */
class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'operation_id',
        'file_path',
        'time',
    ];
}
