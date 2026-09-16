<?php
/**
 * Plugin Name: Presslyte AI Search Check
 * Description: Review one WordPress page for clear, useful, AI-search-ready content without changing the page.
 * Version: 0.1.3
 * Author: Presslyte
 * Author URI: https://presslyte.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: presslyte-ai-search-check
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Presslyte_AI_Search_Check {
	private const VERSION = '0.1.3';
	private static $admin_page_hook = '';

	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_button' ), 90 );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'add_plugin_action_link' ) );
	}

	public static function register_admin_page() {
		self::$admin_page_hook = add_menu_page(
			esc_html__( 'Presslyte AI Search Check', 'presslyte-ai-search-check' ),
			esc_html__( 'AI Search Check', 'presslyte-ai-search-check' ),
			'edit_posts',
			'presslyte-ai-search-check',
			array( __CLASS__, 'render_admin_page' ),
			plugins_url( 'assets/images/icon-128x128.png', __FILE__ ),
			81
		);
	}

	public static function enqueue_admin_assets( $hook_suffix ) {
		wp_add_inline_style(
			'common',
			'#adminmenu .toplevel_page_presslyte-ai-search-check .wp-menu-image img{box-sizing:content-box!important;width:20px!important;height:20px!important;padding:7px 0 0!important;object-fit:contain}'
		);

		if ( ! self::$admin_page_hook || self::$admin_page_hook !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'presslyte-ai-search-check-admin',
			plugins_url( 'assets/css/admin.css', __FILE__ ),
			array(),
			self::VERSION
		);
	}

	public static function add_plugin_action_link( $links ) {
		$dashboard_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=presslyte-ai-search-check' ) ),
			esc_html__( 'Dashboard', 'presslyte-ai-search-check' )
		);

		array_unshift( $links, $dashboard_link );

		return $links;
	}

	public static function render_admin_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'presslyte-ai-search-check' ) );
		}
		?>
		<div class="wrap presslyte-aisc-admin">
			<div class="presslyte-aisc-admin-shell">
				<section class="presslyte-aisc-admin-hero" aria-labelledby="presslyte-aisc-admin-title">
					<div class="presslyte-aisc-admin-brand">
						<img
							class="presslyte-aisc-admin-logo"
							src="<?php echo esc_url( plugins_url( 'assets/images/icon-128x128.png', __FILE__ ) ); ?>"
							width="64"
							height="64"
							alt=""
						/>
						<div>
							<p class="presslyte-aisc-admin-eyebrow"><?php esc_html_e( 'Presslyte', 'presslyte-ai-search-check' ); ?></p>
							<h1 id="presslyte-aisc-admin-title"><?php esc_html_e( 'AI Search Check', 'presslyte-ai-search-check' ); ?></h1>
						</div>
					</div>

					<p class="presslyte-aisc-admin-lead">
						<?php esc_html_e( 'Review the page you are viewing for clear structure, useful content, and common search-readiness issues—without changing or sending your content anywhere.', 'presslyte-ai-search-check' ); ?>
					</p>

					<div class="presslyte-aisc-admin-actions">
						<a class="presslyte-aisc-admin-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Open site', 'presslyte-ai-search-check' ); ?>
							<span aria-hidden="true">&#8599;</span>
							<span class="screen-reader-text"><?php esc_html_e( 'Opens in a new tab', 'presslyte-ai-search-check' ); ?></span>
						</a>
						<span class="presslyte-aisc-admin-status">
							<span aria-hidden="true"></span>
							<?php esc_html_e( 'Ready to use', 'presslyte-ai-search-check' ); ?>
						</span>
					</div>
				</section>

				<div class="presslyte-aisc-admin-grid">
					<section class="presslyte-aisc-admin-card" aria-labelledby="presslyte-aisc-steps-title">
						<p class="presslyte-aisc-admin-kicker"><?php esc_html_e( 'Quick start', 'presslyte-ai-search-check' ); ?></p>
						<h2 id="presslyte-aisc-steps-title"><?php esc_html_e( 'Three steps. No setup.', 'presslyte-ai-search-check' ); ?></h2>
						<ol class="presslyte-aisc-admin-steps">
							<li><span>1</span><div><strong><?php esc_html_e( 'Open a page or post', 'presslyte-ai-search-check' ); ?></strong><p><?php esc_html_e( 'View any singular item that you have permission to edit.', 'presslyte-ai-search-check' ); ?></p></div></li>
							<li><span>2</span><div><strong><?php esc_html_e( 'Select AI Search Check', 'presslyte-ai-search-check' ); ?></strong><p><?php esc_html_e( 'Use the Presslyte icon in the front-end admin bar.', 'presslyte-ai-search-check' ); ?></p></div></li>
							<li><span>3</span><div><strong><?php esc_html_e( 'Review every finding', 'presslyte-ai-search-check' ); ?></strong><p><?php esc_html_e( 'Select an actionable result to highlight it on the page.', 'presslyte-ai-search-check' ); ?></p></div></li>
						</ol>
					</section>

					<aside class="presslyte-aisc-admin-card presslyte-aisc-admin-assurance" aria-labelledby="presslyte-aisc-assurance-title">
						<p class="presslyte-aisc-admin-kicker"><?php esc_html_e( 'Private by design', 'presslyte-ai-search-check' ); ?></p>
						<h2 id="presslyte-aisc-assurance-title"><?php esc_html_e( 'Your content stays here.', 'presslyte-ai-search-check' ); ?></h2>
						<ul>
							<li><?php esc_html_e( 'Runs locally in your browser', 'presslyte-ai-search-check' ); ?></li>
							<li><?php esc_html_e( 'Makes no automatic external requests', 'presslyte-ai-search-check' ); ?></li>
							<li><?php esc_html_e( 'Stores no content or results', 'presslyte-ai-search-check' ); ?></li>
							<li><?php esc_html_e( 'Changes nothing on the page', 'presslyte-ai-search-check' ); ?></li>
						</ul>
					</aside>
				</div>

				<section class="presslyte-aisc-admin-checks" aria-labelledby="presslyte-aisc-checks-title">
					<div>
						<p class="presslyte-aisc-admin-kicker"><?php esc_html_e( 'Included checks', 'presslyte-ai-search-check' ); ?></p>
						<h2 id="presslyte-aisc-checks-title"><?php esc_html_e( 'Focused signals, clearly explained.', 'presslyte-ai-search-check' ); ?></h2>
					</div>
					<ul>
						<li><?php esc_html_e( 'HTML noindex', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Heading structure', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Image alt text', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Failed images', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Descriptive links', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Visible placeholders', 'presslyte-ai-search-check' ); ?></li>
						<li><?php esc_html_e( 'Long paragraphs', 'presslyte-ai-search-check' ); ?></li>
					</ul>
				</section>

				<section class="presslyte-aisc-admin-about" aria-labelledby="presslyte-aisc-about-title">
					<div>
						<p class="presslyte-aisc-admin-kicker"><?php esc_html_e( 'About the company', 'presslyte-ai-search-check' ); ?></p>
						<h2 id="presslyte-aisc-about-title"><?php esc_html_e( 'Meet Presslyte', 'presslyte-ai-search-check' ); ?></h2>
						<p><?php esc_html_e( 'Presslyte is a commercial front-end WordPress content editor built for supported content changes on existing websites.', 'presslyte-ai-search-check' ); ?></p>
					</div>
					<nav class="presslyte-aisc-admin-resources" aria-label="<?php esc_attr_e( 'Presslyte resources', 'presslyte-ai-search-check' ); ?>">
						<a href="https://presslyte.com/documentation/" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Documentation', 'presslyte-ai-search-check' ); ?><span aria-hidden="true">&#8599;</span><span class="screen-reader-text"><?php esc_html_e( 'Opens in a new tab', 'presslyte-ai-search-check' ); ?></span>
						</a>
						<a href="https://presslyte.com/support/" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Support', 'presslyte-ai-search-check' ); ?><span aria-hidden="true">&#8599;</span><span class="screen-reader-text"><?php esc_html_e( 'Opens in a new tab', 'presslyte-ai-search-check' ); ?></span>
						</a>
						<a class="presslyte-aisc-admin-resource-primary" href="https://presslyte.com/pricing/" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Explore Presslyte plans', 'presslyte-ai-search-check' ); ?><span aria-hidden="true">&#8599;</span><span class="screen-reader-text"><?php esc_html_e( 'Opens in a new tab', 'presslyte-ai-search-check' ); ?></span>
						</a>
					</nav>
				</section>
			</div>
		</div>
		<?php
	}

	public static function add_admin_bar_button( $admin_bar ) {
		if ( ! self::can_check_current_page() ) {
			return;
		}

		$admin_bar->add_node(
			array(
				'id'    => 'presslyte-ai-search-check',
				'title' => esc_html__( 'AI Search Check', 'presslyte-ai-search-check' ),
				'href'  => '#presslyte-ai-search-check-panel',
				'meta'  => array(
					'title' => esc_attr__( 'Check this page with Presslyte AI Search Check', 'presslyte-ai-search-check' ),
				),
			)
		);
	}

	public static function enqueue_assets() {
		if ( ! self::can_check_current_page() ) {
			return;
		}

		$post_id  = get_queried_object_id();
		$edit_url = get_edit_post_link( $post_id, 'raw' );

		wp_enqueue_style(
			'presslyte-ai-search-check',
			plugins_url( 'assets/css/check.css', __FILE__ ),
			array(),
			self::VERSION
		);

		wp_enqueue_script(
			'presslyte-ai-search-check',
			plugins_url( 'assets/js/check.js', __FILE__ ),
			array(),
			self::VERSION,
			true
		);
		wp_script_add_data( 'presslyte-ai-search-check', 'strategy', 'defer' );

		wp_localize_script(
			'presslyte-ai-search-check',
			'PresslyteAISC',
			array(
				'postId'       => (int) $post_id,
				'editUrl'      => $edit_url ? esc_url_raw( $edit_url ) : '',
				'iconUrl'      => plugins_url( 'assets/images/icon-128x128.png', __FILE__ ),
				'labels'       => array(
					'panelTitle'    => __( 'AI Search Check', 'presslyte-ai-search-check' ),
					'panelIntro'    => __( 'A private, read-only review of this page. Nothing is changed or sent anywhere.', 'presslyte-ai-search-check' ),
					'close'         => __( 'Close', 'presslyte-ai-search-check' ),
					'edit'          => __( 'Open in WordPress Editor', 'presslyte-ai-search-check' ),
					'statusReady'   => __( 'Ready', 'presslyte-ai-search-check' ),
					'statusReview'  => __( 'Review', 'presslyte-ai-search-check' ),
					'statusNeeds'   => __( 'Needs attention', 'presslyte-ai-search-check' ),
					'priorityTitle' => __( 'Findings to review', 'presslyte-ai-search-check' ),
					'passedTitle'   => __( 'Checks that passed', 'presslyte-ai-search-check' ),
					'noIssues'      => __( 'No priority issues were found by this lightweight check.', 'presslyte-ai-search-check' ),
					'bodyFallback'  => __( 'A clear page-content area could not be identified, so this check reviewed the entire visible page, including theme content such as navigation and the footer.', 'presslyte-ai-search-check' ),
					'findings'      => array(
						'noindexBlockedTitle'   => __( 'An HTML meta noindex instruction was found', 'presslyte-ai-search-check' ),
						'noindexBlockedDetail'  => __( 'A robots, Googlebot, or Bingbot meta tag contains a noindex directive. Review it if the page should appear in search.', 'presslyte-ai-search-check' ),
						'noindexPassTitle'      => __( 'No HTML meta noindex was found; server headers were not checked', 'presslyte-ai-search-check' ),
						'noindexPassDetail'     => __( 'No robots, Googlebot, or Bingbot meta noindex instruction was found. Server X-Robots-Tag headers are not checked.', 'presslyte-ai-search-check' ),
						'noHeadingTitle'        => __( 'No clear primary heading was found', 'presslyte-ai-search-check' ),
						'noHeadingDetail'       => __( 'A clear primary heading helps readers understand the page immediately.', 'presslyte-ai-search-check' ),
						'multipleHeadingTitle'  => __( 'More than one primary heading was found', 'presslyte-ai-search-check' ),
						'multipleHeadingDetail' => __( 'Review the page hierarchy so the main topic is unmistakable.', 'presslyte-ai-search-check' ),
						'headingPassTitle'      => __( 'Primary heading', 'presslyte-ai-search-check' ),
						'headingPassDetail'     => __( 'One visible primary heading was found.', 'presslyte-ai-search-check' ),
						'headingSkipTitle'      => __( 'A heading level is skipped', 'presslyte-ai-search-check' ),
						'headingSkipDetail'     => __( 'A consistent heading order makes the page easier to follow for readers and assistive technology.', 'presslyte-ai-search-check' ),
						'headingOrderTitle'     => __( 'Heading order', 'presslyte-ai-search-check' ),
						'headingOrderDetail'    => __( 'No skipped heading level was found.', 'presslyte-ai-search-check' ),
						'missingAltTitle'       => __( 'An image has no alternative text', 'presslyte-ai-search-check' ),
						'missingAltDetail'      => __( 'Add useful alternative text when the image communicates information. Decorative images may use an empty alt value.', 'presslyte-ai-search-check' ),
						'altPassTitle'          => __( 'Image alternative text', 'presslyte-ai-search-check' ),
						'altPassDetail'         => __( 'Every visible image has an alt attribute.', 'presslyte-ai-search-check' ),
						'brokenImageTitle'      => __( 'An image did not load', 'presslyte-ai-search-check' ),
						'brokenImageDetail'     => __( 'Visitors and search systems may miss important information when an image is broken.', 'presslyte-ai-search-check' ),
						'vagueLinkTitle'        => __( 'A link does not describe its destination', 'presslyte-ai-search-check' ),
						'vagueLinkDetail'       => __( 'Use link text that tells readers what they will find after selecting it.', 'presslyte-ai-search-check' ),
						'linkPassTitle'         => __( 'Descriptive links', 'presslyte-ai-search-check' ),
						'linkPassDetail'        => __( 'No empty or commonly vague visible link was found.', 'presslyte-ai-search-check' ),
						'placeholderTitle'      => __( 'Possible placeholder or unprocessed shortcode found', 'presslyte-ai-search-check' ),
						'placeholderDetail'     => __( 'Review this text before visitors or search systems rely on the page.', 'presslyte-ai-search-check' ),
						'placeholderPassTitle'  => __( 'Visible content cleanup', 'presslyte-ai-search-check' ),
						'placeholderPassDetail' => __( 'No common placeholder text or shortcode pattern was found.', 'presslyte-ai-search-check' ),
						'longParagraphTitle'    => __( 'A paragraph may be difficult to scan', 'presslyte-ai-search-check' ),
						'longParagraphDetail'   => __( 'Consider splitting this long paragraph if doing so would make the explanation easier to follow.', 'presslyte-ai-search-check' ),
					),
				),
			)
		);
	}

	private static function can_check_current_page() {
		if ( is_admin() || ! is_user_logged_in() || ! is_admin_bar_showing() || ! is_singular() ) {
			return false;
		}

		$post_id = get_queried_object_id();

		return $post_id > 0 && current_user_can( 'edit_post', $post_id );
	}
}

Presslyte_AI_Search_Check::init();
