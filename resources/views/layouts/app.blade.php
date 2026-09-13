<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dollie Shop')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Momo+Signature&display=swap" rel="stylesheet">

    @vite(['resources/css/user/home.css', 'resources/css/user/auth.css'])
</head>

<body>

    @include('partials.header')

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

</body>
</html>
