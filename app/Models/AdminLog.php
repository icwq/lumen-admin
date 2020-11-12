<?php


namespace App\Models;


class AdminLog extends BaseModel
{

    protected $table = "admin_log";


    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id');
    }
}
