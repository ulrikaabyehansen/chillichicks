<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}
get_header();
?>

<main class="site-main">
    <div class="cc-content">

        <article class="node-single">

            <h1><?php the_title(); ?></h1>

            <?php
            // =========================
            // MATERIALER (KORREKTE FELTER)
            // =========================
            $pdf_node     = get_field('pdf_node');
            $sibelius_fil = get_field('sibelius_fil');
            $youtube      = get_field('youtube');

            // Normalisér PDF
            $pdf_url = '';
            if ($pdf_node) {
                $pdf_url = is_array($pdf_node) && isset($pdf_node['url'])
                    ? $pdf_node['url']
                    : (is_numeric($pdf_node) ? wp_get_attachment_url($pdf_node) : $pdf_node);
            }

            // Normalisér Sibelius
            $sib_url = '';
            if ($sibelius_fil) {
                $sib_url = is_array($sibelius_fil) && isset($sibelius_fil['url'])
                    ? $sibelius_fil['url']
                    : (is_numeric($sibelius_fil) ? wp_get_attachment_url($sibelius_fil) : $sibelius_fil);
            }
            ?>

            <?php if ($pdf_url || $sib_url || $youtube): ?>
                <div class="node-materialer">

                    <?php if ($pdf_url): ?>
                        <p>
                            <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener">
                                <i class="fa-regular fa-file-pdf cc-icon cc-icon--pdf"></i>
                                Download node (PDF)
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if ($sib_url): ?>
                        <p>
                            <a href="<?php echo esc_url($sib_url); ?>" download>
                                <i class="fa-solid fa-music cc-icon cc-icon--sibelius"></i>
                                Download Sibelius-fil
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if ($youtube): ?>
                        <p>
                            <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener">
                                <i class="fa-brands fa-youtube cc-icon cc-icon--youtube"></i>
                                Se nummeret på YouTube
                            </a>
                        </p>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <!-- =========================
                 META DATA
                 ========================= -->
           <table class="node-meta-table node-table cc-card cc-zebra">
    <thead>
        <tr>
            <th colspan="2" scope="colgroup">
                <?php the_title(); ?>
            </th>
        </tr>
    </thead>
    <tbody>

        <?php if ($v = get_field('komponist')): ?>
            <tr>
                <th scope="row">Komponist</th>
                <td><?php echo esc_html($v); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($v = get_field('arrangor')): ?>
            <tr>
                <th scope="row">Arrangør</th>
                <td><?php echo esc_html($v); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($v = get_field('laengde')): ?>
            <tr>
                <th scope="row">Længde</th>
                <td><?php echo esc_html($v); ?></td>
            </tr>
        <?php endif; ?>

        <?php
        $genre = get_field('genre');
        if (is_array($genre)) {
            $genre = implode(', ', array_filter($genre));
        }
        if ($genre): ?>
            <tr>
                <th scope="row">Genre</th>
                <td><?php echo esc_html($genre); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($v = get_field('svaerhedsgrad')): ?>
            <tr>
                <th scope="row">Sværhedsgrad</th>
                <td><?php echo esc_html($v); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($v = get_field('stemmer')): ?>
            <tr>
                <th scope="row">Stemmer</th>
                <td><?php echo esc_html($v); ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <th scope="row">Sidst opdateret</th>
            <td><?php echo esc_html( cc_relative_modified_date() ); ?></td>
        </tr>

    </tbody>
</table>


            <!-- =========================
                 ØVEFILER
                 ========================= -->
            <?php
            $files = [
				'backtrack'          => 'Backtrack',
                'ovefil_alle_stemmer' => 'Alle stemmer',
                'sopran_ovefil'       => 'Sopran',
                'mezzo_ovefil'        => 'Mezzo',
                'alt_ovefil'          => 'Alt',
                '1_sopran_ovefil'     => '1. Sopran',
                '2_sopran_ovefil'     => '2. Sopran',
                '1_alt_ovefil'        => '1. Alt',
                '2_alt_ovefil'        => '2. Alt',
            ];

            $has_audio = false;
            foreach ($files as $field => $label) {
                if (get_field($field)) { $has_audio = true; break; }
            }
            ?>

           <?php if ($has_audio): ?>
 

  <?php if ($has_audio): ?>
    <h2>Øvefiler</h2>

    <table class="node-audio-table cc-card cc-zebra">
        <thead>
            <tr>
                <th scope="col">Stemme</th>
                <th scope="col">Afspil</th>
                <th scope="col">Download</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($files as $field => $label):
            $file = get_field($field);
            if (!$file) continue;

            $url = is_array($file) && isset($file['url'])
                ? $file['url']
                : (is_numeric($file) ? wp_get_attachment_url($file) : $file);
            if (!$url) continue;
        ?>
            <tr>
                <td><?php echo esc_html($label); ?></td>

                <td>
                    <audio controls src="<?php echo esc_url($url); ?>"></audio>
                </td>

                <td>
                    <a href="<?php echo esc_url($url); ?>"
                       download
                       aria-label="Download øvefil">
                        <i class="fa-solid fa-download"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
<?php endif; ?>

<?php endif; ?>

			
<?php /*
<!-- =========================
     MULTITRACK PLAYER – TEST
     ========================= -->
<div id="cc-multitrack-test"
	 
	 
     data-stemmer="<?php echo esc_attr( get_field('stemmer') ); ?>"

     data-backtrack="<?php echo esc_url( cc_get_file_url( get_field('backtrack') ) ); ?>"

     data-sopran="<?php echo esc_url( cc_get_file_url( get_field('sopran_ovefil') ) ); ?>"
     data-mezzo="<?php echo esc_url( cc_get_file_url( get_field('mezzo_ovefil') ) ); ?>"
     data-alt="<?php echo esc_url( cc_get_file_url( get_field('alt_ovefil') ) ); ?>"

     data-1sopran="<?php echo esc_url( cc_get_file_url( get_field('1_sopran_ovefil') ) ); ?>"
     data-2sopran="<?php echo esc_url( cc_get_file_url( get_field('2_sopran_ovefil') ) ); ?>"
     data-1alt="<?php echo esc_url( cc_get_file_url( get_field('1_alt_ovefil') ) ); ?>"
     data-2alt="<?php echo esc_url( cc_get_file_url( get_field('2_alt_ovefil') ) ); ?>">
</div>

<div class="cc-multitrack-ui">
    <button id="cc-play">▶︎ Play / Pause</button>

    <div id="cc-tracks"></div>
</div>

*/ ?>


            <?php if ($v = get_field('noter')): ?>
                <h2>Noter</h2>
                <p><?php echo nl2br(esc_html($v)); ?></p>
            <?php endif; ?>
			

        </article>

    </div>
</main>

<?php get_footer(); ?>
