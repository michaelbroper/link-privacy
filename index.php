<?php

/*
  Plugin Name: Link Privacy
  Plugin URI: http://linkprivacy.com
  Description: Free plugin by SEO Revolution. Hide your network so it is difficult for competitors to find, analyze, and report it. This version automatically updates.
  Author: SEO Revolution
  Version: 1.2.2
  Author URI: http://seorevolution.com/
  GitHub Plugin URI: https://github.com/michaelbroper/link-privacy
 */

if ( !defined( 'ABSPATH' ) ) {
	require_once dirname(__FILE__) . '/../../../wp-load.php';
	header( 'HTTP/1.0 404 Not Found' );
	get_header();
	global $wp_query;
	$wp_query->set_404();
	include( get_404_template() );
	get_footer();
	die;
}

add_action( 'admin_init', 'link_privacy_has_parent_plugin' );
function link_privacy_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'github-updater-develop/github-updater.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Link Privacy requires the free <a href="https://github.com/afragen/github-updater/archive/develop.zip" target="_blank">GitHub Updater</a> to be installed and <strong><a href="/wp-admin/plugins.php?action=activate&plugin=github-updater-develop%2Fgithub-updater.php&plugin_status=all&paged=1&s&_wpnonce=1edf7419ad">activated</a></strong> for anonymous updates. <strong>Download <a href="https://github.com/afragen/github-updater/archive/develop.zip" target="_blank">HERE</a></strong>.</p></div><?php
}

if (is_admin()) {
    register_activation_hook(__FILE__, array('link_privacy_update', 'install'));
	register_uninstall_hook(__FILE__, array('link_privacy_update', 'uninstall'));
    add_action('admin_menu', array('link_privacy_update', 'setup_menu'));
} else {
    add_action('plugins_loaded', array('link_privacy_update', 'is_bot'));
}

add_filter('robots_txt', array('link_privacy', 'add_robotstxt'));

class link_privacy_update {

    public static function install() {
	    if (!get_option('link_privacy_bots')) {
			self::restore_default_bots();
	    }
    }

	public static function uninstall() {
		delete_option('link_privacy_bots');
	}

