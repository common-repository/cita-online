<?php
/*
Plugin Name: Cita Online
Plugin URI: http://wordpress.org/extend/plugins/cita-online/
Description: Módulo de cita online donde los pacientes podrán pedir una cita desde la página web.
Version: 1.0
Author: Infomed Software
Author URI: http://grupoinfomed.es
License: GPL2
*/


/*
*   Registramos opciones y el menú
*/
function register_cita_online_settings() {
	register_setting( 'cita-online-settings-group', 'option_apikey' );
	register_setting( 'cita-online-settings-group', 'option_mostrar' );
}
function cita_online_create_menu() {
	add_menu_page('Cita Online Opciones', 'Cita Online', 'administrator', __FILE__, 'cita_online_settings_page' , 'dashicons-calendar-alt' );
	add_action( 'admin_init', 'register_cita_online_settings' );
}
add_action('admin_menu', 'cita_online_create_menu');

/*
*   Diseño formulario de las opciones de Admin Panel
*/
function cita_online_settings_page() {
?>
<div class="wrap">
<h1>Cita Online</h1>
<form method="post" action="options.php">
    <?php settings_fields( 'cita-online-settings-group' ); ?>
    <?php do_settings_sections( 'cita-online-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">API KEY</th>
            <td><input type="text" name="option_apikey" style="width:300px;"value="<?php echo esc_attr( get_option('option_apikey') ); ?>" />
            <div><span class="description">La API KEY es la clave que identifica al centro. Es proporcionada por Infomed Software. </span></div>
        </td>            
        </tr>
        <tr valign="top">
            <th scope="row">Mostrar Cita Online</th>
            <td> <select name="option_mostrar" >
                    <option value="1" <?php selected( esc_attr( get_option('option_mostrar') ), 1 ); ?>>Si</option>
                    <option value="0" <?php selected( esc_attr( get_option('option_mostrar') ), 0 ); ?>>No</option>
                </select> 
                <div><span class="description">Selecciona <strong>Si</strong> para mostrar el módulo.</span></div> 
               <div><span class="description">Selecciona <strong>No</strong> para ocultar el módulo.</span></div>               
            </td>
        </tr> 
    </table>
    <?php submit_button(); ?>
</form>
</div>
<?php } 

/*
 *   Convertimos shortcode [cita-online]
*/
function shortcode_cita_online() {
    return '
    <div class="art-post-body">
        <iframe name="CAOInst1_IFrame" id="CAOInst1_IFrame" class="AOFrame"></iframe>
    </div>
    ';
}
add_shortcode('cita-online', 'shortcode_cita_online');

/*
*   Registramos CSS y JS
*/
function cita_online_scripts() {    
    global $post;
    if( get_option('option_mostrar') == 1 && has_shortcode( $post->post_content, 'cita-online') ) {
        wp_enqueue_script('jquery'); 

        /*
            Adjuntamos librería JS y aplicamos code en página.
        */
        wp_register_script( 'cita_online_agendaonline_js', 'https://secure.infomed.es/ClienteHTML/js/infomed.agendaonline-1.0.1-wp.js',array( 'jquery' ), '1.0', true);
        wp_add_inline_script('cita_online_agendaonline_js','
        var AgendaOnlineIniciar = function($){
            var APIKEY = "'.get_option("option_apikey").'";
            var aoObject = ClienteAgendaOnline(APIKEY, ".art-post-body", {
                "clearTargetContent" : true,
            })}
            AgendaOnlineIniciar()
        ;');
        wp_enqueue_script('cita_online_agendaonline_js');
        /*
            Registramos css para el iframe
        */
        wp_register_style( 'cita_online_css', plugins_url('css/wp-agendaonline.css', __FILE__) );
        wp_enqueue_style( 'cita_online_css' );
    }
}
add_action( 'wp_enqueue_scripts', 'cita_online_scripts' );

?>

