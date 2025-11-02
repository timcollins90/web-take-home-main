<?php
/**
 * Render callback for Company List block.
 */

$selected_companies = $attributes['selectedCompanies'] ?? [];
$title = $attributes['title'] ?? '';

if (empty($selected_companies)) {
	return ''; // no output
}

$companies = get_posts([
	'post_type'      => 'company',
	'post__in'       => $selected_companies,
	'orderby'        => 'post__in',
	'posts_per_page' => count($selected_companies),
]);

if (empty($companies)) {
	return '';
}

echo '<div class="widget-company-list">';

if ($title) {
	echo '<h3 class="company-list-title">' . esc_html($title) . '</h3>';
}

echo '<div class="company-cards">';

foreach ($companies as $company) {
	$company_id     = $company->ID;
	$rating         = get_post_meta($company_id, '_company_rating', true);
	$has_free_trial = get_post_meta($company_id, '_company_has_free_trial', true) === '1';
	$benefits       = get_post_meta($company_id, '_company_benefits', true) ?: [];
	$cons           = get_post_meta($company_id, '_company_cons', true) ?: [];
	$summary        = !empty($company->post_excerpt) ? $company->post_excerpt : wp_trim_words($company->post_content, 25);
	$logo_url       = get_the_post_thumbnail_url($company_id, 'medium');
	$permalink      = get_permalink($company_id);

	echo '<div class="company-card">';

	if ($logo_url) {
		echo '<a href="' . esc_url($permalink) . '"><img class="company-logo" src="' . esc_url($logo_url) . '" alt="' . esc_attr($company->post_title) . '"></a>';
	}

	echo '<h4 class="company-title">';
	echo '<a href="' . esc_url($permalink) . '">' . esc_html($company->post_title) . '</a>';

	if ($has_free_trial) {
		echo '<span class="free-trial-badge">Free Trial</span>';
	}
	echo '</h4>';

	if ($rating) {
		echo '<p class="company-rating">⭐ ' . esc_html($rating) . '/10</p>';
	}

	if ($summary) {
		echo '<p class="company-summary">' . esc_html($summary) . '</p>';
	}

	if (!empty($benefits)) {
		echo '<div class="company-benefits"><strong>Benefits:</strong><ul>';
		foreach ($benefits as $benefit) {
			echo '<li>' . esc_html($benefit) . '</li>';
		}
		echo '</ul></div>';
	}

	if (!empty($cons)) {
		echo '<div class="company-cons"><strong>Cons:</strong><ul>';
		foreach ($cons as $con) {
			echo '<li>' . esc_html($con) . '</li>';
		}
		echo '</ul></div>';
	}

	echo '</div>'; // end card
}

echo '</div>'; // end cards
echo '</div>'; // end list wrapper