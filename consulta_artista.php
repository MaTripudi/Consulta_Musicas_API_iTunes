<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Músicas</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="container mt-5">
    <form action="lista_musicas.php" method="post" class="card p-4 shadow-sm">
        <h1 class="h3 mb-3">BUSCAR MÚSICAS POR ARTISTA</h1>
        <div class="mb-3">
            <label for="artista" class="form-label">Nome do Artista</label>
            <input type="text" class="form-control" id="artista" required name="artista">
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
    <br>
    <div id="rodape" class="text-muted">
        <i style="color: #edeeef; font-size: 0.9rem; margin-top: 20px;">Feito por: Maria Luiza Tripudi</i>
    </div>
</body>
</html>