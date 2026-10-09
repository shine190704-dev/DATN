<?php

namespace App\Models;

class DonDatHang extends LegacyModel
{
    protected $table = 'dondathang';

    protected $primaryKey = 'DonDatHangID';

    protected $fillable = [
        'MaDonDatHang',
        'TrangThai',
        'TongTien',
        'SoTienDaThanhToan',
        'SoTienConNo',
        'NgayDatHang',
        'NgayDuKienNhan',
        'NgayNhanHang',
        'GhiChu',
        'NgayTao',
        'NgayCapNhat',
        'NhaCungCapID',
        'NguoiTaoID',
    ];
}
