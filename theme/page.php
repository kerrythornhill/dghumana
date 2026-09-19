<?php get_header(); $key = humana_key();
if ($key) { humana_render($key); } else { ?>
<main id="main" class="wrap section legacy-content"><?php while (have_posts()) { the_post(); ?><h1><?php the_title(); ?></h1><div class="prose"><?php the_content(); ?></div><?php } ?></main>
<?php } get_footer(); ?>
