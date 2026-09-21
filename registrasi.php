    <?php include "config/kriteria.php"; 
    include 'partials/header.php';
    include 'partials/footer.php';?>
    
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Registrasi Calon Penerima Bansos - SPK SAW</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
    </head>
    <body>
    <div class="container py-4">
        <div class="stepper">
            <div class="step active" id="st-1">1</div>
            <div class="step" id="st-2">2</div>
            <div class="step" id="st-3">3</div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8"><div class="card p-4">
                <?php include "partials/form_step.php"; ?>
                <?php include "partials/rfid_step.php"; ?>
                <?php include "partials/success_step.php"; ?>
            </div></div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
    </body>
    </html>
