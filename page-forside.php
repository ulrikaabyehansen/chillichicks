<?php
/**
 * Template Name: Forside
 */
get_header();
?>

<main class="site-main">

    <div class="cc-content">

        <article class="forside">

            <h1>Velkommen til koret</h1>

            <p>
                Denne side er korets interne platform til brug for medlemmer.
                Her deles setlister, noder og praktisk information i forbindelse
                med koncerter og øvning.
            </p>

            <p>
                Er du interesseret i koret, kan du følge os på Facebook:
            </p>

            <p class="facebook-link">
                <a href="https://www.facebook.com/chillichickslyngby"
                   target="_blank"
                   rel="noopener">
                    Chillichicks Lyngby på Facebook
                </a>
            </p>

            <hr>

            <?php if ( is_user_logged_in() ): ?>

                <p>Du er logget ind.</p>

                <p>
                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
                        Log ud
                    </a>
                </p>

            <?php else: ?>

                <h2>Medlemslogin</h2>

                <p>Kun for korets medlemmer.</p>

                <?php
                wp_login_form([
                    'redirect'       => home_url(),
                    'label_username' => 'Brugernavn',
                    'label_password' => 'Adgangskode',
                    'label_log_in'   => 'Log ind',
                ]);
                ?>

            <?php endif; ?>

        </article>

    </div>

</main>

<?php
get_footer();
