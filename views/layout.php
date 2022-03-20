<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Salón</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet"> 

    <link rel="preload" href="/public/build/css/app.css" as="style">
    <link rel="stylesheet" href="/public/build/css/app.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php echo $script ?? ""; //script individual para cada página si se necesita ?>
</head>
<body>
    <div class="contenedor-app">
        <div class="imagen"></div>
        <div class="app">
            <?php echo $contenido; ?>            
        </div>
    </div>


</body>
</html>