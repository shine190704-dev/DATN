<?php

namespace App\Models;

class SanPham extends LegacyModel
{
    protected $table = 'sanpham';

    protected $primaryKey = 'SanPhamID';

    protected $fillable = [
        'TenSanPham',
        'MoTa',
        'Gia',
        'ChatLieu',
        'BoLoc',
        'MaSKU',
        'NoiBat',
        'DaBan',
        'TrangThai',
        'LuongTonKhoThap',
        'NgayTao',
        'NgayCapNhat',
        'NhaCungCapID',
        'DanhMucID',
        'ThuongHieuID',
    ];
}
