<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            margin: 0 auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #009688;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .data-box {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #009688;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Nuevo Mensaje de Contacto</h2>
        <p>Has recibido un nuevo mensaje desde el sitio web de campaña.</p>

        <div class="data-box">
            <?= $data['mensaje']; ?>
        </div>

        <p style="text-align: center; font-size: 12px; margin-top: 30px; color: #999;">
            Sistema de Votación - Campaña Chadan Rosado Taylor 2026
        </p>
    </div>
</body>

</html>