	/**
	 * Restore default set of bots
	 */
	public static function restore_default_bots() {
	    $defaultBots = array(
			array('id' => 1, 'url' => 'https://ahrefs.com/robot/', 'bot' => 'AhrefsBot', 'action' => 'deny'),
			array('id' => 2, 'url' => 'http://www.majestic12.co.uk/projects/dsearch/mj12bot.php', 'bot' => 'MJ12bot', 'action' => 'deny'),
			array('id' => 3, 'url' => 'http://moz.com/help/pro/rogerbot-crawler', 'bot' => 'Rogerbot', 'action' => 'deny'),
			array('id' => 4, 'url' => 'http://www.semrush.com/bot.html', 'bot' => 'SemrushBot', 'action' => 'deny'),
			array('id' => 5, 'url' => 'http://archive.org/about/exclude.php', 'bot' => 'ia_archiver', 'action' => 'deny'),
			array('id' => 6, 'url' => 'http://scoutjet.com', 'bot' => 'ScoutJet', 'action' => 'deny'),
			array('id' => 7, 'url' => 'http://crawler.sistrix.net', 'bot' => 'sistrix', 'action' => 'deny'),
			array('id' => 8, 'url' => 'http://www.searchmetrics.com/en/searchmetrics-bot/', 'bot' => 'SearchmetricsBot', 'action' => 'deny'),
			array('id' => 9, 'url' => 'http://www.seokicks.de/robot.html', 'bot' => 'SEOkicks-Robot', 'action' => 'deny'),
			array('id' => 10, 'url' => 'http://www.lipperhey.com/en/website-spider/', 'bot' => 'Lipperhey Spider', 'action' => 'deny'),
			array('id' => 11, 'url' => 'http://www.exalead.com/search/webmasterguide', 'bot' => 'Exabot', 'action' => 'deny'),
			array('id' => 12, 'url' => 'https://twitter.com/NetComber/status/334476871691550721', 'bot' => 'NCBot', 'action' => 'deny'),
			array('id' => 13, 'url' => 'http://www.backlinktest.com/crawler.html', 'bot' => 'BacklinkCrawler', 'action' => 'deny'),
			array('id' => 14, 'url' => 'http://archive.org/details/archive.org_bot', 'bot' => 'archive.org_bot', 'action' => 'deny'),
			array('id' => 15, 'url' => 'https://meanpath.com/meanpathbot.html', 'bot' => 'meanpathbot', 'action' => 'deny'),
			array('id' => 16, 'url' => 'http://www.botsvsbrowsers.com/details/1002332/index.html', 'bot' => 'PagesInventory', 'action' => 'deny'),
			array('id' => 17, 'url' => 'http://www.aboundex.com/crawler/', 'bot' => 'Aboundexbot', 'action' => 'deny'),
			array('id' => 18, 'url' => 'http://www.seoprofiler.com/bot/', 'bot' => 'spbot', 'action' => 'deny'),
			array('id' => 19, 'url' => 'http://www.linkdex.com/about/bots/', 'bot' => 'linkdexbot', 'action' => 'deny'),
			array('id' => 20, 'url' => 'http://www.useragentstring.com/pages/Gigabot/', 'bot' => 'Gigabot', 'action' => 'deny'),
			array('id' => 21, 'url' => 'http://en.wikipedia.org/wiki/DotBot', 'bot' => 'dotbot', 'action' => 'deny'),
			array('id' => 22, 'url' => 'http://nutch.apache.org/bot.html', 'bot' => 'Nutch', 'action' => 'deny'),
			array('id' => 23, 'url' => 'http://webmeup-crawler.com/', 'bot' => 'BLEXBot', 'action' => 'deny'),
			array('id' => 24, 'url' => 'http://graphicline.co.za/blogs/what-is-ezooms-bot', 'bot' => 'Ezooms', 'action' => 'deny'),
			array('id' => 25, 'url' => 'http://www.majestic12.co.uk/projects/dsearch/mj12bot.php', 'bot' => 'Majestic-12', 'action' => 'deny'),
			array('id' => 26, 'url' => 'http://www.majestic12.co.uk/projects/dsearch/mj12bot.php', 'bot' => 'Majestic-SEO', 'action' => 'deny'),
			array('id' => 27, 'url' => 'http://www.majestic12.co.uk/projects/dsearch/mj12bot.php', 'bot' => 'DSearch', 'action' => 'deny'),
			array('id' => 28, 'url' => 'http://blekko.com/about/blekkobot', 'bot' => 'BlekkoBot', 'action' => 'deny'),
			array('id' => 29, 'url' => 'http://help.yandex.com/search/?id=1112030', 'bot' => 'Yandex', 'action' => null),
			array('id' => 30, 'url' => 'https://support.google.com/webmasters/answer/182072?hl=en', 'bot' => 'googlebot', 'action' => null),
			array('id' => 31, 'url' => 'https://support.google.com/webmasters/answer/178852', 'bot' => 'Feedfetcher-Google', 'action' => null),
			array('id' => 32, 'url' => 'http://en.wikipedia.org/wiki/Bingbot', 'bot' => 'BingBot', 'action' => null),
			array('id' => 33, 'url' => 'http://nerdybot.com/', 'bot' => 'NerdyBot', 'action' => 'deny')
		);
        update_option('link_privacy_bots', serialize($defaultBots));
	}
	              
    public static function setup_menu() {
        $page = add_menu_page('Link Privacy', 'Link Privacy', 'administrator', 'link_privacy', array(__CLASS__, 'index'));
        add_action('load-' . $page, array(__CLASS__, 'actions'));
    }

