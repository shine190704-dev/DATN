<?php

namespace App\Models;

class DanhMuc extends LegacyModel
{
    protected $table = 'danhmuc';

    protected $primaryKey = 'DanhMucID';

    public $timestamps = false;

    protected $fillable = [
        'TenDanhMuc',
        'TrangThai',
        'NgayTao',
        'NgayCapNhat',
    ];
}
