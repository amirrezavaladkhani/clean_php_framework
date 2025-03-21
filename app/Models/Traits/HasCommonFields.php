<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCommonFields
{
    public static function bootHasCommonFields(): void
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    public const COMMON_FIELDS = ['created_at', 'updated_at', 'deleted_at'];
}