    public static function notice() {

		$text = '';
		switch ($_GET['done']) {
			case "reset": $text = 'Bot action is reset'; break;
			case "deny": $text = 'Bot is denied'; break;
			case "cloak": $text = 'Bot is cloaked'; break;
			case "robots.txt": $text = 'Bot is added to robots.txt'; break;
			case "add_custom": $text = 'Custom bot is added'; break;
			case "mass_reset": $text = 'Actions are reset'; break;
			case "mass_deny": $text = 'Bots are denied'; break;
			case "mass_cloak": $text = 'Bots are cloaked'; break;
			case "mass_robots.txt": $text = 'Bots are added to robots.txt'; break;
			case "mass_delete": $text = 'Bot(s) deleted'; break;
		}

		echo $text ? '<div class="updated"><p>'.$text.'</p></div>' : '';
    }

	public static function applyAction($id, $action) {
	    $bots = unserialize(get_option('link_privacy_bots'));
		if ($bots) {
			if (is_array($id)) {
				foreach ($bots as &$bot) {
					if (in_array($bot['id'], $id)) {
						$bot['action'] = $action=='reset' ? null : $action;
					}
				}
			} else {
				foreach ($bots as &$bot) {
					if ($bot['id']==$id) {
						$bot['action'] = $action=='reset' ? null : $action;
						break;
					}
				}
			}
		    update_option('link_privacy_bots', serialize($bots));
		}
	}

    public static function actions() {
		if ($_GET['action']) {
			$action = $_GET['action'];
		} elseif ($_POST['action'] && $_POST['action']!=-1) {
			$action = $_POST['action'];
		} elseif ($_POST['action2'] && $_POST['action2']!=-1) {
			$action = $_POST['action2'];
		} 

        switch ($action) {
			case 'add_custom':
				$bot = trim($_POST['bot']);
				$url = trim($_POST['url']);

			    $bots = unserialize(get_option('link_privacy_bots'));
				$id = 1;
				if ($bots) {
					foreach ($bots as $b) {
						if ($b['id']>$id) {
							$id = $b['id'];
						}
					}
				}
				$bots[] = array('id' => $id+1, 'url' => $url, 'bot' => $bot, 'action' => null);
			    update_option('link_privacy_bots', serialize($bots));
				wp_redirect('admin.php?page='.rawurlencode($_GET['page']).'&done='.$action);
				exit;

			case 'restore_defaults':
            	check_admin_referer('restore_defaults');
				self::restore_default_bots();
				wp_redirect('admin.php?page='.rawurlencode($_GET['page']).'&done='.$action);
				exit;

			case 'reset':
			case 'deny':
			case 'cloak':
			case 'robots.txt':
				self::applyAction((int)$_GET['id'], $_REQUEST['action']);
				wp_redirect('admin.php?page='.rawurlencode($_GET['page']).'&done='.$action);
				exit;

			case 'mass_reset':
			case 'mass_deny':
			case 'mass_cloak':
			case 'mass_robots.txt':
				self::applyAction($_POST['ids'], str_replace('mass_', '', $action));
				wp_redirect('admin.php?page='.rawurlencode($_GET['page']).'&done='.$action);
				exit;

			case 'mass_delete':
	            if ($_POST['ids'] && is_array($_POST['ids'])) {
				    $bots = unserialize(get_option('link_privacy_bots'));
					if ($bots) {
						foreach ($bots as $id=>$bot) {
							if (in_array($bot['id'], $_POST['ids'])) {
								unset($bots[$id]);
							}
						}
					}
				    update_option('link_privacy_bots', serialize($bots));
				}
				wp_redirect('admin.php?page='.rawurlencode($_GET['page']).'&done='.$action);
				exit;

			default:
	            add_action('admin_notices', array('link_privacy', 'notice'));
		}
    }

	public static function is_bot() {

		$action = self::get_action();
		switch ($action) {
			case "deny":
				header('HTTP/1.0 403 Forbidden');
				echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You don\'t have permission to access '.$_SERVER['REQUEST_URI'].' on this server.</p></body></html>';
				exit;

			case "cloak":
				// cloak by empty page
				exit;
		}
	}

