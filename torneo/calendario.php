<?php
include "db.php";

$page_title = "Calendario e Risultati";
$active_page = "calendario";

// Edizione corrente
$ed_corrente = $conn->query("SELECT anno_scolastico FROM edizione ORDER BY anno_scolastico DESC LIMIT 1")->fetch_assoc();

if (!$ed_corrente) {
    $anno_corrente = '';
} else {
    $anno_corrente = $ed_corrente['anno_scolastico'];
}

// Filtri
$filtro_sport = isset($_GET['sport']) ? trim($_GET['sport']) : '';
$filtro_stato = isset($_GET['stato']) ? trim($_GET['stato']) : '';

$query = "
    SELECT 
        P.data,
        P.ora,
        P.campo,
        S1.nome AS squadra1,
        S2.nome AS squadra2,
        P.punteggio1,
        P.punteggio2,
        SP.nome AS sport,
        C.nome AS categoria
    FROM partita P
    JOIN squadra S1 ON P.id_squadra1 = S1.id_squadra
    JOIN squadra S2 ON P.id_squadra2 = S2.id_squadra
    JOIN torneo T ON P.id_torneo = T.id_torneo
    JOIN sport SP ON T.id_sport = SP.id_sport
    JOIN categoria C ON T.id_categoria = C.id_categoria
    JOIN edizione E ON T.id_edizione = E.id_edizione
    WHERE E.anno_scolastico = ?
";

$params = [$anno_corrente];
$types = 's';

if ($filtro_sport !== '') {
    $query .= " AND SP.nome = ?";
    $params[] = $filtro_sport;
    $types .= 's';
}

// Bisogna vedere i punteggi
if ($filtro_stato === 'giocate') {
    $query .= " AND P.punteggio1 IS NOT NULL";
} elseif ($filtro_stato === 'programmate') {
    $query .= " AND P.punteggio1 IS NULL";
}

/*
Sta cosa serve per ordinare dalla più nuova altrimenti non va
*/
$query .= " ORDER BY P.data DESC, P.ora DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Sport per filtro
$sports = $conn->query("SELECT nome FROM sport ORDER BY nome");

include "includes/header.php";
?>

<div class="page-container">

    <div class="page-header">
        <h2>Calendario e Risultati</h2>
        <p class="page-subtitle">Anno scolastico <?= htmlspecialchars($anno_corrente) ?></p>
    </div>

    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label for="sport">Sport:</label>
                <select name="sport" id="sport">
                    <option value="">Tutti</option>
                    <?php while ($s = $sports->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($s['nome']) ?>" <?= $filtro_sport === $s['nome'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="stato">Stato:</label>
                <select name="stato" id="stato">
                    <option value="">Tutte</option>
                    <option value="giocate" <?= $filtro_stato === 'giocate' ? 'selected' : '' ?>>Giocate</option>
                    <option value="programmate" <?= $filtro_stato === 'programmate' ? 'selected' : '' ?>>Da giocare</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">🔍 Filtra</button>

            <?php if ($filtro_sport !== '' || $filtro_stato !== ''): ?>
                <a href="calendario.php" class="btn-reset">✕ Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty-state">
            <p>📅 Nessuna partita trovata.</p>
            <a href="calendario.php" class="btn-back">Mostra tutte le partite</a>
        </div>
    <?php else: ?>

        <div class="matches-container">
            <?php
            $current_date = '';
            while ($row = $result->fetch_assoc()):
                $giocata = ($row['punteggio1'] !== null);
                $data_formattata = date('d/m/Y', strtotime($row['data']));
                $oggi = date('Y-m-d');

                if ($current_date !== $row['data']):
                    if ($current_date !== '') echo "</div>";
                    $current_date = $row['data'];

                    $classe_data = '';
                    if ($row['data'] === $oggi) $classe_data = 'match-today';
                    elseif ($row['data'] > $oggi) $classe_data = 'match-future';
            ?>
                <div class="match-day <?= $classe_data ?>">
                    <h3 class="match-day-title">
                        📅 <?= $data_formattata ?>
                        <?php if ($row['data'] === $oggi): ?>
                            <span class="badge-today">OGGI</span>
                        <?php endif; ?>
                    </h3>
            <?php endif; ?>

                <div class="match-card <?= $giocata ? 'played' : 'scheduled' ?>">
                    <div class="match-meta">
                        <span class="match-time">🕐 <?= htmlspecialchars(substr($row['ora'], 0, 5)) ?></span>
                        <span class="match-field">📍 <?= htmlspecialchars($row['campo']) ?></span>
                        <span class="badge badge-sport"><?= htmlspecialchars($row['sport']) ?></span>
                        <span class="badge badge-cat"><?= htmlspecialchars($row['categoria']) ?></span>
                    </div>
                    <div class="match-teams">
                        <span class="team-name home"><?= htmlspecialchars($row['squadra1']) ?></span>

                        <?php if ($giocata): ?>
                            <span class="match-score">
                                <span class="score"><?= (int)$row['punteggio1'] ?></span>
                                <span class="score-sep">-</span>
                                <span class="score"><?= (int)$row['punteggio2'] ?></span>
                            </span>
                        <?php else: ?>
                            <span class="match-score scheduled-label">VS</span>
                        <?php endif; ?>

                        <span class="team-name away"><?= htmlspecialchars($row['squadra2']) ?></span>
                    </div>
                </div>

            <?php endwhile; ?>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>