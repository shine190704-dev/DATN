<?php

namespace App\Models;

class MaGiamGia extends LegacyModel
{
    protected $table = 'magiamgia';

    protected $primaryKey = 'MaGiamGiaID';

    protected $fillable = [
        'MaCode',
        'GiaTriGiam',
        'GiaTriDonHangToiThieu',
        'NgayHetHan',
        'TrangThai',
        'LoaiGiamGia',
        'SoLuongPhatHanh',
        'SoLuongDaCap',
        'NgayBatDau',
    ];
}
