<?php
include "db.php";

$page_title = "Squadre e Rose";
$active_page = "rose";

// Carica sport e edizioni dal DB per i filtri
$sports = $conn->query("SELECT id_sport, nome FROM sport ORDER BY nome");
$edizioni = $conn->query("SELECT id_edizione, anno_scolastico FROM edizione ORDER BY anno_scolastico DESC");

// Parametri filtro con validazione
$sport = isset($_GET['sport']) ? trim($_GET['sport']) : '';
$anno = isset($_GET['anno']) ? trim($_GET['anno']) : '';
$classe = isset($_GET['classe']) ? trim($_GET['classe']) : ''; // <-- NUOVO filtro classe (1..5)

// Query con prepared statement
$query = "
    SELECT 
        S.id_squadra,
        S.nome AS squadra,
        S.logo,
        SP.nome AS sport,
        C.nome AS categoria,
        E.anno_scolastico
    FROM squadra S
    JOIN torneo T ON S.id_torneo = T.id_torneo
    JOIN sport SP ON T.id_sport = SP.id_sport
    JOIN categoria C ON T.id_categoria = C.id_categoria
    JOIN edizione E ON T.id_edizione = E.id_edizione
    WHERE 1=1
";

$params = [];
$types = '';

if ($anno !== '') {
    $query .= " AND E.anno_scolastico = ?";
    $params[] = $anno;
    $types .= 's';
}

if ($sport !== '') {
    $query .= " AND SP.nome = ?";
    $params[] = $sport;
    $types .= 's';
}

/*
    ✅ MODIFICA PRINCIPALE:
    filtro per classe (1..5) in base al nome della squadra, es. "5E", "3A"...
    LEFT(S.nome, 1) prende la prima cifra.
*/
if ($classe !== '' && in_array($classe, ['1','2','3','4','5'], true)) {
    $query .= " AND LEFT(S.nome, 1) = ?";
    $params[] = $classe;
    $types .= 's';
}

$query .= " ORDER BY E.anno_scolastico DESC, SP.nome, C.nome, S.nome";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

include "includes/header.php";
?>

<div class="page-container">

    <div class="page-header">
        <h2>Squadre e Rose</h2>
        <p class="page-subtitle">Visualizza le rose delle squadre partecipanti</p>
    </div>

    <div class="filter-bar">
        <form method="GET" class="filter-form">

            <div class="filter-group">
                <label for="anno">Edizione:</label>
                <select name="anno" id="anno">
                    <option value="">Tutte le edizioni</option>
                    <?php while ($e = $edizioni->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($e['anno_scolastico']) ?>" <?= ($anno === $e['anno_scolastico']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['anno_scolastico']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="sport">Sport:</label>
                <select name="sport" id="sport">
                    <option value="">Tutti gli sport</option>
                    <?php while ($s = $sports->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($s['nome']) ?>" <?= ($sport === $s['nome']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- ✅ NUOVO MENU: CLASSE 1..5 IN ORDINE CRESCENTE -->
            <div class="filter-group">
                <label for="classe">Classe:</label>
                <select name="classe" id="classe">
                    <option value="">Tutte</option>
                    <?php foreach (['1','2','3','4','5'] as $cl): ?>
                        <option value="<?= $cl ?>" <?= ($classe === $cl) ? 'selected' : '' ?>>
                            <?= $cl ?>ª
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter">🔍 Filtra</button>

            <?php if ($sport !== '' || $anno !== '' || $classe !== ''): ?>
                <a href="rose.php" class="btn-reset">✕ Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty-state">
            <p>😕 Nessuna squadra trovata con i filtri selezionati.</p>
            <a href="rose.php" class="btn-back">Mostra tutte le squadre</a>
        </div>
    <?php else: ?>

        <div class="teams-grid">
            <?php while ($squadra = $result->fetch_assoc()): ?>
                <div class="team-card">
                    <div class="team-header">
                        <?php if ($squadra['logo']): ?>
                            <img src="<?= htmlspecialchars($squadra['logo']) ?>" alt="Logo" class="team-logo">
                        <?php else: ?>
                            <div class="team-logo-placeholder">
                                <?= mb_strtoupper(mb_substr($squadra['squadra'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>

                        <div class="team-info">
                            <h3><?= htmlspecialchars($squadra['squadra']) ?></h3>
                            <span class="badge badge-sport"><?= htmlspecialchars($squadra['sport']) ?></span>
                            <span class="badge badge-cat"><?= htmlspecialchars($squadra['categoria']) ?></span>
                            <span class="badge badge-anno"><?= htmlspecialchars($squadra['anno_scolastico']) ?></span>
                        </div>
                    </div>

                    <?php
                    $ids = (int)$squadra['id_squadra'];
                    $atleti = $conn->query("
                        SELECT nome, cognome
                        FROM atleta
                        WHERE id_squadra = $ids
                        ORDER BY cognome, nome
                    ");
                    ?>

                    <div class="team-roster">
                        <table class="roster-table">
                            <thead>
                                <tr>
                                    <th>Giocatore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($atleti->num_rows === 0): ?>
                                    <tr><td class="no-data">Nessun giocatore registrato</td></tr>
                                <?php else: ?>
                                    <?php while ($a = $atleti->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($a['cognome']) ?></strong> <?= htmlspecialchars($a['nome']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="roster-count">
                            👥 <?= $atleti->num_rows ?> giocator<?= $atleti->num_rows === 1 ? 'e' : 'i' ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>