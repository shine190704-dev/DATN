<?php

namespace App\Models;

class YeuCauHoanTien extends LegacyModel
{
    protected $table = 'yeucauhoantien';

    protected $primaryKey = 'YeuCauHoanTienID';

    protected $fillable = [
        'LyDo',
        'MoTa',
        'AnhMinhChung',
        'VideoMinhChung',
        'SoTienHoan',
        'TrangThai',
        'GhiChuXuLy',
        'NgayYeuCau',
        'NgayXuLy',
        'DonHangID',
        'NguoiDungID',
        'NguoiXuLyID',
    ];
}
