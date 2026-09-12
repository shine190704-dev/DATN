<div class="product-sort">
    <span class="product-sort-label">SẮP XẾP THEO:</span>

    <select onchange="window.location.href = this.value">
        <option
            value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
            {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}
        >
            Mới nhất
        </option>

        <option
            value="{{ request()->fullUrlWithQuery(['sort' => 'az']) }}"
            {{ request('sort') == 'az' ? 'selected' : '' }}
        >
            A - Z
        </option>

        <option
            value="{{ request()->fullUrlWithQuery(['sort' => 'za']) }}"
            {{ request('sort') == 'za' ? 'selected' : '' }}
        >
            Z - A
        </option>

        <option
            value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}"
            {{ request('sort') == 'price_asc' ? 'selected' : '' }}
        >
            Thấp đến cao
        </option>

        <option
            value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}"
            {{ request('sort') == 'price_desc' ? 'selected' : '' }}
        >
            Cao đến thấp
        </option>
    </select>
</div>