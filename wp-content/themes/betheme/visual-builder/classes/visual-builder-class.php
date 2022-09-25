<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

class MfnVisualBuilder {

	public $url = MFN_OPTIONS_URI;

	public $post_type = false;
	public $template_type = false;
	public $options = array();
	public $widgets = array();

	public function __construct() {
		global $post;

    add_action( 'admin_enqueue_scripts', array( $this, 'mfn_append_vb_styles') );
    add_action( 'admin_body_class', array($this, 'mfn_admin_body_class') );

    $this->options = Mfn_Builder_Helper::get_options();

    if( isset($post->ID) ){
    	$this->post_type = get_post_type($post->ID);

	    if($this->post_type == 'template'){
				$this->template_type = get_post_meta($post->ID, 'mfn_template_type', true);
			}
    }
    

  }

	public function mfn_append_vb_styles() {

		global $post;

		if ($api_key = trim(mfn_opts_get('google-maps-api-key'))) {
			$api_key = '?key='. $api_key;
			wp_enqueue_script('google-maps', 'https://maps.google.com/maps/api/js'. esc_attr($api_key), false, null, true);
		}

		wp_enqueue_script( 'mfn-opts-plugins',get_template_directory_uri() .'/muffin-options/js/plugins.js', array('jquery'), MFN_THEME_VERSION, true );
		wp_enqueue_script('mfn-plugins', get_theme_file_uri('/js/plugins.js'), array('jquery'), MFN_THEME_VERSION, true);

		wp_enqueue_style('mfn-vbreset', get_theme_file_uri('/visual-builder/assets/css/reset.css'), false, MFN_THEME_VERSION, 'all');

		wp_enqueue_script('wp-theme-plugin-editor');
		wp_enqueue_style('wp-codemirror');

		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-draggable' );

    // Add the color picker
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
 		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );

 		// webfont

 		if( ! mfn_opts_get('google-font-mode') ) {
			wp_enqueue_script( 'mfn-webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array( 'jquery' ), false, true );
		}

 		wp_enqueue_editor();
 		wp_enqueue_media();

 		wp_enqueue_script('mfn-rangy', get_theme_file_uri('/visual-builder/assets/js/rangy-core.js'), false, MFN_THEME_VERSION, true);
 		wp_enqueue_script('mfn-rangy-classapplier', get_theme_file_uri('/visual-builder/assets/js/rangy-classapplier.js'), false, MFN_THEME_VERSION, true);
 	
 		// icons
 		wp_enqueue_style('mfn-icons', get_theme_file_uri('/fonts/mfn/icons.css'), false, time());
 		wp_enqueue_style('mfn-font-awesome', get_theme_file_uri('/fonts/fontawesome/fontawesome.css'), false, time());

 		// VB styles & scripts
 		wp_enqueue_style('mfn-vbcolorpickerstyle', get_theme_file_uri('/visual-builder/assets/css/nano.min.css'), false, time(), false);
 		wp_enqueue_style('mfn-vbstyle', get_theme_file_uri('/visual-builder/assets/css/style.css'), false, time(), false);

 		wp_enqueue_script('mfn-vbcolorpickerjs', get_theme_file_uri('/visual-builder/assets/js/pickr.min.js'), false, time(), true);
 		wp_enqueue_script('mfn-inline-editor-js', get_theme_file_uri('/visual-builder/assets/js/medium-editor.min.js'), false, time(), true);
 		wp_enqueue_script('mfn-vblistjs', get_theme_file_uri('/visual-builder/assets/js/list.min.js'), false, time(), true);
		wp_enqueue_script('mfn-vbscripts', get_theme_file_uri('/visual-builder/assets/js/scripts.js'), false, time(), true);

		$localize_visual = array(
			'mfnsc' => get_theme_file_uri( '/functions/tinymce/plugin.js' ),
		);

		wp_enqueue_script( 'mfn-opts-field-visual-vb', MFN_OPTIONS_URI .'fields/visual/field_visual_vb.js', array( 'jquery' ), MFN_THEME_VERSION, true );
		wp_localize_script( 'mfn-opts-field-visual-vb', 'fieldVisualJS_vb', $localize_visual);


		wp_add_inline_script( 'mfn-vbscripts', 'var ajaxurl = "'. admin_url( 'admin-ajax.php' ) . '";' );

		$permalink = get_preview_post_link($post->ID).'&visual=iframe';

		if( get_post_status($post->ID) == 'publish' ){
			$permalink = get_permalink( $post->ID );
			if( strpos($permalink, '?') !== false){
				$permalink .= '&visual=iframe';
			}else{
				$permalink .= '?visual=iframe';
			}
		}

		wp_localize_script( 'mfn-vbscripts', 'mfnvbvars',
      array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'rev_slider_id' => get_post_meta($post->ID, 'mfn-post-slider', true),
        'adminurl' => admin_url(),
        'themepath' => get_theme_file_uri('/'),
        'rooturl' => get_site_url(),
        'permalink' => $permalink,
      )
    );

	  $cm_args = wp_enqueue_code_editor(array(
			'autoRefresh' => true,
			'lint' => true,
			'indentUnit' => 2,
			'tabSize' => 2
		));

	    $codemirror['css']['codeEditor'] = wp_enqueue_code_editor(array(
			'type' => 'text/css', // required for lint
			'codemirror' => $cm_args,
		));

		$codemirror['html']['codeEditor'] = wp_enqueue_code_editor(array(
			'type' => 'text/html', // required for lint
			'codemirror' => $cm_args,
		));

		$codemirror['javascript']['codeEditor'] = wp_enqueue_code_editor(array(
			'type' => 'javascript', // required for lint
			'codemirror' => $cm_args,
		));

		wp_localize_script('mfn-vbscripts', 'mfn_cm', $cm_args);
		wp_enqueue_style('mfn-codemirror-dark', get_theme_file_uri('/visual-builder/assets/css/codemirror-dark.css'), false, MFN_THEME_VERSION, 'all');

		$lightboxOptions = mfn_opts_get('prettyphoto-options');

		$config = array(
			'mobileInit' => mfn_opts_get('mobile-menu-initial', 1240),
			'themecolor' => mfn_opts_get('color-theme'),
			'parallax' => mfn_parallax_plugin(),
			'responsive' => intval(mfn_opts_get('responsive', 0)),
			'sidebarSticky' => mfn_opts_get('sidebar-sticky') ? true : false,
			'lightbox' => array(
				'disable' => isset($lightboxOptions['disable']) ? true : false,
				'disableMobile' => isset($lightboxOptions['disable-mobile']) ? true : false,
				'title' => isset($lightboxOptions['title']) ? true : false,
			),
			'slider' => array(
				'blog' => intval(mfn_opts_get('slider-blog-timeout', 0)),
				'clients' => intval(mfn_opts_get('slider-clients-timeout', 0)),
				'offer' => intval(mfn_opts_get('slider-offer-timeout', 0)),
				'portfolio' => intval(mfn_opts_get('slider-portfolio-timeout', 0)),
				'shop' => intval(mfn_opts_get('slider-shop-timeout', 0)),
				'slider' => intval(mfn_opts_get('slider-slider-timeout', 0)),
				'testimonials' => intval(mfn_opts_get('slider-testimonials-timeout', 0)),
			),
		);

