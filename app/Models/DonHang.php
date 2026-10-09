<?php

namespace App\Models;

class DonHang extends LegacyModel
{
    protected $table = 'donhang';

    protected $primaryKey = 'DonHangID';

    protected $fillable = [
        'MaDonHang',
        'TongTien',
        'TenNguoiNhan',
        'SoDienThoaiNguoiNhan',
        'DiaChiNhanHang',
        'TrangThai',
        'PhuongThucThanhToan',
        'TrangThaiThanhToan',
        'MaThanhToan',
        'MaGiaoDich',
        'ThoiGianThanhToan',
        'ThoiHanThanhToan',
        'MaVanDon',
        'NgayTao',
        'NgayCapNhat',
        'NguoiDungID',
        'MaGiamGiaID',
    ];
}
