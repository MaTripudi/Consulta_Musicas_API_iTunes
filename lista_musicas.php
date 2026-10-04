<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Músicas - Resultados</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="container py-5">
    <?php
    if (isset($_POST['artista']) && !empty($_POST['artista'])) {
        $nomeArtistaInput =$_POST['artista'];
        $nomeArtista = urlencode($nomeArtistaInput);

        // Chama a API do iTunes
        $url = "https://itunes.apple.com/search?term=" . $nomeArtista . "&entity=song&limit=20";
        $json = @file_get_contents($url);

        if ($json !== false) {
            $dados = json_decode($json, true);

            echo "<div class='d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-danger-subtle'>";
            echo "  <h1 class='h2 m-0'>Resultados para: <span class='text-white'>" . htmlspecialchars($nomeArtistaInput) . "</span></h1>";
            echo "  <a href='consulta_artista.php' class='btn btn-primary'>Nova Busca</a>";
            echo "</div>";

            if (isset($dados["results"]) && count($dados["results"]) > 0) {
                echo "<div class='row g-4'>";
                foreach($dados["results"] as $index =>$musica){
                    $nome =$musica["trackName"] ?? 'Desconhecido';
                    $album =$musica["collectionName"] ?? 'Sem álbum';
                    // Substitui a imagem de 100x100 por uma de maior resolução (600x600)
                    $capa = isset($musica["artworkUrl100"]) ? str_replace('100x100bb', '600x600bb', $musica["artworkUrl100"]) : '';
                    $data_lancamento = isset($musica["releaseDate"]) ? date('d/m/Y', strtotime($musica["releaseDate"])) : 'N/A';
                    $genero =$musica["primaryGenreName"] ?? 'Gênero não informado';
                    $preview =$musica["previewUrl"] ?? '';
                    $audioId = "audio-" . $index;

                    echo "
                    <div class='col-12 col-md-6 col-lg-4'>
                        <div class='card music-card h-100 shadow'>
                            <div class='row g-0 h-100 flex-column'>
                                <div class='col-12 position-relative text-center p-3'>
                                    <img src='{$capa}' class='img-fluid rounded shadow-sm cover-img' alt='Capa do álbum {$album}'>
                                </div>
                                <div class='col-12 card-body d-flex flex-column justify-content-between'>
                                    <div>
                                        <h5 class='card-title text-truncate fw-bold mb-1' title='{$nome}'>{$nome}</h5>
                                        <p class='card-subtitle text-muted text-truncate mb-3' title='{$album}'>{$album}</p>
                                        
                                        <div class='music-info small mb-3'>
                                            <div><strong>Lançamento:</strong> {$data_lancamento}</div>
                                            <div><strong>Gênero:</strong> {$genero}</div>
                                        </div>
                                    </div>

                                    " . ($preview ? "
                                    <div class='custom-player p-2 rounded'>
                                        <audio id='{$audioId}' src='{$preview}'></audio>
                                        <div class='d-flex align-items-center gap-2 mb-2'>
                                            <button type='button' class='btn-play' onclick='toggleAudio(\"{$audioId}\", this)'>▶</button>
                                            <input type='range' class='form-range progress-bar-custom' id='progress-{$audioId}' value='0' min='0' max='100' oninput='seekAudio(\"{$audioId}\", this.value)'>
                                        </div>
                                        <div class='d-flex justify-content-between small text-muted px-1'>
                                            <span id='time-{$audioId}'>0:00</span>
                                            <span>0:30</span>
                                        </div>
                                    </div>
                                    " : "<p class='text-muted small fst-italic'>Prévia indisponível</p>") . "
                                </div>
                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";
            } else {
                echo "<div class='alert alert-warning-custom p-4 text-center rounded shadow-sm my-5'>
                        <h4>Nenhuma música encontrada</h4>
                        <p class='mb-0'>Tente buscar por outro artista ou verifique a ortografia.</p>
                      </div>";
            }
        } else {
            echo "<div class='alert alert-danger-custom p-4 text-center rounded shadow-sm my-5'>
                    <h4>Erro de Conexão</h4>
                    <p class='mb-0'>Não foi possível conectar ao serviço da Apple. Tente novamente mais tarde.</p>
                  </div>";
        }
    } else {
        echo "<div class='alert alert-warning-custom p-4 text-center rounded shadow-sm my-5'>
                <h4>Nenhum artista informado</h4>
                <a href='consulta_artista.php' class='btn btn-primary mt-3'>Voltar para a Busca</a>
              </div>";
    }
    ?>

    <footer id="rodape" class="text-center mt-5 pt-4 border-top border-secondary-subtle">
        <i>Feito por: Maria Luiza Tripudi</i>
    </footer>

    <!-- Script JS para controle do Player Personalizado -->
    <script>
    function toggleAudio(id, btn) {
        const audio = document.getElementById(id);
        const progressBar = document.getElementById('progress-' + id);
        const timeDisplay = document.getElementById('time-' + id);

        // Pausa todos os outros áudios rodando na página
        document.querySelectorAll('audio').forEach(otherAudio => {
            if (otherAudio !== audio) {
                otherAudio.pause();
                otherAudio.currentTime = 0;
                const otherId = otherAudio.id;
                const otherBtn = otherAudio.parentElement.querySelector('.btn-play');
                if (otherBtn) otherBtn.textContent = '▶';
            }
        });

        if (audio.paused) {
            audio.play();
            btn.textContent = '❚❚';
        } else {
            audio.pause();
            btn.textContent = '▶';
        }

        // Atualiza a barra de progresso e tempo
        audio.ontimeupdate = function() {
            if (audio.duration) {
                const percentage = (audio.currentTime / audio.duration) * 100;
                progressBar.value = percentage;
                
                const mins = Math.floor(audio.currentTime / 60);
                const secs = Math.floor(audio.currentTime % 60).toString().padStart(2, '0');
                timeDisplay.textContent = `${mins}:${secs}`;
            }
        };

        // Quando o áudio terminar
        audio.onended = function() {
            btn.textContent = '▶';
            progressBar.value = 0;
            timeDisplay.textContent = '0:00';
        };
    }

    function seekAudio(id, value) {
        const audio = document.getElementById(id);
        if (audio.duration) {
            audio.currentTime = (value / 100) * audio.duration;
        }
    }
    </script>
</body>
</html>