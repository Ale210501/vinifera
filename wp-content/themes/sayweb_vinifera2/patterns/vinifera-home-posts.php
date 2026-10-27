<?php
/**
 * Title: Vinifera Home Posts
 * Slug: sayweb-vinifera/vinifera-home-posts
 * Categories: sayweb-vinifera
 * Inserter: true
 */
?>
<!-- wp:group {"align":"full","className":"vinifera-home-posts","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull vinifera-home-posts">
	<!-- wp:heading {"textAlign":"center","level":2,"className":"vinifera-section-title"} -->
	<h2 class="wp-block-heading has-text-align-center vinifera-section-title">Ultimi articoli</h2>
	<!-- /wp:heading -->

	<!-- wp:query {"className":"vinifera-post-query","query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"constrained"}} -->
	<div class="wp-block-query vinifera-post-query">
		<!-- wp:post-template {"className":"vinifera-post-grid","layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:group {"className":"vinifera-post-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group vinifera-post-card">
				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
				<!-- wp:post-title {"isLink":true,"level":3} /-->
				<!-- wp:post-excerpt {"excerptLength":18,"moreText":"Leggi"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Nessun contenuto disponibile.</p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
