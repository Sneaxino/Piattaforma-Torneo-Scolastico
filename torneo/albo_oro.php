<?php
include "db.php";

$page_title = "Albo d'Oro";
$active_page = "albo";

// Carica tutte le edizioni per il menu a tendina
$edizioni_menu = $conn->query("
    SELECT id_edizione, anno_scolastico
    FROM edizione
    ORDER BY anno_scolastico DESC
");

// Filtro anno selezionato
$anno_sel = isset($_GET['anno']) ? trim($_GET['anno']) : '';

// Query edizioni da mostrare
if ($anno_sel !== '') {
    $stmt_ed = $conn->prepare("
        SELECT id_edizione, anno_scolastico
        FROM edizione
        WHERE anno_scolastico = ?
        ORDER BY anno_scolastico DESC
    ");
    $stmt_ed->bind_param('s', $anno_sel);
    $stmt_ed->execute();
    $edizioni = $stmt_ed->get_result();
} else {
    $edizioni = $conn->query("
        SELECT id_edizione, anno_scolastico
        FROM edizione
        ORDER BY anno_scolastico DESC
    ");
}

include "includes/header.php";
?>

<div class="page-container">

    <div class="page-header">
        <h2>🏆 Albo d'Oro</h2>
        <p class="page-subtitle">La storia del torneo d'Istituto</p>
    </div>

    <!-- FILTRO ANNO -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label for="anno">Anno Scolastico:</label>
                <select name="anno" id="anno">
                    <option value="">Tutte le edizioni</option>
                    <?php while ($em = $edizioni_menu->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($em['anno_scolastico']) ?>" <?= ($anno_sel === $em['anno_scolastico']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($em['anno_scolastico']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter">🔍 Filtra</button>

            <?php if ($anno_sel !== ''): ?>
                <a href="albo_oro.php" class="btn-reset">✕ Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- CONTENUTO -->
    <?php if ($edizioni->num_rows === 0): ?>
        <div class="empty-state">
            <p>😕 Nessuna edizione trovata.</p>
            <a href="albo_oro.php" class="btn-back">Mostra tutte le edizioni</a>
        </div>
    <?php else: ?>

        <?php while ($ed = $edizioni->fetch_assoc()):
            $id_ed = (int)$ed['id_edizione'];
        ?>

            <div class="albo-edizione">
                <h3 class="albo-anno">📆 Anno Scolastico <?= htmlspecialchars($ed['anno_scolastico']) ?></h3>

                <?php
                // Tornei di questa edizione
                $tornei = $conn->query("
                    SELECT T.id_torneo, SP.nome AS sport, C.nome AS categoria
                    FROM torneo T
                    JOIN sport SP ON T.id_sport = SP.id_sport
                    JOIN categoria C ON T.id_categoria = C.id_categoria
                    WHERE T.id_edizione = $id_ed
                    ORDER BY SP.nome, C.nome
                ");

                if ($tornei->num_rows === 0):
                ?>
                    <p class="no-data-text">Nessun torneo registrato per questa edizione.</p>
                <?php else:

                    // === SQUADRE VINCITRICI ===
                    $vincitori = [];
                    $tornei->data_seek(0);
                    while ($t = $tornei->fetch_assoc()):
                        $id_t = (int)$t['id_torneo'];

                        $vincitore = $conn->query("
                            SELECT 
                                S.nome,
                                COALESCE(SUM(CASE 
                                    WHEN (P.id_squadra1 = S.id_squadra AND P.punteggio1 > P.punteggio2) 
                                      OR (P.id_squadra2 = S.id_squadra AND P.punteggio2 > P.punteggio1) 
                                    THEN 3 
                                    WHEN P.punteggio1 = P.punteggio2 AND P.punteggio1 IS NOT NULL
                                    THEN 1 
                                    ELSE 0 END), 0) AS punti
                            FROM squadra S
                            LEFT JOIN partita P 
                                ON (P.id_squadra1 = S.id_squadra OR P.id_squadra2 = S.id_squadra) 
                                AND P.punteggio1 IS NOT NULL
                            WHERE S.id_torneo = $id_t
                            GROUP BY S.id_squadra, S.nome
                            ORDER BY punti DESC
                            LIMIT 1
                        ")->fetch_assoc();

                        if ($vincitore && (int)$vincitore['punti'] > 0) {
                            $vincitori[] = [
                                'squadra' => $vincitore['nome'],
                                'sport' => $t['sport'],
                                'categoria' => $t['categoria']
                            ];
                        }
                    endwhile;

                    if (!empty($vincitori)):
                ?>
                    <div class="albo-blocco">
                        <h4 class="albo-blocco-titolo">🥇 Squadre Vincitrici</h4>
                        <?php foreach ($vincitori as $v): ?>
                            <div class="albo-riga">
                                <span class="albo-icona">🏆</span>
                                <span class="albo-testo">
                                    <strong><?= htmlspecialchars($v['squadra']) ?></strong>
                                    <span class="albo-dettaglio">(<?= htmlspecialchars($v['sport']) ?> — <?= htmlspecialchars($v['categoria']) ?>)</span>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="albo-blocco">
                            <h4 class="albo-blocco-titolo">🥇 Squadre Vincitrici</h4>
                            <p class="no-data-text">Edizione in corso — risultati non ancora disponibili.</p>
                        </div>
                    <?php endif; ?>

                    <?php
                    // === PREMI INDIVIDUALI ===
                    $premi = $conn->query("
                        SELECT 
                            PI.tipo_premio,
                            A.nome,
                            A.cognome,
                            SP.nome AS sport,
                            C.nome AS categoria
                        FROM premio_individuale PI
                        JOIN atleta A ON PI.id_atleta = A.id_atleta
                        JOIN torneo T ON PI.id_torneo = T.id_torneo
                        JOIN sport SP ON T.id_sport = SP.id_sport
                        JOIN categoria C ON T.id_categoria = C.id_categoria
                        WHERE T.id_edizione = $id_ed
                        ORDER BY SP.nome, C.nome, PI.tipo_premio
                    ");

                    if ($premi->num_rows > 0):
                    ?>
                        <div class="albo-blocco">
                            <h4 class="albo-blocco-titolo">⭐ Premi Individuali</h4>
                            <?php while ($p = $premi->fetch_assoc()): ?>
                                <div class="albo-riga">
                                    <span class="albo-premio-tipo"><?= htmlspecialchars($p['tipo_premio']) ?></span>
                                    <span class="albo-testo">
                                        <strong><?= htmlspecialchars($p['nome']) ?> <?= htmlspecialchars($p['cognome']) ?></strong>
                                        <span class="albo-dettaglio">(<?= htmlspecialchars($p['sport']) ?> — <?= htmlspecialchars($p['categoria']) ?>)</span>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>