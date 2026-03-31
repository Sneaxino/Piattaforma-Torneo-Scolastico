<?php
include "db.php";

$page_title = "Classifica";
$active_page = "classifica";

// Edizione corrente
$ed_corrente = $conn->query("
    SELECT id_edizione, anno_scolastico 
    FROM edizione 
    ORDER BY anno_scolastico DESC 
    LIMIT 1
")->fetch_assoc();

if (!$ed_corrente) {
    include "includes/header.php";
    echo "<div class='page-container'><div class='empty-state'><p>Nessuna edizione trovata.</p></div></div>";
    include "includes/footer.php";
    exit;
}

$id_edizione = (int)$ed_corrente['id_edizione'];
$anno_corrente = $ed_corrente['anno_scolastico'];

// Tornei dell'edizione corrente
$tornei = $conn->query("
    SELECT T.id_torneo, SP.nome AS sport, C.nome AS categoria
    FROM torneo T
    JOIN sport SP ON T.id_sport = SP.id_sport
    JOIN categoria C ON T.id_categoria = C.id_categoria
    WHERE T.id_edizione = $id_edizione
    ORDER BY SP.nome, C.nome
");

include "includes/header.php";
?>

<div class="page-container">

    <div class="page-header">
        <h2>📊 Classifica</h2>
        <p class="page-subtitle">Anno scolastico <?= htmlspecialchars($anno_corrente) ?> — aggiornata in tempo reale</p>
    </div>

    <?php if ($tornei->num_rows === 0): ?>
        <div class="empty-state">
            <p>Nessun torneo attivo per l'anno corrente.</p>
        </div>
    <?php else: ?>

        <?php while ($torneo = $tornei->fetch_assoc()): ?>
            <?php
            $id_torneo = (int)$torneo['id_torneo'];

            // Classifica calcolata dinamicamente
            // punteggio1 IS NOT NULL = partita giocata
            $classifica_query = $conn->query("
                SELECT 
                    S.id_squadra,
                    S.nome,
                    COUNT(P.id_partita) AS partite_giocate,
                    SUM(CASE 
                        WHEN (P.id_squadra1 = S.id_squadra AND P.punteggio1 > P.punteggio2) 
                          OR (P.id_squadra2 = S.id_squadra AND P.punteggio2 > P.punteggio1) 
                        THEN 1 ELSE 0 END) AS vinte,
                    SUM(CASE 
                        WHEN P.punteggio1 = P.punteggio2 
                        THEN 1 ELSE 0 END) AS pareggiate,
                    SUM(CASE 
                        WHEN (P.id_squadra1 = S.id_squadra AND P.punteggio1 < P.punteggio2) 
                          OR (P.id_squadra2 = S.id_squadra AND P.punteggio2 < P.punteggio1) 
                        THEN 1 ELSE 0 END) AS perse,
                    COALESCE(SUM(CASE 
                        WHEN P.id_squadra1 = S.id_squadra THEN P.punteggio1 
                        WHEN P.id_squadra2 = S.id_squadra THEN P.punteggio2 
                        ELSE 0 END), 0) AS fatti,
                    COALESCE(SUM(CASE 
                        WHEN P.id_squadra1 = S.id_squadra THEN P.punteggio2 
                        WHEN P.id_squadra2 = S.id_squadra THEN P.punteggio1 
                        ELSE 0 END), 0) AS subiti,
                    COALESCE(SUM(CASE 
                        WHEN (P.id_squadra1 = S.id_squadra AND P.punteggio1 > P.punteggio2) 
                          OR (P.id_squadra2 = S.id_squadra AND P.punteggio2 > P.punteggio1) 
                        THEN 3 
                        WHEN P.punteggio1 = P.punteggio2 
                        THEN 1 
                        ELSE 0 END), 0) AS punti
                FROM squadra S
                LEFT JOIN partita P 
                    ON (P.id_squadra1 = S.id_squadra OR P.id_squadra2 = S.id_squadra) 
                    AND P.punteggio1 IS NOT NULL
                WHERE S.id_torneo = $id_torneo
                GROUP BY S.id_squadra, S.nome
                ORDER BY punti DESC,
                    (COALESCE(SUM(CASE WHEN P.id_squadra1 = S.id_squadra THEN P.punteggio1 WHEN P.id_squadra2 = S.id_squadra THEN P.punteggio2 ELSE 0 END), 0) 
                   - COALESCE(SUM(CASE WHEN P.id_squadra1 = S.id_squadra THEN P.punteggio2 WHEN P.id_squadra2 = S.id_squadra THEN P.punteggio1 ELSE 0 END), 0)) DESC,
                    fatti DESC
            ");

            $num_squadre = $classifica_query->num_rows;
            ?>

            <div class="classifica-section">
                <h3 class="classifica-title">
                    <?= htmlspecialchars($torneo['sport']) ?> — <?= htmlspecialchars($torneo['categoria']) ?>
                </h3>

                <?php if ($num_squadre === 0): ?>
                    <p class="no-data-text">Nessuna squadra iscritta a questo torneo.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="classifica-table">
                            <thead>
                                <tr>
                                    <th class="pos">#</th>
                                    <th class="team-col">Squadra</th>
                                    <th>PG</th>
                                    <th>V</th>
                                    <th>P</th>
                                    <th>S</th>
                                    <th>GF</th>
                                    <th>GS</th>
                                    <th>DR</th>
                                    <th class="pts">Punti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $pos = 1;
                                while ($row = $classifica_query->fetch_assoc()):
                                    $diff = (int)$row['fatti'] - (int)$row['subiti'];
                                    $diff_str = ($diff > 0 ? '+' : '') . $diff;
                                    $row_class = '';
                                    if ($pos === 1) $row_class = 'first-place';
                                    elseif ($pos === $num_squadre && $num_squadre > 1) $row_class = 'last-place';
                                ?>
                                    <tr class="<?= $row_class ?>">
                                        <td class="pos">
                                            <?php if ($pos === 1): ?>
                                                <span class="pos-medal">🥇</span>
                                            <?php elseif ($pos === 2): ?>
                                                <span class="pos-medal">🥈</span>
                                            <?php elseif ($pos === 3): ?>
                                                <span class="pos-medal">🥉</span>
                                            <?php else: ?>
                                                <?= $pos ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="team-col"><strong><?= htmlspecialchars($row['nome']) ?></strong></td>
                                        <td><?= (int)$row['partite_giocate'] ?></td>
                                        <td class="win"><?= (int)$row['vinte'] ?></td>
                                        <td><?= (int)$row['pareggiate'] ?></td>
                                        <td class="loss"><?= (int)$row['perse'] ?></td>
                                        <td><?= (int)$row['fatti'] ?></td>
                                        <td><?= (int)$row['subiti'] ?></td>
                                        <td class="<?= $diff > 0 ? 'positive' : ($diff < 0 ? 'negative' : '') ?>"><?= $diff_str ?></td>
                                        <td class="pts"><strong><?= (int)$row['punti'] ?></strong></td>
                                    </tr>
                                <?php
                                    $pos++;
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        <?php endwhile; ?>

        <div class="legenda">
            <h4>Legenda</h4>
            <p><strong>PG</strong> = Partite Giocate · <strong>V</strong> = Vinte · <strong>P</strong> = Pareggiate · <strong>S</strong> = Sconfitte</p>
            <p><strong>GF</strong> = Gol/Punti Fatti · <strong>GS</strong> = Gol/Punti Subiti · <strong>DR</strong> = Differenza Reti</p>
            <p>Vittoria = 3 punti · Pareggio = 1 punto · Sconfitta = 0 punti</p>
        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>