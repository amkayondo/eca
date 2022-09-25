<?php  
if( ! defined( 'ABSPATH' ) ){
    exit; // Exit if accessed directly
}
$this->template_type = false;
if($this->post_type == 'post'){
    $page_opt_class = new Mfn_Post_Type_Post();
}elseif($this->post_type == 'portfolio'){
    $page_opt_class = new Mfn_Post_Type_Portfolio();
}elseif($this->post_type == 'template'){
    $page_opt_class = new Mfn_Post_Type_Template();
    $this->template_type = get_post_meta($post->ID, 'mfn_template_type', true);
}else{
    $page_opt_class = new Mfn_Post_Type_Page();
}

if($this->post_type == 'template' && $this->template_type == 'header'){
    $options = $page_opt_class->set_header_fields();
}else{
    $options = $page_opt_class->set_fields();
}


echo '<div class="panel panel-view-options" style="display: none;"><div class="mfn-form mfn-form-options">';
    
    echo '<form id="mfn-options-form">';
    echo '<input type="hidden" name="pageid" value="'.get_the_ID().'">';
    echo '<input type="hidden" name="mfn-builder-nonce" value="'.wp_create_nonce( 'mfn-builder-nonce' ).'">';
    if(count($options) > 0){
        foreach ($options['fields'] as $o=>$opt) {
            if(isset($opt['id'])){
                echo $this->mfn_formElement($opt, get_post_meta($post->ID, $opt['id'], true), 'mfnopt', '', '', 'releaser-first', '');
            }else if(isset( $opt['title']) && !isset($opt['id']) ){
                echo '<h5 class="row-header-title">'. wp_kses($opt['title'], mfn_allowed_html('title')) .'</h5>';
            }
        }
    }

    echo '<a href="#" data-action="update" class="mfn-btn btn-save-form-primary mfn-btn-blue btn-copy-text btn-save-changes"><span class="btn-wrapper">Save options</span></a>';

    echo '</form>';
        
        // echo '<pre>';
        // print_r( $options );
        // echo '</pre>';
              
echo '</div></div>';