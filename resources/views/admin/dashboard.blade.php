<!DOCTYPE html>
<html lang="vi">

<head>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap"
        rel="stylesheet"
    >

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Dollie</title>

    @vite('resources/css/admin/dashboard.css')

</head>


<body>

    <div class="admin-dashboard admin-shell">

        @include('admin.partials.sidebar')

        <main class="admin-main">

        </main>

    </div>

</body>

</html>