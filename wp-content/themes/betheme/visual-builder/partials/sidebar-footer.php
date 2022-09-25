<?php  
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

echo '<div class="sidebar-panel-footer">
    <div class="mfn-option-dropdown btn-change-resolution">
        <a class="mfn-option-btn btn-large btn-icon-right mfn-option-text btn-icon-right btn-medium" style="padding: 0 4px;" title="Add" href="#"><span class="mfn-icon mfn-icon-desktop"></span><span style="width: 9px;" class="mfn-icon mfn-icon-fold"></span></a>
        <div class="dropdown-wrapper">
        <h6>Select a preview:</h6>
        <div class="mfn-dropdown-divider"></div>
        <a class="mfn-dropdown-item mfn-preview-opt" data-preview="desktop" href="#"><span class="mfn-icon mfn-icon-desktop"></span>Desktop <span class="res">960 &lt;</span></a>
        <a class="mfn-dropdown-item mfn-preview-opt" data-preview="tablet" href="#"><span class="mfn-icon mfn-icon-tablet"></span>Tablet <span class="res">768 - 959</span></a>
        <a class="mfn-dropdown-item mfn-preview-opt" data-preview="mobile" href="#"><span class="mfn-icon mfn-icon-mobile"></span>Mobile <span class="res">&lt; 768px</span></a>
        </div>
    </div>';

    echo '
    <a style="visibility: hidden;" class="mfn-option-btn btn-large btn-undo mfn-history-btn" title="Undo" href="#"><span class="mfn-icon mfn-icon-undo"></span></a>
    <a style="visibility: hidden;" class="mfn-option-btn btn-large btn-redo mfn-history-btn" title="Redo" href="#"><span class="mfn-icon mfn-icon-redo"></span></a>
    ';

    echo '<div class="mfn-option-dropdown btn-view-action">
        <a class="mfn-option-btn btn-large btn-icon-right mfn-option-text btn-icon-right btn-medium" style="padding: 0 4px;" href="#"><span class="mfn-icon mfn-icon-preview"></span><span style="width: 9px;" class="mfn-icon mfn-icon-fold"></span></a>
        <div class="dropdown-wrapper">';
        echo '<a class="mfn-dropdown-item menu-viewpage" href="'.get_the_permalink( $post->ID ).'" target="_blank"><span class="mfn-icon mfn-icon-view-page"></span> View page</a>';
        echo '<a class="mfn-dropdown-item mfn-preview-generate" target="_blank" href="#" data-href="'.get_permalink($post->ID).'&mfn-preview=true"><span class="mfn-icon mfn-icon-preview"></span> Generate preview</a>';
    echo '</div>
    </div>';
      
    if(get_post_status() == 'publish'){
       echo '<a href="#" data-action="update" class="mfn-btn btn-save-form-primary mfn-btn-green btn-copy-text btn-save-changes"><span class="btn-wrapper">Update</span></a>';
    }else{
        echo '<a href="#" data-action="publish" class="mfn-btn btn-save-form-primary mfn-btn-green btn-copy-text btn-save-changes"><span class="btn-wrapper">Publish</span></a>';
    }
    echo '<div class="mfn-option-dropdown btn-save-action">
        <a href="#" class="mfn-btn btn-save-option mfn-btn-green"><span class="mfn-icon mfn-icon-fold-light"></span></a>
        <div class="dropdown-wrapper">';
    if(get_post_status() == 'publish'){
        echo '<a data-action="draft" class="mfn-dropdown-item btn-save-form-secondary btn-save-changes" href="#">Save as draft</a>';
    }else{
        echo '<a data-action="update" class="mfn-dropdown-item btn-save-form-secondary btn-save-changes" href="#">Save draft</a>';
    }
    if( get_post_type($post->ID) == 'template' && get_post_meta($post->ID, 'mfn_template_type', true) != 'default' ){
        echo '<a class="mfn-btn woo-display-conditions" href="#"><span class="btn-wrapper">Display conditions</span></a>';
    }
    echo '</div>
    </div>
    
</div>';