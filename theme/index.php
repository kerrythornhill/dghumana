<?php get_header(); ?>
<main id="main" class="wrap section legacy-content"><p class="eyebrow">Writing & updates</p><h1><?php echo esc_html(is_search() ? 'Search results' : 'Writing'); ?></h1>
<?php if (have_posts()) { while (have_posts()) { the_post(); ?><article class="section"><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article><?php } the_posts_pagination(); } else { ?><p>No entries are available here yet.</p><?php } ?></main>
<?php get_footer(); ?>
