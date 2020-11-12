<?php


namespace App\Models;


class Settings extends BaseModel
{
    protected $table = 'settings';

    public function getTypeAttribute($data)
    {
//        switch (strtolower($data)){
//            case 'int':
//                $this->attributes['value'] = intval($data);
//                break;
//            case 'array':
//                $this->attributes['value'] = json_decode($data, true);
//                break;
//
//        }
    }
}