	protected static function get_action() {

		$ua   = $_SERVER['HTTP_USER_AGENT'];
	    $bots = unserialize(get_option('link_privacy_bots'));

		if ($bots && $ua) {
			foreach ($bots as $bot) {
		        if (stripos($ua, $bot['bot'])!==false) {
					return $bot['action'];
				}
			}
		}

		return false;
	}

    public static function index() {

		echo '<div class="wrap"><h2 id="link_privacy-title">Link Privacy</h2>';

        require_once 'table.php';
        $list = new link_privacy_list();
        $list->render_page();
?>
<br />
<div class="form-wrap">
	<h3>Add Custom Bot:</h3>

	<form method="post">
	<input type="hidden" name="action" value="add_custom" />

	<table cellPadding="0" cellSpacing="0" border="0" width="100%">
	<td width="50%">
	<div class="form-field form-required">
		<label for="tag-bot">Bot Mask:</label>
		<input id="tag-bot" type="text" name="bot" />
	</div>
	</td>
	<td width="50%">
	<div class="form-field form-required">
		<label for="tag-url">URL:</label>
		<input id="tag-url" type="text" name="url" />
	</div>
	</td>
	<td>
		<div class="submit"><input type="submit" class="button-primary" name="submit" value="Add Bot"></div>
	</td>
	</table>

	</form>

</div>

<?

		echo '</div>';
    }

	public static function add_robotstxt($text) {

	    $bots = unserialize(get_option('link_privacy_bots'));
		foreach ($bots as $bot) {
			if ($bot['action']=='robots.txt') {
				$text .= "\n\nUser-agent: ".$bot['bot']."\nDisallow: /";
			}
		}
		return $text;  
	}
}








add_action( 'wp_dashboard_setup', 'link_privacy_add_dashboard_widgets' );

function link_privacy_dashboard_widget_function() {

	// Display whatever it is you want to show.
$link_address = '#';
	// echo 'Talk with other likeminded SEOs. Join our Facebook group <a href='$link_address'>Link</a>.';}

echo '<strong><a href="https://www.facebook.com/groups/linkprivacy/" target="_blank">Join the Link Privacy Facebook group here</a>.</strong><br/><br/>Show us any footprint you find. We will fix it. Find a new link analysis bot? We will add it. Or come chat about SEO, we like that too.<br/><br/>No licensing required. No "calls home" for updates. Updates from <a href="https://github.com/michaelbroper/link-privacy" target="_blank">GitHub</a>.<br/><br/>For security and blocking IP ranges, use the <a href="https://wordpress.org/plugins/better-wp-security/" target="_blank">iThemes Security</a> plugin.<br/><br/>To your true privacy,<br/>Jerry West & Michael Roper, <a href="http://seorevolution.com/" target="_blank">SEO Revolution</a>';}

function link_privacy_add_dashboard_widgets() {
 	wp_add_dashboard_widget( 'link_privacy_dashboard_widget', 'Link Privacy: Activated', 'link_privacy_dashboard_widget_function' );
 	
 	// Globalize the metaboxes array, this holds all the widgets for wp-admin
 
 	global $wp_meta_boxes;
 	
 	// Get the regular dashboard widgets array 
 	// (which has our new widget already but at the end)
 
 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
 	
 	// Backup and delete our new dashboard widget from the end of the array
 
 	$link_privacy_widget_backup = array( 'link_privacy_dashboard_widget' => $normal_dashboard['link_privacy_dashboard_widget'] );
 	unset( $normal_dashboard['link_privacy_dashboard_widget'] );
 
 	// Merge the two arrays together so our widget is at the beginning
 
 	$sorted_dashboard = array_merge( $link_privacy_widget_backup, $normal_dashboard );
 
 	// Save the sorted array back into the original metaboxes 
 
 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
} 
