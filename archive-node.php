<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}
get_header();

/*
 * Klikbar sortering (server-side)
 */
$orderby = $_GET['orderby'] ?? 'title';
$order   = $_GET['order'] ?? 'asc';

$allowed_orderby = ['title', 'stemmer', 'svaerhedsgrad', 'updated'];
$allowed_order   = ['asc', 'desc'];

if (!in_array($orderby, $allowed_orderby, true)) {
    $orderby = 'title';
}
if (!in_array($order, $allowed_order, true)) {
    $order = 'asc';
}

$args = [
    'post_type'      => 'node',
    'posts_per_page' => -1,
    'order'          => strtoupper($order),
];

if ($orderby === 'title') {
    $args['orderby'] = 'title';

} elseif ($orderby === 'updated') {
    $args['orderby'] = 'modified';

} else {
    $args['meta_key'] = $orderby;
    $args['orderby']  = 'meta_value';
}

$node_query = new WP_Query($args);
?>

<main class="site-main">
    <article class="inside-article cc-content">

        <h1>Nodearkiv</h1>

        <p>
            Her er et samlet arkiv over de noder der bliver aktivt brugt i koret.
            Du kan sortere efter titel, stemmer og sværhedsgrad ved at klikke på kolonnerne.
            Et ekstra klik vender rækkefølgen.
        </p>

        <p>
            Klik på PDF-ikonet for at downloade noden, eller på titlen for at se detaljer
            og eventuelle øvefiler.
        </p>

        <p>
            Arkivet er ikke 100 % opdateret – enkelte aktive numre mangler stadig.
        </p>

        <!-- Live søgning -->
        <form class="node-search">
            <input
                type="search"
                id="nodeSearch"
                placeholder="Søg i noder…"
            />
        </form>

        <table class="node-table cc-table cc-card cc-zebra">
            <thead>
                <tr>
                    <th>
                        <a href="?orderby=title&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">
                            Titel
                        </a>
                    </th>
                    <th>
                        <a href="?orderby=stemmer&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">
                            Stemmer
                        </a>
                    </th>
                    <th>Genre</th>
                    <th>
                        <a href="?orderby=svaerhedsgrad&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">
                            Sværhedsgrad
                        </a>
                    </th>
                    <th>Materiale</th>
                    <th>
                        <a href="?orderby=updated&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">
                            Opdateret
                        </a>
                    </th>
                </tr>
            </thead>

            <tbody>
            <?php if ($node_query->have_posts()): ?>
                <?php while ($node_query->have_posts()): $node_query->the_post(); ?>
                    <tr>
                        <td>
                            <?php
                            $pdf = get_field('pdf_node');
                            if ($pdf) {
                                $pdf_url = is_array($pdf) && isset($pdf['url'])
                                    ? $pdf['url']
                                    : (is_numeric($pdf) ? wp_get_attachment_url($pdf) : $pdf);
                                ?>
                                <a href="<?php echo esc_url($pdf_url); ?>"
                                   download
                                   title="Download node (PDF)">
                                    <i class="fa-regular fa-file-pdf cc-icon cc-icon--pdf" aria-hidden="true"></i>
                                </a>
                            <?php } ?>

                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </td>

                        <td><?php echo esc_html(get_field('stemmer')); ?></td>

                        <td>
                            <?php
                            $genre = get_field('genre');
                            echo esc_html(is_array($genre) ? implode(', ', $genre) : $genre);
                            ?>
                        </td>

                        <td><?php echo esc_html(get_field('svaerhedsgrad')); ?></td>

                        <td>
                            <?php
                            $audio_fields = [
                                'ovefil_alle_stemmer',
                                'sopran_ovefil',
                                'mezzo_ovefil',
                                'alt_ovefil',
                                '1_sopran_ovefil',
                                '2_sopran_ovefil',
                                '1_alt_ovefil',
                                '2_alt_ovefil',
                            ];

                            foreach ($audio_fields as $field) {
                                if (get_field($field)) {
                                    echo '<a href="' . esc_url(get_permalink()) . '" aria-label="Øvefiler">';
                                    echo '<i class="fa-solid fa-headphones cc-icon cc-icon--audio"></i>';
                                    echo '</a>';
                                    break;
                                }
                            }

                            if ($youtube = get_field('youtube')) {
                                echo '<a href="' . esc_url($youtube) . '" target="_blank" rel="noopener">';
                                echo '<i class="fa-brands fa-youtube cc-icon cc-icon--youtube"></i>';
                                echo '</a>';
                            }

                            if ($sib = get_field('sibelius_fil')) {
                                $sib_url = is_array($sib) && isset($sib['url'])
                                    ? $sib['url']
                                    : (is_numeric($sib) ? wp_get_attachment_url($sib) : $sib);

                                if ($sib_url) {
                                    echo '<a href="' . esc_url($sib_url) . '" download aria-label="Download Sibelius-fil">';
                                    echo '<i class="fa-solid fa-music cc-icon"></i>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </td>

                        <td>
                            <?php echo esc_html( cc_relative_modified_archive() ); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Ingen noder fundet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

    </article>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('nodeSearch');
    const rows  = document.querySelectorAll('.node-table tbody tr');

    input.addEventListener('input', function () {
        const query = input.value.toLowerCase();

        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
        });
    });
});
</script>

<?php
wp_reset_postdata();
get_footer();
