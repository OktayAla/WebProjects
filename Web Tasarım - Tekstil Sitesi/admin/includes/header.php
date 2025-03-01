<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegance Textile - Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        
        .sidebar .nav-link {
            padding: .5rem 1rem;
            color: #333;
            transition: all .3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,.1);
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Elegance Textile</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light btn-sm">Çıkış Yap</a>
            </div>
        </div>
    </nav>
