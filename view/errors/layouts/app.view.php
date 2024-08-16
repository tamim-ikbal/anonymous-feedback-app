<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@show('title') - Anonymous Feedback App</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css'); ?>">
</head>
<body class="bg-gray-100">
    @include('layouts/partials/header')

    @show('content')

    @include('layouts/partials/footer')
    @show('js')@endshow
</body>
</html>