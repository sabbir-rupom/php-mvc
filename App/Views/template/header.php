<head>
    <title><?= isset($title) ? $title : 'Welcome' ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="https://www.php.net/favicon.ico">

    <!-- Add External Library CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700&amp;subset=latin-ext">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    
    <!-- Add User Defined CSS -->
    <link rel="stylesheet" href="<?= baseUrl('assets/css/main.css') ?>">

    <!-- make base-url variable/constant for JavaScript usage -->
    <script> const baseUrl = '<?= baseUrl(); ?>'; </script>
</head>
