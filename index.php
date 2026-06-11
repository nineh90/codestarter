<?php
// =====================================================================
// index.php — Das Dashboard: Begrüßung, Level-Fortschritt und alle
// Lektionen als Karten mit Fortschrittsanzeige.
// =====================================================================

require_once __DIR__ . '/config.php';

$db = get_db();
$user_id = current_user_id($db);
$total_xp = get_total_xp($db, $user_id);
$completed = get_completed_task_ids($db, $user_id);
$stats = xp_stats($total_xp);
$lessons = get_all_lessons();

render_page_start('Dashboard', '', $total_xp);
?>
<main class="container">

    <section class="hero">
        <h1>Hey Jonas! 👋</h1>
        <?php if ($total_xp === 0): ?>
            <p>Bereit? Such dir unten eine Lektion aus und sammel deine ersten XP.</p>
        <?php elseif ($stats['xp_to_next'] > 0): ?>
            <p>Du bist <strong>Level <?= $stats['level'] ?> — <?= h($stats['name']) ?></strong>.
               Noch <strong><?= $stats['xp_to_next'] ?> XP</strong> bis Level <?= $stats['level'] + 1 ?> (<?= h((string) $stats['next_level_name']) ?>)!</p>
        <?php else: ?>
            <p>Level <?= $stats['level'] ?> — <?= h($stats['name']) ?>. Du hast alles erreicht. Respekt! 🏆</p>
        <?php endif; ?>

        <div class="hero-bar">
            <!-- width ist dynamisch, deshalb ausnahmsweise inline -->
            <span class="hero-bar-fill" style="width: <?= $stats['percent'] ?>%"></span>
        </div>
    </section>

    <section class="lesson-grid">
        <?php foreach ($lessons as $lesson): ?>
            <?php
            // Fortschritt dieser Lektion ausrechnen
            $task_ids = array_column($lesson['tasks'], 'id');
            $total_tasks = count($task_ids);
            $done_tasks = count(array_intersect($task_ids, $completed));
            $lesson_xp = array_sum(array_column($lesson['tasks'], 'xp'));

            if ($done_tasks === $total_tasks) {
                $button_text = 'Nochmal ansehen';
            } elseif ($done_tasks > 0) {
                $button_text = 'Weitermachen';
            } else {
                $button_text = 'Loslegen';
            }
            ?>
            <article class="lesson-card<?= $done_tasks === $total_tasks ? ' complete' : '' ?>">
                <div class="lesson-card-icon"><?= h($lesson['icon']) ?></div>
                <h2><?= h($lesson['title']) ?></h2>
                <p><?= h($lesson['description']) ?></p>
                <p class="lesson-card-meta">
                    <?= $done_tasks ?>/<?= $total_tasks ?> Aufgaben
                    · <?= $lesson_xp ?> XP möglich
                    <?= $done_tasks === $total_tasks ? ' · fertig 🎉' : '' ?>
                </p>
                <div class="card-bar">
                    <!-- width ist dynamisch, deshalb ausnahmsweise inline -->
                    <span class="card-bar-fill" style="width: <?= $total_tasks > 0 ? (int) round($done_tasks / $total_tasks * 100) : 0 ?>%"></span>
                </div>
                <a class="btn" href="lessons/<?= h($lesson['id']) ?>.php"><?= h($button_text) ?></a>
            </article>
        <?php endforeach; ?>
    </section>

</main>
<?php render_page_end(''); ?>
