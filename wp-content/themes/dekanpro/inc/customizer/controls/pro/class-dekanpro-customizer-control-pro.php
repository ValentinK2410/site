<?php
if ( ! class_exists( 'Dekanpro_Customizer_Control_Pro' ) ) :
	class Dekanpro_Customizer_Control_Pro extends Dekanpro_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'dekanpro-pro';

		/**
		 * Pro features
		 *
		 * @since 1.1.1
		 */
		public $features = array();

		/**
		 * Pro theme screenshot
		 *
		 * @since 1.1.1
		 */

		 public $screenshot;

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

			// Script debug.
			$dekanpro_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Control type.
			$dekanpro_type = str_replace( 'dekanpro-', '', $this->type );

			/**
			 * Enqueue control stylesheet
			 */
			wp_enqueue_style(
				'dekanpro-' . $dekanpro_type . '-control-style',
				DEKANPRO_THEME_URI . '/inc/customizer/controls/' . $dekanpro_type . '/' . $dekanpro_type . $dekanpro_suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION,
				'all'
			);
		}

		public function to_json() {
			parent::to_json();
			$this->json['features']   = $this->features;
			$this->json['screenshot'] = $this->screenshot;
		}

		/**
		 * Render the content on the theme customizer page
		 */
		public function content_template() {?>

			<div class="upsell-btn" style="text-align: center;border-bottom: 3px solid #ddd;">                 
				<a style="margin: 0 auto 5px;display: inline-block;" href="https://dekan.pro/" target="blank" class="btn btn-success"><?php esc_html_e( 'Upgrade to DekanPro Pro', 'dekanpro' ); ?></a>
			</div>
			<# if ( data.screenshot ) {   #>
			<div style="padding: 1rem;background: #e6e6e6;">
				<img class="dekanpro_img_responsive " src="{{{ data.screenshot }}}" alt="<?php esc_attr_e( 'DekanPro Pro', 'dekanpro' ); ?>">
			</div>  
			<# }  #>       
			<div class="">
				<h3 style="margin-top:10px;padding: 10px;color:#111;font-size:16px;margin-bottom: 0;background: #fff;border-bottom: 1px solid #ddd;border-top: 3px solid #2271b1;"><?php esc_html_e( 'DekanPro Pro Features', 'dekanpro' ); ?></h3>
				<ul style="padding: 10px;background: #fff;">
					<# _.each(data.features, function(feature){ #>
						<li class="upsell-dekanpro"> <div class="dashicons dashicons-yes"></div> {{{ feature }}} </li>
					<# }); #>
				</ul>
			</div>
			<div class="upsell-btn" style="text-align: center;padding: 10px;background: #fff;">                 
				<a style="margin: 0 auto 5px;display: inline-block;" href="https://dekan.pro/" target="blank" class="btn btn-success"><?php esc_html_e( 'Upgrade to DekanPro Pro', 'dekanpro' ); ?></a>
			</div>
		   
			<p style="padding: 10px;background: #fff; margin-top: 0;">
				<?php
					printf( __( 'If you Like our Products , Please Rate us 5 star on %1$sWordPress.org%2$s.  We\'d really appreciate it! </br></br>  Thank You', 'dekanpro' ), '<a target="_blank" href="https://wordpress.org/support/view/theme-reviews/dekanpro?filter=5">', '</a>' );
				?>
			</p>
			<?php
		}
	}
endif;
