<?php

namespace BitApps\FM\Model;

use BitApps\FM\Core\Database\Model;

/**
 * Undocumented class
 */
class Tag extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'filter',
        'status',
    ];
}
