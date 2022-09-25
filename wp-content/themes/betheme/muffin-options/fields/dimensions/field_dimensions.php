<?php
class MFN_Options_dimensions extends Mfn_Options_field
{

	/**
	 * Render
	 */

	public function render( $meta = false, $vb = false )
	{
		$class = '';
		$pseudo = false;
		$value = '';
		$explode_val = false;
		$readonly = false;
		$input_class = false;

 		// inputs

    if( isset($this->field['version']) ){
    	$inputs = [
	      'top' => '',
	      'right' => '',
	      'bottom' => '',
	      'left' => ''
	    ];
    } else {
    	$inputs = [
	      'top', 'right', 'bottom', 'left'
	    ];
    }

    if( strpos($this->field['id'], 'border-radius') !== false ){
    	$inputs = [
	      '&#8598;',
	      '&#8599;',
	      '&#8600;',
	      '&#8601;'
	    ];
    }

    if ( $this->value ) {
			$value = $this->value;
			if( is_array($value) ){
				$explode_val = $value; // tu wrócić
			}else{
				$explode_val = explode(' ', $value);
			}
		} elseif( ! empty( $this->field['std'] ) ) {
			$value = $this->field['std'];
		} else {
			$value = '';
		}

		if($explode_val && (count($explode_val) == 4) && (count(array_unique($explode_val)) == 1) ){
			$class .= ' isLinked';
		}

		if(isset($this->field['version'])){
			$class .= ' '. $this->field['version'];
		} else {
			$class .= ' pseudo';
		}

		// output -----

		echo '<div class="form-group multiple-inputs has-addons has-addons-append '. esc_attr( $class ) .'">';

			echo '<div class="form-control">';

				// pseudo field

				if( !isset($this->field['version']) ) {
					echo '<input class="pseudo-field mfn-form-control mfn-field-value" type="hidden" '. $this->get_name( $meta ) .' value="'. $value .'" autocomplete="off"/>';
				}

				foreach( $inputs as $i=>$input ){

					isset($explode_val[$i]) ? $sub_value = $explode_val[$i] : $sub_value = '';

					$input_class = 'field-'.esc_attr( $i );

					if(isset($this->field['version'])){
						$input_class .= ' mfn-field-value';
					}

					if( 'top' != $i && '&#8598;' != $i ){

						$in_class = 'disableable';

						if($explode_val && (count($explode_val) == 4) && (count(array_unique($explode_val)) == 1) ){
							$readonly = 'readonly="readonly"';
							$input_class .= ' readonly';
						}

					} else {
						$in_class = false;
					}

					echo '<div class="field '. esc_attr( $in_class ) .'" data-key="'. ( isset($this->field['version']) ? esc_attr( $i ) : $inputs[$i] ) .'">';
						echo '<input '. ( isset($this->field['version']) && $this->field['version'] == 'separated-fields' ? $this->get_name( $meta, $i ) : null ) .' type="text" class="mfn-form-control mfn-form-input numeral  '. esc_attr( $input_class ) .'" data-key="'. esc_attr( $i ) .'" value="'. esc_attr( isset($sub_value) ? $sub_value : '' ) .'" '. $readonly .' autocomplete="off" placeholder="" />';
					echo '</div>';

				}

			echo '</div>';

			echo '<div class="form-addon-append">';
				echo '<a href="#" class="link">';
					echo '<span class="label"><i class="icon-link"></i></span>';
				echo '</a>';
			echo '</div>';
		echo '</div>';

	}

	/**
	 * Enqueue Function.
	 */

	public function enqueue()
	{
		wp_enqueue_script( 'mfn-field-dimensions', MFN_OPTIONS_URI .'fields/dimensions/field_dimensions.js', array( 'jquery' ), MFN_THEME_VERSION, true );
	}

}
