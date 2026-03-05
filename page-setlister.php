<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}
/**
 * Template Name: Aktive setlister
 */
get_header();
?>

<main class="site-main">

    <!-- FAST CONTENT-WRAPPER -->
    <div class="cc-content">

        <h1><?php the_title(); ?></h1>

        <?php
        $today = date('Ymd');

        $args = [
            'post_type'      => 'setliste',
            'posts_per_page' => -1,
            'meta_key'       => 'koncertdato',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => 'koncertdato',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                ],
            ],
        ];

        $setlister = new WP_Query($args);
        ?>

        <?php if ($setlister->have_posts()): ?>
            <?php while ($setlister->have_posts()): $setlister->the_post(); ?>

                <article class="setliste-oversigt-item">

                    <h3>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <?php if ($dato = get_field('koncertdato')): ?>
                        <p class="setliste-dato">
    <i class="fa-solid fa-calendar" aria-hidden="true"></i>
    <?php echo date_i18n('j. F Y', strtotime($dato)); ?>
</p>
                    <?php endif; ?>

                    <?php
                    // Beskrivelse
                    if ($desc = get_field('beskrivelse')): ?>
                        <div class="setliste-beskrivelse">
                            <?php echo wp_kses_post($desc); ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Beregn samlet spilletid
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

                    $total_minutes = floor($total_seconds / 60);
                    $total_secs    = $total_seconds % 60;
                    ?>

                    <?php if ($total_seconds > 0): ?>
                        <p class="setliste-total-tid">
                            <strong>Ca. spilletid:</strong>
                            <?php echo $total_minutes; ?> min
                            <?php echo str_pad($total_secs, 2, '0', STR_PAD_LEFT); ?> sek
                        </p>
                    <?php endif; ?>

                    <?php if ($noder && is_array($noder)): ?>
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
                                           title="Download node (PDF)">
                                            <i class="fa-regular fa-file-pdf" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo get_permalink($node->ID); ?>">
                                        <?php echo esc_html(get_the_title($node->ID)); ?>
                                    </a>
                                </li>

                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>

                </article>

            <?php endwhile; ?>
        <?php else: ?>
            <p>Der er endnu ingen aktive setlister.</p>
        <?php endif; ?>
		
		<p class="setliste-arkiv-link">
            <a href="<?php echo esc_url( get_permalink( get_page_by_path('saetlistearkiv') ) ); ?>">
                Se tidligere setlister
            </a>
        </p>

    </div>
    <!-- /cc-content -->

</main>

<?php
wp_reset_postdata();
get_footer();
