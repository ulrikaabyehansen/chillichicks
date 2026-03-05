<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

get_header();
?>

<main class="site-main">

    <div class="cc-content">

        <article class="setliste-single">

            <h1><?php the_title(); ?></h1>

            <?php if ($dato = get_field('koncertdato')): ?>
                <span class="setliste-dato-badge">
                    <i class="fa-solid fa-calendar" aria-hidden="true"></i>
                    <?php echo date_i18n('j. F Y', strtotime($dato)); ?>
                </span>
            <?php endif; ?>

            <?php
            /* ===== BESKRIVELSE ===== */
            if ($desc = get_field('beskrivelse')): ?>
                <div class="setliste-beskrivelse">
                    <?php echo wp_kses_post($desc); ?>
                </div>
            <?php endif; ?>

            <?php
            /* ===== BEREGN SPILLETID ===== */
            $total_seconds = 0;
            $noder = get_field('noder');

            if ($noder && is_array($noder)) {
                foreach ($noder as $node) {
                    $length = get_field('laengde', $node->ID);
                    if ($length && preg_match('/(\d+):(\d+)/', $length, $m)) {
                        $total_seconds += ((int)$m[1] * 60) + (int)$m[2];
                    }
                }
            }

            if ($total_seconds > 0):
                $total_minutes = floor($total_seconds / 60);
                $total_secs    = $total_seconds % 60;
            ?>
                <p class="setliste-total-tid">
                    <strong>Ca. spilletid:</strong>
                    <?php echo $total_minutes; ?> min
                    <?php echo str_pad($total_secs, 2, '0', STR_PAD_LEFT); ?> sek
                </p>
            <?php endif; ?>

            <?php
            /* ===== NODER ===== */
            if ($noder && is_array($noder)): ?>
                <ol class="setliste-noder">
                    <?php foreach ($noder as $node): ?>

                        <?php
                        $pdf = get_field('pdf_node', $node->ID);
                        $pdf_url = '';

                        if ($pdf) {
                            $pdf_url = is_array($pdf) && isset($pdf['url'])
                                ? $pdf['url']
                                : (is_numeric($pdf) ? wp_get_attachment_url($pdf) : $pdf);
                        }
                        ?>

                        <li>
                            <?php if ($pdf_url): ?>
                                <a href="<?php echo esc_url($pdf_url); ?>"
                                   download
                                   title="Download node (PDF)"
                                   aria-label="Download node (PDF)">
                                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo get_permalink($node->ID); ?>">
                                <?php echo esc_html(get_the_title($node->ID)); ?>
                            </a>
                        </li>

                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p>Der er endnu ikke tilføjet noder til denne setliste.</p>
            <?php endif; ?>

        </article>

    </div>

</main>

<?php get_footer(); ?>
