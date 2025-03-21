<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCommonFields
{
    use SoftDeletes; // Enables the `deleted_at` field for soft deletes

    protected static function bootHasCommonFields()
    {
        static::creating(function (Model $model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected function createdAt(): Attribute
    {
        return Attribute::get(fn($value) => \Carbon\Carbon::parse($value)->toDateTimeString());
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::get(fn($value) => \Carbon\Carbon::parse($value)->toDateTimeString());
    }

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['created_at', 'updated_at', 'deleted_at'];
}