		wp_localize_script( 'mfn-vbscripts', 'mfn', $config );

	}

	public function sizes($size){
		$classes = array(
  			'divider' => 'divider',
  			'1/6' => 'one-sixth',
  			'1/5' => 'one-fifth',
  			'1/4' => 'one-fourth',
  			'1/3' => 'one-third',
  			'2/5' => 'two-fifth',
  			'1/2' => 'one-second',
  			'3/5' => 'three-fifth',
  			'2/3' => 'two-third',
  			'3/4' => 'three-fourth',
  			'4/5' => 'four-fifth',
  			'5/6' => 'five-sixth',
  			'1/1' => 'one'
  		);

  		return $classes[$size];
	}

	public function mfn_admin_body_class( $classes ) {
		if( $this->template_type == 'header' ){
			$classes .= ' mfn-preview-mode';
		}
    return $classes;
}

	public function mfn_load_sidebar(){
		global $post;
		$mfn_helper = new Mfn_Builder_Helper();

		require_once(get_theme_file_path('/visual-builder/visual-builder-header.php'));

		$mfn_items_get = '';
		if(get_post_meta($post->ID, 'mfn-page-items', true)){
			$mfn_items_get = get_post_meta($post->ID, 'mfn-page-items', true);
		}

		if($mfn_items_get && ! is_array($mfn_items_get)) {
			$mfn_items = unserialize(call_user_func('base'.'64_decode', $mfn_items_get));
			$navigator = self::getNavigatorTree($mfn_items);
		}else{
			$mfn_items = $mfn_items_get;
		}
		
		$widgetsClass =  new Mfn_Builder_Fields();

		$widgets = $widgetsClass->get_items();

		$inline_shortcodes = $widgetsClass->get_inline_shortcode();

		$builder_class = array();
		$builder_class[] = 'mfn-vb-'.$this->post_type;

		if( is_array( $this->options ) ){
			foreach( $this->options as $option_id => $option_val ){
				if( $option_val == "1" ){
					$builder_class[] = $option_id;
				}elseif( $option_val != "0" ){
					$builder_class[] = $option_val;
				}
			}
		}

		if( !in_array( array('mfn-ui-auto', 'mfn-ui-dark', 'mfn-ui-light'), $builder_class) ) $builder_class[] = 'mfn-ui-auto';

		$builder_class = implode( ' ', $builder_class );

		if($this->post_type == 'template' && !empty($this->template_type)){
			$builder_class .= ' mfn-vb-tmpl-'.$this->template_type;
		}

		echo '<div class="frameOverlay"></div><div id="mfn-visualbuilder" class="mfn-ui mfn-visualbuilder '.esc_attr( $builder_class ).'" data-tutorial="'. apply_filters('betheme_disable_support', '0') .'">';

		require_once(get_theme_file_path('/visual-builder/partials/preloader.php'));

		echo '<div class="mfn-contextmenu mfn-items-list-contextmenu"><ul><li><a href="#" data-action="love-it"><span class="mfn-icon mfn-icon-star"></span><span class="label">Add to favourites</span></a></li></ul></div>';

		require_once(get_theme_file_path('/visual-builder/partials/navigator.php'));

		echo '<div style="position: fixed; z-index: 9999;" class="mfn-contextmenu mfn-builder-area-contextmenu"><h6 class="mfn-context-header">Section</h6><ul><li><a href="#" data-action="edit"><span class="mfn-icon mfn-icon-edit"></span><span class="label">Edit</span></a></li><li><a href="#" class="mfn-context-copy" data-action="copy"><span class="mfn-icon mfn-icon-copy"></span><span class="label">Copy</span></a></li><li><a href="#" class="mfn-context-paste" data-action="paste"><span class="mfn-icon mfn-icon-paste"></span><span class="label">Paste</span></a></li><li class="mfn-contextmenu-delete"><a href="#" data-action="delete"><span class="mfn-icon mfn-icon-delete-red"></span><span class="label">Delete</span></a></li></ul></div>';

		$edit_lock = wp_check_post_lock($post->ID);

		if( $edit_lock && $edit_lock != get_current_user_id() ){
			require_once(get_theme_file_path('/visual-builder/partials/locker.php'));
		}else{
			wp_set_post_lock($post->ID);
		}

		// start sidebar
    echo '<div class="sidebar-wrapper" id="mfn-vb-sidebar">';

    echo '<div id="mfn-sidebar-resizer"></div>';
    echo '<div id="mfn-sidebar-switcher"></div>';

  // sidebar left
  require_once(get_theme_file_path('/visual-builder/partials/sidebar-menu.php'));

  // end sidebar left

  // start sidebar panel
    echo '<div class="sidebar-panel">';

    // start sidebar header

  require_once(get_theme_file_path('/visual-builder/partials/sidebar-header.php'));

  // end sidebar header

  // items panel
    echo '<div class="sidebar-panel-content">';

    // start items panel
    require_once(get_theme_file_path('/visual-builder/partials/sidebar-widgets.php'));

    // end items panel

   	// start pre build
   	require_once(get_theme_file_path('/visual-builder/partials/sidebar-prebuilds.php'));
   	// end pre build

    // start revision
    require_once(get_theme_file_path('/visual-builder/partials/sidebar-revisions.php'));
    // end revisions

    // start export/import
    require_once(get_theme_file_path('/visual-builder/partials/sidebar-export-import.php'));

   // end export/import

    // start settings
   	require_once(get_theme_file_path('/visual-builder/partials/sidebar-settings.php'));
   	// end settings

   	// start options
   	require_once(get_theme_file_path('/visual-builder/partials/sidebar-options.php'));
   	// end options

   // start edit form

   echo '<div class="panel panel-edit-item" style="display: none;"><div class="mfn-form"><form id="mfn-vb-form">';

   	echo '<input type="hidden" name="pageid" value="'.get_the_ID().'">';
   	echo '<input type="hidden" name="mfn-builder-nonce" value="'.wp_create_nonce( 'mfn-builder-nonce' ).'">';

		if(isset($mfn_items) && is_array($mfn_items)):
			$this->mfn_createForm($mfn_items);
		endif;
		//$this->mfn_createForm($mfn_items);

       echo '</form></div></div>';
       // end edit form

        echo '</div>';
        // start footer
        require_once(get_theme_file_path('/visual-builder/partials/sidebar-footer.php'));

        // end panel
        echo '</div>';
        // end sidebar
        echo '</div>';

        // iframe

        echo '<div id="mfn-preview-wrapper-holder" class="preview-wrapper">';
        // preview toolbar
        require_once(get_theme_file_path('/visual-builder/partials/preview-toolbar.php'));
        //echo '<pre style="line-height: 1.6em; display:none;">';print_r($mfn_items);echo '</pre>';
        echo '<div id="mfn-preview-wrapper"></div>';
        
		//echo '<iframe id="mfn-vb-ifr" src="'.get_permalink().'?visual=iframe"></iframe>';
		
		echo '</div>';

		// introduction
    require_once(get_theme_file_path('/visual-builder/partials/introduction.php'));

    // shortcuts
    require_once(get_theme_file_path('/visual-builder/partials/shortcuts.php'));

    // modal icons
		require_once(get_theme_file_path('/visual-builder/partials/modal-icons.php'));

		// modal shortcodes
		require_once(get_theme_file_path('/visual-builder/partials/modal-shortcodes.php'));

		if( get_post_type( $post->ID ) == 'template' ) require_once(get_theme_file_path('/visual-builder/partials/modal-conditionals.php'));

    echo '</div>';

    require_once(get_theme_file_path('/visual-builder/visual-builder-footer.php'));

	}

	public function mfn_appendNewSection($c, $r){
		$return = array();

		// wrappers loaded separately with muffins ui theme

		$mfn_fields = new Mfn_Builder_Fields();
		$mfn_helper = new Mfn_Builder_Helper();

		$items = $mfn_fields->get_section();
		$item_id = $mfn_helper->unique_ID();

		ob_start();

		echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="section" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-section-'.$item_id.'" >';

		echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

		$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$c.'][uid]', 'mcb-section-'.$item_id, $r);

		foreach($items as $i=>$j){
			if(isset($j['id'])){
				$this->mfn_formElement($j, '', $item_id, 'sections['.$c.'][attr]['.$j['id'].']', 'mcb-section-'.$item_id, $r);
			}else{
				$this->mfn_formElement($j, '', $item_id, '', 'mcb-section-'.$item_id, $r);
			}
		}

		echo '</div>';

		$form = ob_get_contents();

		ob_end_clean();

		$return['form'] = $form;

		$return['html'] = '<div data-order="'.$c.'" data-uid="'.$item_id.'" class="section mcb-section mcb-section-new mfn-new-item vb-item mcb-section-'.$item_id.' empty blink" data-title="Section" data-icon="mfn-icon-section">';
		$return['html'] .= $mfn_helper->sectionTools();
		$return['html'] .= '<div class="section_wrapper mcb-section-inner"><div class="mfn-section-new"><h5>Select a wrap layout</h5> <div class="wrap-layouts"> <div class="wrap-layout wrap-11" data-type="wrap-11" data-tooltip="1/1"></div><div class="wrap-layout wrap-12" data-type="wrap-12" data-tooltip="1/2 | 1/2"><span></span></div><div class="wrap-layout wrap-13" data-type="wrap-13" data-tooltip="1/3 | 1/3 | 1/3"><span></span><span></span></div><div class="wrap-layout wrap-14" data-type="wrap-14" data-tooltip="1/4 | 1/4 | 1/4 | 1/4"><span></span><span></span><span></span></div><div class="wrap-layout wrap-13-23" data-type="wrap-1323" data-tooltip="1/3 | 2/3"><span></span></div><div class="wrap-layout wrap-23-13" data-type="wrap-2313" data-tooltip="2/3 | 1/3"><span></span></div><div class="wrap-layout wrap-14-12-14" data-type="wrap-141214" data-tooltip="1/4 | 1/2 | 1/4"><span></span><span></span></div></div><p>or choose from</p><a class="mfn-btn prebuilt-button mfn-btn-green btn-icon-left" href="#"><span class="btn-wrapper"><span class="mfn-icon mfn-icon-add-light"></span>Pre-built sections</span></a> </div></div>';
		$return['html'] .= '<a href="#" class="btn-section-add mfn-icon-add-light mfn-section-add siblings next" data-position="after">Add section</a></div>';

		$return['id'] = $item_id;
		$return['navigator'] = $this->navigatorHtml('Section', $item_id);

		//'<li class="navigator-section nav-'.$item_id.'"><a data-uid="'.$item_id.'" href="#" class="">Section</a><span class="navigator-arrow"><i class="icon-down-open-big"></i></span></li>';

		return $return;
	}

	public function mfn_appendNewWrap($c, $s, $r, $d){

		$return = array();

		$mfn_fields = new Mfn_Builder_Fields();
		$mfn_helper = new Mfn_Builder_Helper();

		$items = $mfn_fields->get_wrap();
		$item_id = $mfn_helper->unique_ID();

		$input_size = '1/1';
		$class_size = 'one';

		if($d == 1){
			$input_size = 'divider';
			$class_size = 'divider';
		}

		ob_start();

		echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

		echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

		$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps]['.$c.'][uid]', 'mcb-wrap-'.$item_id, $r);
		$this->mfn_formElement('size', $input_size, $item_id, 'sections['.$s.'][wraps]['.$c.'][size]', 'mcb-wrap-'.$item_id, $r);
		$this->mfn_formElement('mobile_size', $input_size, $item_id, 'sections['.$s.'][wraps]['.$c.'][mobile_size]', 'mcb-wrap-'.$item_id, $r);
		$this->mfn_formElement('tablet_size', $input_size, $item_id, 'sections['.$s.'][wraps]['.$c.'][tablet_size]', 'mcb-wrap-'.$item_id, $r);

		foreach($items as $i=>$j){
			if(isset($j['type']) && isset($j['id'])){
				$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps]['.$c.'][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
			}else{
				$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
			}
		}

		echo '</div>';
		$form = ob_get_contents();

		ob_end_clean();

		$return['form'] = $form;
		$return['id'] = $item_id;

		$return['html'] = $this->wrapHtml($item_id, $input_size, $c, $class_size);

		$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/1');

		return $return;
	}

	public function mfn_appendWrapLayout($t, $s, $r){
		$return = array();

		$mfn_fields = new Mfn_Builder_Fields();
		$mfn_helper = new Mfn_Builder_Helper();

		$items = $mfn_fields->get_wrap();
		$item_id = $mfn_helper->unique_ID();

		switch ($t){
			case 'wrap-12':
				$item_id2 = $mfn_helper->unique_ID();
				ob_start();

				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';
				
				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/2', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/2', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div>';
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '1/2', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '1/2', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = $this->wrapHtml($item_id, '1/2', '0', 'one-second');
				$html .= $this->wrapHtml($item_id2, '1/2', '1', 'one-second');

				$return['form'] = $form;
				$return['html'] = $html;
				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/2').$return['navigator'] = $this->navigatorHtml('Wrap', $item_id2, '1/2');

			break;
			case 'wrap-13':
				$item_id2 = $mfn_helper->unique_ID();
				$item_id3 = $mfn_helper->unique_ID();
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/3', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/3', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '1/3', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '1/3', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id3.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id3.'" data-element="'.'mcb-wrap-'.$item_id3.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id3.' sidebar-panel-content-tabs spct-'.$item_id3.'"><li data-tab="content-'.$item_id3.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id3.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id3, $item_id3, 'sections['.$s.'][wraps][2][uid]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('size', '1/3', $item_id3, 'sections['.$s.'][wraps][2][size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('tablet_size', '1/3', $item_id3, 'sections['.$s.'][wraps][2][tablet_size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id3, 'sections['.$s.'][wraps][2][mobile_size]', 'mcb-wrap-'.$item_id3, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id3, 'sections['.$s.'][wraps][2][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id3, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id3, '', 'mcb-wrap-'.$item_id3, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = $this->wrapHtml($item_id, '1/3', '0', 'one-third');
				$html .= $this->wrapHtml($item_id2, '1/3', '1', 'one-third');
				$html .= $this->wrapHtml($item_id3, '1/3', '2', 'one-third');

				$return['form'] = $form;
				$return['html'] = $html;

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/3').$return['navigator'] = $this->navigatorHtml('Wrap', $item_id2, '1/3').$return['navigator'] = $this->navigatorHtml('Wrap', $item_id3, '1/3');

			break;
			case 'wrap-14':
				$item_id2 = $mfn_helper->unique_ID();
				$item_id3 = $mfn_helper->unique_ID();
				$item_id4 = $mfn_helper->unique_ID();
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/4', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '1/4', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id3.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id3.'" data-element="'.'mcb-wrap-'.$item_id3.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id3.' sidebar-panel-content-tabs spct-'.$item_id3.'"><li data-tab="content-'.$item_id3.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id3.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id3, $item_id3, 'sections['.$s.'][wraps][2][uid]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('size', '1/4', $item_id3, 'sections['.$s.'][wraps][2][size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id3, 'sections['.$s.'][wraps][2][tablet_size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id3, 'sections['.$s.'][wraps][2][mobile_size]', 'mcb-wrap-'.$item_id3, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id3, 'sections['.$s.'][wraps][2][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id3, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id3, '', 'mcb-wrap-'.$item_id3, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id4.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id4.'" data-element="'.'mcb-wrap-'.$item_id4.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id4.' sidebar-panel-content-tabs spct-'.$item_id4.'"><li data-tab="content-'.$item_id4.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id4.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id4, $item_id4, 'sections['.$s.'][wraps][3][uid]', 'mcb-wrap-'.$item_id4, $r);
				$this->mfn_formElement('size', '1/4', $item_id4, 'sections['.$s.'][wraps][3][size]', 'mcb-wrap-'.$item_id4, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id4, 'sections['.$s.'][wraps][3][tablet_size]', 'mcb-wrap-'.$item_id4, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id4, 'sections['.$s.'][wraps][3][mobile_size]', 'mcb-wrap-'.$item_id4, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id4, 'sections['.$s.'][wraps][3][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id4, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id4, '', 'mcb-wrap-'.$item_id4, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = $this->wrapHtml($item_id, '1/4', '0', 'one-fourth');
				$html .= $this->wrapHtml($item_id2, '1/4', '1', 'one-fourth');
				$html .= $this->wrapHtml($item_id3, '1/4', '2', 'one-fourth');
				$html .= $this->wrapHtml($item_id4, '1/4', '3', 'one-fourth');

				$return['form'] = $form;
				$return['html'] = $html;

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/4').$this->navigatorHtml('Wrap', $item_id2, '1/4').$this->navigatorHtml('Wrap', $item_id3, '1/4').$this->navigatorHtml('Wrap', $item_id4, '1/4');

			break;
			case 'wrap-1323':
				$item_id2 = $mfn_helper->unique_ID();
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/3', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/3', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '2/3', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '2/3', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = $this->wrapHtml($item_id, '1/3', '0', 'one-third');
				$html .= $this->wrapHtml($item_id2, '2/3', '1', 'two-third');

				$return['form'] = $form;
				$return['html'] = $html;

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/3').$this->navigatorHtml('Wrap', $item_id2, '2/3');

			break;
			case 'wrap-2313':
				$item_id2 = $mfn_helper->unique_ID();
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/3', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/3', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, 'n', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '2/3', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '2/3', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = '<div data-order="0" data-uid="'.$item_id2.'" data-desktop-size="2/3" data-tablet-size="2/3" data-mobile-size="1/1" style="background-repeat:no-repeat;background-position:left top;" class="blink wrap mcb-wrap mcb-wrap-new vb-item vb-item-wrap mcb-wrap-'.$item_id2.' two-third tablet-two-third mobile-one clearfix">'.$mfn_helper->wrapTools('2/3').'<div class="mcb-wrap-inner empty"><div class="mfn-drag-helper placeholder-wrap"></div><div class="mfn-wrap-new"><a href="#" class="mfn-item-add mfn-btn btn-icon-left btn-small mfn-btn-blank2"><span class="btn-wrapper"><span class="mfn-icon mfn-icon-add"></span>Add element</span></a></div></div></div>';
				$html .= '<div data-order="1" data-uid="'.$item_id.'" data-desktop-size="1/3" data-tablet-size="1/3" data-mobile-size="1/1" style="background-repeat:no-repeat;background-position:left top;" class="blink wrap mcb-wrap mcb-wrap-new vb-item vb-item-wrap mcb-wrap-'.$item_id.' one-third tablet-one-third mobile-one clearfix">'.$mfn_helper->wrapTools('1/3').'<div class="mcb-wrap-inner empty"><div class="mfn-drag-helper placeholder-wrap"></div><div class="mfn-wrap-new"><a href="#" class="mfn-item-add mfn-btn btn-icon-left btn-small mfn-btn-blank2"><span class="btn-wrapper"><span class="mfn-icon mfn-icon-add"></span>Add element</span></a></div></div></div>';

				$html = $this->wrapHtml($item_id2, '2/3', '0', 'two-third');
				$html .= $this->wrapHtml($item_id, '1/3', '1', 'one-third');
				

				$return['form'] = $form;
				$return['html'] = $html;

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id2, '2/3').$this->navigatorHtml('Wrap', $item_id, '1/3');

			break;
			case 'wrap-141214':
				$item_id2 = $mfn_helper->unique_ID();
				$item_id3 = $mfn_helper->unique_ID();
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/4', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id2.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id2.'" data-element="'.'mcb-wrap-'.$item_id2.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id2.' sidebar-panel-content-tabs spct-'.$item_id2.'"><li data-tab="content-'.$item_id2.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id2.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id2, $item_id2, 'sections['.$s.'][wraps][1][uid]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('size', '1/2', $item_id2, 'sections['.$s.'][wraps][1][size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('tablet_size', '1/2', $item_id2, 'sections['.$s.'][wraps][1][tablet_size]', 'mcb-wrap-'.$item_id2, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id2, 'sections['.$s.'][wraps][1][mobile_size]', 'mcb-wrap-'.$item_id2, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id2, 'sections['.$s.'][wraps][1][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id2, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id2, '', 'mcb-wrap-'.$item_id2, $r);
					}
				}
				echo '</div><div class="mfn-element-fields-wrapper mfn-vb-'.$item_id3.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id3.'" data-element="'.'mcb-wrap-'.$item_id3.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id3.' sidebar-panel-content-tabs spct-'.$item_id3.'"><li data-tab="content-'.$item_id3.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id3.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id3, $item_id3, 'sections['.$s.'][wraps][2][uid]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('size', '1/4', $item_id3, 'sections['.$s.'][wraps][2][size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('tablet_size', '1/4', $item_id3, 'sections['.$s.'][wraps][2][tablet_size]', 'mcb-wrap-'.$item_id3, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id3, 'sections['.$s.'][wraps][2][mobile_size]', 'mcb-wrap-'.$item_id3, $r);
				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id3, 'sections['.$s.'][wraps][2][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id3, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id3, '', 'mcb-wrap-'.$item_id3, $r);
					}
				}
				echo '</div>';
				$form = ob_get_contents();

				ob_end_clean();

				$html = $this->wrapHtml($item_id, '1/4', '0', 'one-fourth');
				$html .= $this->wrapHtml($item_id2, '1/2', '1', 'one-second');
				$html .= $this->wrapHtml($item_id3, '1/4', '2', 'one-fourth');

				$return['form'] = $form;
				$return['html'] = $html;

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/4').$this->navigatorHtml('Wrap', $item_id2, '1/2').$this->navigatorHtml('Wrap', $item_id3, '1/4');

			break;
			default:
				ob_start();
				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="wrap" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-wrap-'.$item_id.'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

				$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps][0][uid]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('size', '1/1', $item_id, 'sections['.$s.'][wraps][0][size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('tablet_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][tablet_size]', 'mcb-wrap-'.$item_id, $r);
				$this->mfn_formElement('mobile_size', '1/1', $item_id, 'sections['.$s.'][wraps][0][mobile_size]', 'mcb-wrap-'.$item_id, $r);

				foreach($items as $i=>$j){
					if(isset($j['id'])){
						$this->mfn_formElement($j, '', $item_id, 'sections['.$s.'][wraps][0][attr]['.$j['id'].']', 'mcb-wrap-'.$item_id, $r);
					}else{
						$this->mfn_formElement($j, '', $item_id, '', 'mcb-wrap-'.$item_id, $r);
					}

				}
				echo '</div>';
				$form = ob_get_contents();
				$return['id'] = $item_id;

				ob_end_clean();

				$return['form'] = $form;
				$return['html'] = $html = $this->wrapHtml($item_id, '1/1', '0', 'one');

				$return['navigator'] = $this->navigatorHtml('Wrap', $item_id, '1/1');
		}

		return $return;
	}


	public function mfn_appendNewWidget($c, $s, $w, $i, $r, $wrap_size, $p){

		$return = array();
		$params = array();
		$params_content = '';

		$mfn_fields = new Mfn_Builder_Fields();
		$mfn_helper = new Mfn_Builder_Helper();

		$widgets = $mfn_fields->get_items();
		$item_id = $mfn_helper->unique_ID();

		$widget = $widgets[$i];

		ob_start();

		echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$item_id.' '.$r.'" data-item="'.$i.'" data-group="mfn-vb-'.$item_id.'" data-element="'.'mcb-item-'.$item_id.'" >';

		echo '<ul class="mfn-vb-formrow mfn-vb-'.$item_id.' sidebar-panel-content-tabs spct-'.$item_id.'"><li data-tab="content-'.$item_id.'" class="spct-li-content active">Content</li><li data-tab="style-'.$item_id.'" class="spct-li-style">Style</li><li data-tab="advanced-'.$item_id.'" class="spct-li-advanced">Advanced</li></ul>';

		$this->mfn_formElement('uid', $item_id, $item_id, 'sections['.$s.'][wraps]['.$w.'][items]['.$c.'][uid]', 'mcb-item-'.$item_id, $r);

		foreach ($widget as $g => $wid) {

			if(is_array($wid)){
				foreach($wid as $e=>$t){
					$val = '';

					if(!empty($t['std'])) {
						$val = $t['std'];

						if($t['id'] == 'content'){
							$params_content = $t['std'];
						}else{
							$params[$t['id']] = $t['std'];
						}
					}

					if(!empty($t['vbstd'])) {
						$val = $t['vbstd'];

						if($t['id'] == 'content'){
							$params_content = $t['vbstd'];
						}else{
							$params[$t['id']] = $t['vbstd'];
						}
					}

					if(isset($t['id'])){
						$this->mfn_formElement($t, $val, $item_id, 'sections['.$s.'][wraps]['.$w.'][items]['.$c.'][fields]['.$t['id'].']', 'mcb-item-'.$item_id, $r, $widget['type']);
					}else{
						$this->mfn_formElement($t, $val, $item_id, '', 'mcb-item-'.$item_id, $r, $widget['type']);
					}
				}
			}else{
				// type, title, cat, size

				if( in_array($g, array('size', 'tablet_size', 'mobile_size')) ){
					$this->mfn_formElement($g, '1/1', $item_id, 'sections['.$s.'][wraps]['.$w.'][items]['.$c.']['.$g.']', 'mcb-item-'.$item_id, $r);
				}else{
					$this->mfn_formElement($g, $wid, $item_id, 'sections['.$s.'][wraps]['.$w.'][items]['.$c.']['.$g.']', 'mcb-item-'.$item_id, $r);
				}


			}
		}

		echo '</div>';

		$form = ob_get_contents();

		ob_end_clean();

		$return['form'] = $form;
		$return['id'] = $item_id;

		$size = $widget['size'];

		//if($wrap_size != '1/1'){
			$size = '1/1';
		//}


		$html = '<div data-order="'.$c.'" data-uid="'.$item_id.'" data-desktop-size="'.$size.'" data-tablet-size="'.$size.'" data-mobile-size="'.$size.'" data-icon="mfn-icon-'.str_replace('_', '-', $widget['type']).'" class="blink column mcb-column mfn-new-item vb-item vb-item-widget mcb-item-'.$item_id.' column_'.$widget['type'].' '.$this->sizes($size).' tablet-'.$this->sizes($size).' mobile-one">';

		$html .= '<div class="mcb-column-inner mcb-item-'.$i.'-inner">';

		$html .= $mfn_helper->itemTools($size);

		$fun_name = 'sc_'.$i;

		if($i == 'placeholder'){
			$html .= '<div class="placeholder"></div>';
		}elseif($i == 'shop_products'){
			$html .= $fun_name($params, 'sample');
		}elseif($i == 'content'){
			$html .= '<div class="content-wp">'.get_post_field( 'post_content', $p ).'</div>';
		}elseif($i == 'divider'){
			$html .= '<hr />';
		}elseif($i == 'slider_plugin'){
			$html .= '<div class="mfn-widget-placeholder mfn-wp-revolution"><img class="item-preview-image" src="'.get_theme_file_uri('/muffin-options/svg/placeholders/slider_plugin.svg').'"></div>';
		}elseif($i == 'visual'){
			$html .= '<div class="mfn-visualeditor-content mfn-inline-editor">'.$params_content.'</div>';
		}elseif($i == 'sidebar_widget'){
			$html .= '<img src="'.get_theme_file_uri( '/muffin-options/svg/placeholders/sidebar_widget.svg' ).'" alt="">';
		}elseif($i == 'column'){
			$html .= '<div class="column_attr mfn-inline-editor clearfix">'.$params_content.'</div>';
		}elseif($i == 'image_gallery'){
			$params['id'] = null;
			$html .= sc_gallery($params);
		}elseif($i == 'shop' && class_exists( 'WC_Shortcode_Products' )){
			$params['post'] = 0;
			$shortcode = new WC_Shortcode_Products( $params, 'products' );
			$html .= $shortcode->get_content();
		}elseif(!empty($params_content)){
			$html .= $fun_name($params, $params_content);
		}else{
			$output = $fun_name($params);
			if(is_array($output)){
				$html .= $output[0];
				$return['script'] = $output[1];
			}else{
				$html .= $output;
			}
		}

		$html .= '</div></div>';

		$return['html'] = $html;

		$return['navigator'] = $this->navigatorHtml($i, $item_id, $widget['title']);

		//'<li class="navigator-item nav-'.$item_id.'"><a data-uid="'.$item_id.'" href="#"><span class="mfn-icon mfn-icon-'.str_replace('_', '-', $i).'"></span><span class="navitemtype">'.$widget['title'].'</span></a></li>';
		return $return;

	}


	public function mfn_createForm($mfn_items, $a = false, $r = false){
		$a ? $a = $a : $a = 0;
		$r ? $release = $r : $release = 'releaser-first';

		$mfn_fields = new Mfn_Builder_Fields();
		$sections = $mfn_fields->get_section();

		foreach($mfn_items as $i=>$j){

			if( !empty($j['uid']) ){

			echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$j['uid'].' '.$release.'" data-item="section" data-group="mfn-vb-'.$j['uid'].'" data-element="'.'mcb-section-'.$j['uid'].'" >';

			echo '<ul class="mfn-vb-formrow mfn-vb-'.$j['uid'].' sidebar-panel-content-tabs spct-'.$j['uid'].'"><li data-tab="content-'.$j['uid'].'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$j['uid'].'" class="spct-li-advanced">Advanced</li></ul>';

			$b = 0;
			echo $this->mfn_formElement('uid', $j['uid'], $j['uid'], 'sections['.$a.'][uid]', 'mcb-section-'.$j['uid'], $release);
			foreach ($sections as $x => $section) {
				$val = '';
				if(isset($section['id'])){
					if (isset($j['attr']) && array_key_exists($section['id'], $j['attr'])) {
						$val = $j['attr'][$section['id']];
					}
					echo $this->mfn_formElement($section, $val, $j['uid'], 'sections['.$a.'][attr]['.$section['id'].']', 'mcb-section-'.$j['uid'], $release);
				}else{
					echo $this->mfn_formElement($section, '', $j['uid'], '', 'mcb-section-'.$j['uid'], $release);
				}
			}

			echo '</div>';
			if(isset($j['wraps']) && is_iterable($j['wraps'])):
				// display wraps
				$this->mfn_createForm_wraps($j['wraps'], $a, $b, $release);
			endif;
			$a++;
		}
		}
	}

	public function mfn_createForm_wraps($wraps_arr, $a, $b, $release){
		$mfn_fields = new Mfn_Builder_Fields();
		$wraps = $mfn_fields->get_wrap();
		$c = 0;

		foreach($wraps_arr as $m=>$n){
			if(!empty($n['uid']) && !empty($n['size'])){

				echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$n['uid'].' '.$release.'" data-item="wrap" data-group="mfn-vb-'.$n['uid'].'" data-element="'.'mcb-wrap-'.$n['uid'].'" >';

				echo '<ul class="mfn-vb-formrow mfn-vb-'.$n['uid'].' sidebar-panel-content-tabs spct-'.$n['uid'].'"><li data-tab="content-'.$n['uid'].'" class="spct-li-content active">Settings</li><li data-tab="advanced-'.$n['uid'].'" class="spct-li-advanced">Advanced</li></ul>';
				
				echo $this->mfn_formElement('uid', $n['uid'], $n['uid'], 'sections['.$a.'][wraps]['.$b.'][uid]', 'mcb-wrap-'.$n['uid'], $release);
				echo $this->mfn_formElement('size', $n['size'], $n['uid'], 'sections['.$a.'][wraps]['.$b.'][size]', 'mcb-wrap-'.$n['uid'], $release);
				echo !empty($n['tablet_size']) ? $this->mfn_formElement('tablet_size', $n['tablet_size'], $n['uid'], 'sections['.$a.'][wraps]['.$b.'][tablet_size]', 'mcb-wrap-'.$n['uid'], $release) : $this->mfn_formElement('tablet_size', $n['size'], $n['uid'], 'sections['.$a.'][wraps]['.$b.'][tablet_size]', 'mcb-wrap-'.$n['uid'], $release);
				echo !empty($n['mobile_size']) ? $this->mfn_formElement('mobile_size', $n['mobile_size'], $n['uid'], 'sections['.$a.'][wraps]['.$b.'][mobile_size]', 'mcb-wrap-'.$n['uid'], $release) : $this->mfn_formElement('mobile_size', '1/1', $n['uid'], 'sections['.$a.'][wraps]['.$b.'][mobile_size]', 'mcb-wrap-'.$n['uid'], $release);
				
				foreach ($wraps as $y => $wrap) {
					$val = '';
					if( isset($wrap['id']) ){
						if (isset($n['attr']) && array_key_exists($wrap['id'], $n['attr'])) {
							$val = $n['attr'][$wrap['id']];
						}
						echo $this->mfn_formElement($wrap, $val, $n['uid'], 'sections['.$a.'][wraps]['.$b.'][attr]['.$wrap['id'].']', 'mcb-wrap-'.$n['uid'], $release);
					}else{
						echo $this->mfn_formElement($wrap, '', $n['uid'], '', 'mcb-wrap-'.$n['uid'], $release);
					}
				}
				echo '</div>';
				if(isset($n['items']) && is_iterable($n['items']) && count($n['items']) > 0):
						$this->mfn_createForm_items($n['items'], $a, $b, $c, $release);
				endif;
				$b++;
			}
		}
		
	}

	public function mfn_createForm_items($items_arr, $a, $b, $c, $release){
		$mfn_fields = new Mfn_Builder_Fields();
		$widgets = $mfn_fields->get_items();

		foreach($items_arr as $q=>$r){

			if( !empty($r['type']) ){

			echo '<div class="mfn-element-fields-wrapper mfn-vb-'.$r['uid'].' '.$release.'" data-item="'.$r['type'].'" data-group="mfn-vb-'.$r['uid'].'" data-element="'.'mcb-item-'.$r['uid'].'" >';
			echo '<ul class="mfn-vb-formrow mfn-vb-'.$r['uid'].' sidebar-panel-content-tabs spct-'.$r['uid'].'"><li data-tab="content-'.$r['uid'].'" class="spct-li-content active">Content</li><li data-tab="style-'.$r['uid'].'" class="spct-li-style">Style</li><li data-tab="advanced-'.$r['uid'].'" class="spct-li-advanced">Advanced</li></ul>';

			$desktop_size = $r['size'];
			echo $this->mfn_formElement('uid', $r['uid'], $r['uid'], 'sections['.$a.'][wraps]['.$b.'][items]['.$c.'][uid]', 'mcb-item-'.$r['uid'], $release);
			//if( empty($r['tablet_size']) ) echo $this->mfn_formElement('tablet_size', $desktop_size, $r['uid'], 'sections['.$a.'][wraps]['.$b.'][items]['.$c.'][tablet_size]', 'mcb-item-'.$r['uid'], $release);
			//if( empty($r['mobile_size']) ) echo $this->mfn_formElement('mobile_size', '1/1', $r['uid'], 'sections['.$a.'][wraps]['.$b.'][items]['.$c.'][mobile_size]', 'mcb-item-'.$r['uid'], $release);

			foreach($widgets[$r['type']] as $s=>$t){
				if(!is_array($t) && isset($widgets[$r['type']][$s]) && !is_array( $widgets[$r['type']][$s] )){
					$item_val = !empty($r[$s]) ? $r[$s] : $t;
					if( $s == 'tablet_size' && empty($r[$s]) ) $item_val = $r['size']; // empty tablet size
					echo $this->mfn_formElement($s, $item_val, $r['uid'], 'sections['.$a.'][wraps]['.$b.'][items]['.$c.']['.$s.']', 'mcb-item-'.$r['uid'], $release, $widgets[$r['type']]['type']);
				}elseif(is_array($t) && isset($widgets[$r['type']][$s]) && is_array( $widgets[$r['type']][$s] )){
					foreach($widgets[$r['type']][$s] as $f=>$field){
						$wid_val = '';
						if(isset($field['id'])){
							if( isset($r['fields'][$field['id']]) ) $wid_val = $r['fields'][$field['id']];
							echo $this->mfn_formElement($field, $wid_val, $r['uid'], 'sections['.$a.'][wraps]['.$b.'][items]['.$c.'][fields]['.$field['id'].']', 'mcb-item-'.$r['uid'], $release, $widgets[$r['type']]['type']);
						}else{
							echo $this->mfn_formElement($field, '', $r['uid'], '', 'mcb-item-'.$r['uid'], $release);
						}
					}
				}
			}

			echo '</div>';
			$c++;
		}
		}
	}

	public function mfn_formElement($field, $value, $uid, $meta, $t, $r, $n = false){

		// $field - input name
		// $value - input value
		// $uid - uid
		// $meta - name attr
		// $t - type
		// $r - release
		// $n - widget name optional

		$field_name = '';
		$fid = '';
		$classes = '';

		if( 
			( !empty($field['class']) && strpos($field['class'], 'mfn-deprecated') !== false && empty($value) && !empty($meta) ) || 
			( !empty($field['class']) && strpos($field['class'], 'mfn-deprecated') !== false && !empty($field['std']) && !empty($value) && $field['std'] == $value) 
		){
			return;
		}

		if( !is_array($field) && !isset($field['id']) ){
			//echo $field.'<br>';
			echo '<input class="'.$field.'input item-hidden-inputs mfn-form-control mfn-form-input" type="hidden" data-name="'.$field.'" name="'.$meta.'" value="'.$value.'">';
			return;
		}

		/*if( !empty($field['form']) && $field['form'] == 'advanced' && $value != 'mfnforce' ){

			if( !empty( $value ) && !empty( $meta ) ) echo '<input type="text" name="'.$meta.'" value="'.$value.'">';
			return;
			
		}*/

		if( isset( $field['themeoptions'] ) ){
			$themeoption = explode(':', $field['themeoptions']);
			if( isset($themeoption[0]) && isset($themeoption[1]) ){
				if( (!empty(mfn_opts_get($themeoption[0])) && mfn_opts_get($themeoption[0]) != $themeoption[1]) || (empty(mfn_opts_get($themeoption[0])) && !empty($themeoption[1])) ){
					return;
				}else{
					$classes .= !empty( mfn_opts_get('style') ) ? ' theme-'.mfn_opts_get('style').'-style' : ' theme-classic-style';
				}
			}
		}

		$csspath = false;
		$dataname = false;
		$conditions = false;
		$id = false;

		if(isset($field['edit_tag'])){
			$classes .= ' content-txt-edit';
		}

		/*if ( isset($field['std']) && !is_array($field['std']) && ( ! $value ) && ( '0' !== $value ) ) {
			$value = stripslashes( htmlspecialchars( $field['std'], ENT_QUOTES ) );
		}*/

		if(isset($field['class'])){
			$classes .= ' '.$field['class'];
		}

		if(isset($field['settings'])){
			$classes .= ' toggle_fields';
		}

		if( !empty($field['row_class']) ){
			$classes .= ' '.$field['row_class'];
		}

		if(isset($field['type']) && $field['type'] == 'sliderbar' && isset($field['units'])){
			$classes .= ' sliderbar-units';	
		}

		$element = explode('-', $t);

		if(isset($field['id'])){
			$fid = $field['id'];
			$tmppreview = explode(':', $field['id']);
			$field_name = end($tmppreview);
			$field_name = str_replace(array(']', 'typography['), '', $field_name);
		}

		if( strpos($fid, 'style:') !== false ){
			$css_arr = explode(':', $fid);
			if( isset($css_arr[1]) ){

				$csspath = 'data-csspath="'.str_replace('mfnuidelement', $uid, $css_arr[1]).'"';
				$dataname = 'data-name="'.( $field_name == 'gradient' ? 'background-image' : str_replace(array('_mobile', '_tablet'), '', $field_name ) ).'"';
				$classes .= ' inline-style-input';
			}
		}

		if( strpos($fid, '|hover') !== false ){
			$classes .= ' mfn-hover-input';
		}

		if($uid == 'mfnopt'){

			echo '<div class="mfn-form-row mfn-row mfn-vb-formrow mfnopt '.$r.' '.$classes.'">';

				if(isset($field['type']) ):

					echo '<div class="form-label-wrapper">';
					if(isset($field['title'])) echo '<label class="form-label">'.$field['title'].'</label>';

					if(isset($field['responsive'])) Mfn_Options_field::get_responsive_swither($field['responsive']);

					echo '</div>';

					echo '<div class="form-content">';

		     	$field_class = 'MFN_Options_'. $field['type'];

					require_once( get_template_directory() .'/muffin-options/fields/'. $field['type'] .'/field_'. $field['type'] .'.php' );

					if ( class_exists( $field_class ) ) {
						$field_object = new $field_class( $field, $value, 'options' );
						$field_object->render( $meta, true );
					}

					echo '</div>';

				endif;

			echo '</div>';

		}else{

			$n == 'button' ? $n = 'widget-button' : null;
			$n == 'chart' ? $n = 'widget-chart' : null;
			$n == 'code' ? $n = 'widget-code' : null;
			$n ? $classes .= ' '.$n : null;

			if(empty($meta) && isset($field['title'])){
				$classes .= ' row-header';
			}

			$classes .= ' '.$field_name;

			if(isset($field['re_render']) && $field['re_render'] == 'tabs'){
				$classes .= ' re_render_tabs';
			}else if(isset($field['re_render']) && $field['re_render'] == 'standard'){
				$classes .= ' re_render';
			}

			if( !$dataname ) $dataname = 'data-name="'.$field_name.'"';

			if(isset($field['type']) && $field['type'] == 'html'){

				echo str_replace( 'mfnuidhere', $uid, $field['html'] );

				if(isset($field['title'])){
					echo '<label>'.$field['title'];
					if(isset($field['label_after'])){
							echo $field['label_after']; 
						}
					echo '</label>'; 
				}
			}elseif(isset($field['type']) && in_array($field['type'], array('info', 'helper')) ){

				echo '<div class="mfn-form-row mfn-vb-formrow mfn-type-'.$element[1]. ' ' .(isset($field['class']) ? $field['class'] : null).'">';

				$field_class = 'MFN_Options_'. $field['type'];

				require_once( get_template_directory() .'/muffin-options/fields/'. $field['type'] .'/field_'. $field['type'] .'.php' );

				if ( class_exists( $field_class ) ) {
					$field_object = new $field_class( $field, $value );
					$field_object->render( $meta );
				}

				echo '</div>';

			}else{

				if( isset($field['condition']) ){
					$classes .= ' activeif activeif-'.$field['condition']['id'];
					$conditions = 'data-conditionid="'. $field['condition']['id'] .'" data-opt="'. $field['condition']['opt'] .'" data-val="'. (is_array($field['condition']['val']) ? implode(',', $field['condition']['val']) : $field['condition']['val'] ) .'"';
				}

				if( isset($field['attr_id']) ){
					$id = 'id="'.$field['attr_id'].'"';
					//$classes .= ' '.$field['attr_id'];
				}

				if( isset($field['type']) && in_array($field['type'], array('text', 'textarea', 'select')) ){
					$classes .= ' form-group-vb';
				}
				
				echo '<div class="mfn-form-row mfn-vb-formrow'.$classes.'" '.$dataname.' '.$csspath.' '.$conditions.' '.$id.' '.(isset($field['edit_tagchild']) ? 'data-edittagchild="'.$field['edit_tagchild'].'"' : null).' '.(isset($field['edit_tag']) ? 'data-edittag="'.$field['edit_tag'].'"' : null).' '.(isset($field['edit_position']) ? 'data-tagposition="'.$field['edit_position'].'"' : null ).' '.(isset($field['edit_tag_var']) ? 'data-edittagvar="'.$field['edit_tag_var'].'"' : null ).'>';

				if(empty($meta) && isset($field['title'])):
					echo '<h5 class="row-header-title">'. wp_kses($field['title'], mfn_allowed_html('title')) .'</h5>';

				elseif(isset($field['type']) ):

					$field['preview'] = $field_name.'input';

				if(isset($field['title'])){
						$label_class = 'form-label';

						if( isset($field['responsive']) || isset($field['iconinfo']) || isset($field['desc']) ){
							$label_class .= ' form-label-wrapper';
						}

						echo '<label class="'.$label_class.'">'.$field['title'];
						if(isset($field['label_after'])){
							echo $field['label_after']; 
						}

						if(isset($field['responsive'])) Mfn_Options_field::get_responsive_swither($field['responsive']);
						if(isset($field['iconinfo'])) Mfn_Options_field::get_icon_info($field['iconinfo']);
						if(isset($field['desc'])) Mfn_Options_field::get_icon_desc($field['desc']);

						echo '</label>'; 

						if ( ! empty( $field['desc'] ) ) {
							echo '<div class="desc-group">';
								echo '<span class="description">'. $field['desc'] .'</span>';
							echo '</div>';
						}
					}

					if($field['type'] != 'typography_vb') echo '<div class="form-content">';

		      $field_class = 'MFN_Options_'. $field['type'];

		      //echo $field['type'];

					require_once( get_template_directory() .'/muffin-options/fields/'. $field['type'] .'/field_'. $field['type'] .'.php' );

					if ( class_exists( $field_class ) ) {
						$field_object = new $field_class( $field, $value );
						if( $field['type'] == 'typography_vb' ){
							$field_object->render( $meta, true, $uid, $r );
						}else{
							$field_object->render( $meta, true );
						}
						
					}

					if($field['type'] != 'typography_vb') echo '</div>';

				endif;

				echo '</div>';

			}
		}
	}

	public function wrapHtml($item_id, $size, $order, $sizeclass){
		$mfn_helper = new Mfn_Builder_Helper();
		$html = '<div data-title="Wrap" data-icon="mfn-icon-wrap" data-order="'.$order.'" data-uid="'.$item_id.'" data-desktop-size="'.$size.'" data-tablet-size="'.$size.'" data-mobile-size="1/1" class="blink wrap mcb-wrap mcb-wrap-new vb-item vb-item-wrap mcb-wrap-'.$item_id.' '.$sizeclass.' tablet-'.$sizeclass.' mobile-one clearfix"><div class="mcb-wrap-inner empty">'.$mfn_helper->wrapTools($size).'<div class="mfn-drag-helper placeholder-wrap"></div><div class="mfn-wrap-new"><a href="#" class="mfn-item-add mfn-btn btn-icon-left btn-small mfn-btn-blank2"><span class="btn-wrapper"><span class="mfn-icon mfn-icon-add"></span>Add element</span></a></div></div></div>';

		return $html;
	}

	public function navigatorHtml($type, $item_id, $size = false){

		if( $type == 'Section' ){
			$html = '<li class="navigator-section nav-'.$item_id.'"><a data-uid="'.$item_id.'" href="#" class="">'.$type.'</a><span class="navigator-arrow"><i class="icon-down-open-big"></i></span></li>';
		}elseif( $type == 'Wrap'){
			$html = '<li class="navigator-wrap nav-'.$item_id.'"><a data-uid="'.$item_id.'" href="#">'.$type.' <span class="navigator-size-label">'.$size.'</span><span class="navigator-add-item back-to-widgets"></span></a><span class="navigator-arrow"><i class="icon-down-open-big"></i></span></li>';
		}else{
			$html = '<li class="navigator-item nav-'.$item_id.'"><a data-uid="'.$item_id.'" href="#"><span class="mfn-icon mfn-icon-'.str_replace('_', '-', $type).'"></span><span class="navitemtype">'.$size.'</span></a></li>';
		}
		
		return $html;
	}

	public static function getNavigatorTree($mfn_items){
		$nav = '';
		if(isset($mfn_items) && is_array($mfn_items) && is_iterable($mfn_items)){
			foreach ($mfn_items as $section) {
				if( !empty($section["uid"]) ){
				$nav .= '<li class="navigator-section nav-'.$section["uid"].'"><a data-uid="'.$section['uid'].'" href="#">Section '.( !empty($section['attr']['custom_id']) ? '<span class="navigator-section-id">#'.$section['attr']['custom_id'].'</span>' : null ).'</a><span class="navigator-arrow"><i class="icon-down-open-big"></i></span>';
					if(isset($section['wraps']) && is_iterable($section['wraps'])){
						$nav .= '<ul class="mfn-sub-nav">';
							foreach ($section['wraps'] as $wrap) {
								if( !empty($wrap['uid']) && !empty($wrap['size']) ){
								$nav .= '<li class="navigator-wrap nav-'.$wrap['uid'].'"><a data-uid="'.$wrap['uid'].'" href="#">Wrap <span class="navigator-size-label">'.$wrap['size'].'</span><span class="navigator-add-item back-to-widgets"></a><span class="navigator-arrow"><i class="icon-down-open-big"></i></span>';
									if(isset($wrap['items']) && is_iterable($wrap['items'])){
									$nav .= '<ul class="mfn-sub-nav">';
										foreach ($wrap['items'] as $i=>$item) {
											if( !empty($item['type']) ){
											$nav .= '<li data-name="'.$item['type'].'" class="navigator-item nav-'.$item['uid'].' navitemtype"><a data-uid="'.$item['uid'].'" href="#"><span class="mfn-icon mfn-icon-'.str_replace('_', '-', $item['type']).'"></span>'.( !empty($item['title']) ? $item['title'] : str_replace('_', ' ', ucfirst($item['type'])) ).'</a></li>';
											}
										}
									$nav .= '</ul>';
									}
								$nav .= '</li>';
							}
							}
						$nav .= '</ul>';
					}
				$nav .= '</li>';
			}
			}
		}

		return $nav;
	}

}
