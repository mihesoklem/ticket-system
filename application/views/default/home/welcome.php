<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="response-message no-radius no-mb"><?php echo alert_message(); ?></div>

<!-- Hero: -->
<div class="bg-white z-hero-wrapper">
  <div class="z-hero kb-header container py-5">
    <div class="row">
      <div class="col-md-7 align-self-center search-form-wrapper">
        <h3 class="h1 fw-bold"><?php echo lang( 'search_the_solution' ); ?></h3>
        <p><?php echo lang( 'search_tagline' ); ?></p>
        <form class="search-form" action="<?php echo env_url( 'search' ); ?>">
          <div class="input-group align-items-center">
            <input type="search" class="form-control" name="query" placeholder="<?php echo lang( 'search_articles' ); ?>" required>
            <button class="btn btn-sub btn-wide"><i class="fas fa-search"></i></button>
          </div>
          <!-- /.input-group -->
        </form>
        <?php if ( ! empty( $popular_topics ) ) { ?>
          <p class="mt-2 mb-0"><strong><?php echo lang( 'popular_tags' ); ?>:</strong>
          <?php
            $count = sizeof( $popular_topics );
            $i = 1;
            foreach ( $popular_topics as $popular_topic ) { ?>
              <a class="text-z" href="<?php echo env_url( get_kb_category_slug( html_escape( $popular_topic->slug ) ) ); ?>"><?php echo html_escape( $popular_topic->name ); ?></a><?php echo ( $i != $count ) ? ',' : ''; ?>
          <?php $i++; } ?>
          </p>
        <?php } ?>
      </div>
      <!-- /col -->
      <div class="col-md-5">
        <img class="img-fluid d-none d-md-block" src="<?php illustration_by_color( 'search' ); ?>" alt="">
      </div>
      <!-- /col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container -->
</div>
<!-- /.z-hero-wrapper -->

<!-- Articles: -->
<?php if ( ! empty( $categories = get_articles_categories() ) ) {
  foreach ( $categories as $category ) { ?>
  <div class="z-posts container my-5">
    <div class="row mb-3">
      <div class="col">
        <h2 class="h4 fw-bold border-bottom pb-2">
          <a href="<?php echo env_url( get_kb_category_slug( html_escape( $category->slug ) ) ); ?>"><?php echo html_escape( $category->name ); ?></a>
        </h2>
      </div>
      <!-- /col -->
    </div>
    <!-- /.row -->
    <?php if ( ! empty( $child_categories = get_articles_subcategories( $category->id ) ) ) { ?>
      <div class="row row-main">
        <?php foreach ( $child_categories as $child_category ) { ?>
          <div class="col-xl-4 col-lg-6">
            <h3 class="h5 mb-4 fw-bold border-bottom pb-2">
              <a href="<?php echo env_url( html_escape( get_kb_category_slug( $category->slug, $child_category->slug ) ) ); ?>">
                <?php echo html_escape( $child_category->name ); ?>
                (<?php echo get_articles_by_category( $child_category->id, true ); ?>)
              </a>
            </h3>
            <?php if ( ! empty( $articles = get_articles_by_category( $child_category->id ) ) ) { ?>
              <ul class="nav flex-column z-kb-list">
                <?php foreach ( $articles as $article ) { ?>
                  <li>
                    <a href="<?php echo env_url( get_kb_article_slug( html_escape( $article->slug ) ) ); ?>">
                      <i class="far fa-file-alt me-2"></i> <?php echo html_escape( $article->title ); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            <?php } ?>
          </div>
          <!-- /col -->
        <?php } ?>
      </div>
      <!-- /.row -->
    <?php } ?>
  </div>
  <!-- /.container -->
<?php }
} else { ?>
  <div class="z-list container my-5">
    <div class="shadow-sm">
      <div class="row">
        <div class="col">
          <div class="list-item">
            <div class="row">
              <div class="col">
                <div class="text-center">
                  <img class="not-found mt-2 mb-4" src="<?php illustration_by_color( 'not_found' ); ?>" alt="">
                  <h2 class="h4 fw-bold"><?php echo lang( 'no_records_found' ); ?></h2>
                </div>
              </div>
              <!-- /col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.list-item -->
        </div>
        <!-- /col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.shadow-sm -->
  </div>
  <!-- /.z-list -->
<?php } ?>

<?php load_view( 'home/still_no_luck' ); ?>