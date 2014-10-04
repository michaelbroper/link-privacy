<?php

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

if (!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class link_privacy_list extends WP_List_Table {

    private $delete_nonce;

    function __construct() {
        parent::__construct(array(
            'singular' => 'Bot',
            'plural' => 'Bots',
            'ajax' => false
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'url':
            case 'bot':
                return ''.$item[$column_name];
        }
    }

    function column_bot($item) {
		if ($item['action']) {
			$link = '<a href="?page='.$_REQUEST['page'].'&action=%s&id='.$item['id'].'">%s</a>';
	        $actions = array(
	            'reset' => sprintf($link, 'reset', 'Reset Action')
	        );
		} else {
			if (in_array(strtolower($item['bot']), array('bingbot', 'yandex', 'googlebot', 'feedfetcher-google'))) {
				$alert = 'onclick="return confirm(\'WARNING: These are the major search engines. Only block these if you do not want to be indexed in them. This is for those who want to build Google or Bing-only link networks, or hide the site completely.\')"';
			}
			$link = '<a href="?page='.$_REQUEST['page'].'&action=%s&id='.$item['id'].'"'.$alert.'>%s</a>';
	        $actions = array(
	            'deny' => sprintf($link, 'deny', 'Deny'),
	            'cloak' => sprintf($link, 'cloak', 'Cloak An Empty Page'),
	            'robots.txt' => sprintf($link, 'robots.txt', 'Add to Robots'),
	        );
		}
        return sprintf('%1$s %2$s', $item['bot'], $this->row_actions($actions));
    }

    function column_action($item) {
		switch ($item['action']) {
			case "deny": $text = 'Deny'; break;
			case "cloak": $text = 'Cloak An Empty Page'; break;
			case "robots.txt": $text = 'Add to Robots'; break;
		}		
		return $text ? '<font style="font-weight:bold; color:red">'.$text.'</font>' : '';
    }

    function column_url($item) {
		return '<a href="'.$item['url'].'" target="_blank">'.$item['url'].'</a>';
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'bot' => 'Bot',
            'action' => 'Action',
            'url' => 'URL'
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'mass_deny'       => 'Deny',
            'mass_cloak'      => 'Cloak An Empty Page',
			'mass_robots.txt' => 'Add to Robots',
            'mass_reset'      => 'Reset',
            'mass_delete'     => 'Delete'
        );                           
        return $actions;
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="ids[]" value="%s" />', $item['id']);
    }

    function prepare_items($pagination=true) {
        $columns = $this->get_columns();
        $hidden = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->items = unserialize(get_option('link_privacy_bots'));
    }

    function extra_tablenav($which) {
        ?>
		<div class="alignleft actions">
            <a href="<?php echo wp_nonce_url('?page='.rawurlencode($_REQUEST['page']).'&action=restore_defaults', 'restore_defaults') ?>" onclick="return confirm('Are you sure you want to do this?')" class="button-primary">
			Default Settings</a>
        </div>
		<?php
    }

    public function render_page() {
        $this->prepare_items();
		echo '<form method="POST"><input type="hidden" name="page" value="'.htmlspecialchars($_GET['page']).'">';
		$this->display();
		echo '</form>';
    }
}