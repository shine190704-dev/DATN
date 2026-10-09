<?php

namespace App\Models;

class DiaChiNguoiDung extends LegacyModel
{
    protected $table = 'diachinguoidung';

    protected $primaryKey = 'DiaChiNguoiDungID';

    protected $fillable = [
        'TenNguoiNhan',
        'SoDienThoai',
        'DiaChi',
        'ThanhPho',
        'MacDinh',
        'NgayTao',
        'NgayCapNhat',
        'NguoiDungID',
    ];
}
