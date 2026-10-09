<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản trị - Dollie')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

    @vite('resources/css/admin/dashboard.css')
    @stack('styles')
</head>

<body>
    <div class="admin-shell">
        @include('admin.partials.sidebar')

        <main class="admin-main">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>