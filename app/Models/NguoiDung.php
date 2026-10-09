<?php

namespace App\Models;

class NguoiDung extends LegacyModel
{
    protected $table = 'nguoidung';

    protected $primaryKey = 'NguoiDungID';

    protected $fillable = [
        'Ho',
        'Ten',
        'Email',
        'SoDienThoai',
        'MatKhau',
        'VaiTro',
        'TrangThai',
        'MaDatLaiMatKhau',
        'ThoiGianHetHanMaDatLaiMatKhau',
        'NgayTao',
        'NgayCapNhat',
    ];

    protected $hidden = [
        'MatKhau',
        'MaDatLaiMatKhau',
    ];
}
