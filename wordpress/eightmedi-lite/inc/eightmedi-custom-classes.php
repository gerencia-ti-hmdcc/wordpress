<?php
//add new custom control type switch
if(class_exists( 'WP_Customize_control')):
	class Eightmedi_Lite_WP_Customize_Category_Control extends WP_Customize_Control {
        /**
         * Render the control's content.
         *
         * @since 3.4.0
         */
        public function render_content() {
        	$dropdown = wp_dropdown_categories(
        		array(
        			'name'              => '_customize-dropdown-categories-' . $this->id,
        			'echo'              => 0,
        			'show_option_none'  => __( '&mdash; Select &mdash;','eightmedi-lite' ),
        			'option_none_value' => '0',
                    'value_field'       => 'slug',
        			'selected'          => $this->value(),
        			)
        		);
        	
            // Hackily add in the data link parameter.
        	$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
        	
        	printf(
                /* translators: 1: label, 2: dropdown. */
        		'<label class="customize-control-select"><span class="customize-control-title">%1$s</span> %2$s</label>',
        		esc_html($this->label),
        		wp_kses($dropdown,array('select'=>array('name'=>array(),'class'=>array(),'id'=>array(),'data-customize-setting-link'=>array()),'option'=>array('class'=>array(),'value'=>array(),'selected'=>array())))
        		);
        }
    }
    endif;