<?php
namespace tuanlq11\auditing;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by Mr.Tuan.
 * User: tuanlq
 * Date: 3/31/16
 * Time: 11:27 AM
 */
class Audit extends Model
{
    protected $table = 'audit';
    protected $fillable = [
        'id',
        'model',
        'model_id',
        'user_id',
        'new_value',
        'old_value',
        'action',
    ];

    protected static function boot()
    {
        self::saving(function (Audit $obj) {
            $obj->id = md5(uniqid($obj->toJson()));
        });
    }

}