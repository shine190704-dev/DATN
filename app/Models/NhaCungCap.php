<?php

namespace App\Models;

class NhaCungCap extends LegacyModel
{
    protected $table = 'nhacungcap';

    protected $primaryKey = 'NhaCungCapID';

    protected $fillable = [
        'TenNhaCungCap',
        'SoDienThoai',
        'Email',
        'DiaChi',
        'TrangThai',
        'NgayCapNhat',
    ];
}
