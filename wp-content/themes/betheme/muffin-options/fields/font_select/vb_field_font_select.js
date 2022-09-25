(function($) {

  /* globals jQuery */

  "use strict";

  function mfnFieldFontSelect($group) {

    var options = '',
      value;

    if( typeof mfn_google_fonts !== 'undefined' ){

      mfn_google_fonts.forEach((entry) => {
        options += '<option value="'+ entry +'">'+ entry +'</option>';
      });

      options += '</optgroup>';

      // console.log(options);

      $('optgroup[data-type="google-fonts"]', $group).empty()
        .append(options);

      $group.each(function(index, $el){

        value = $('select', $el).attr('data-value');
        if( value ){
          $('select', $el).val(value);
        }

      });

    }

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function() {
    
    $(document).on('click', '.mfn-visualbuilder .sidebar-panel-content .panel-edit-item .mfn-form .form-group.font-family-select', function() {
      mfnFieldFontSelect($(this));
    });

  });

})(jQuery);
