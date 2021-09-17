<?php
/**
 * Calculadora de helio
 *
 * @package           calculadora-helio
 * @author            Robert Ochoa
 * @copyright         2021 Robert Ochoa
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Calculadora de helio
 * Plugin URI:        https://robertochoaweb.com/calculadora-helio
 * Description:       Calculadora simple de helio para globos
 * Version:           1.0.0
 *
 * calculadora-helio is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * calculadora-helio is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with calculadora-helio. If not, see {URI to Plugin License}.
*/

// CREATE HOOKS ON ACTIVATE / DEACTIVATE =======================================
require_once('inc/activation_hook.php');
require_once('inc/deactivation_hook.php');

// MAIN CLASS CONTAINER ========================================================
class Calculadora
{
    // =========================================================================
    // MAIN CONSTRUCTOR
    // =========================================================================
    public function __construct()
    {
        // ADMIN HOOKS AND ACTIONS =============================================
        add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_action('save_post', array($this, 'saveMetaBox'));
        add_filter('manage_medidas_posts_columns', array($this, 'custom_filter_posts_columns'));
        add_action('manage_medidas_posts_custom_column', array($this, 'custom_medidas_column'), 10, 2);
        add_filter('manage_edit-medidas_sortable_columns', array($this, 'custom_medidas_sortable_columns'));
        add_action('pre_get_posts', array($this, 'custom_posts_orderby' ));

        // FRONTEND HOOKS AND ACTIONS ==========================================
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('init', array($this, 'calculadora_shortcode_init'));
        add_action('wp_ajax_add_calculadora_items', array($this, 'calculadora_items'));
        add_action('wp_ajax_nopriv_add_calculadora_items', array($this, 'calculadora_items'));
    }

    public function calculadora_items() {
        ob_start();
        ?>
<div class="calculadora-globos-input-item-group cloned">
    <div class="calculadora-input-item">
        <input type="number" min="1" value="1" name="cantidad_globos[]" placeholder="<?php _e('Ingrese la cantidad', 'calculadora-helio'); ?>" />
    </div>
    <div class="calculadora-input-item">
        <select name="modelo_globo[]" required>
            <option value="" disabled selected><?php _e('Seleccione un tipo de globo', 'calculadora-helio'); ?></option>
            <?php $arr_posts = new WP_Query(array('post_type' => 'medidas', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC')); ?>
            <?php if ($arr_posts->have_posts()) : ?>
            <?php while ($arr_posts->have_posts()) : $arr_posts->the_post(); ?>
            <option value="<?php echo get_post_meta(get_the_ID(), 'cantidad_helio', true); ?>"><?php echo get_the_title(); ?></option>
            <?php endwhile; ?>
            <?php endif; ?>
            <?php wp_reset_query(); ?>
        </select>
    </div>
    <div class="calculadora-input-item-button">
        <a class="deleteItems"><img src="<?php echo plugins_url('/svg/delete.svg', __FILE__); ?>" width="15" height="15" alt="Agregar" /></a>
    </div>
</div>
<?php
        $content = ob_get_clean();
        wp_send_json_success($content);
        wp_die();
    }

    // =========================================================================
    // ADD FRONTEND CUSTOM STYLES AND SCRIPTS
    // =========================================================================
    public function frontend_scripts()
    {
        /* GET MEDIDAS */
        $arr_medidas = array();
        $arr_posts = new WP_Query(array('post_type' => 'medidas', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
        if ($arr_posts->have_posts()) :
        while ($arr_posts->have_posts()) : $arr_posts->the_post();
        $arr_medidas[] = get_the_title() . ',' . get_post_meta(get_the_ID(), 'cantidad_helio', true);
        endwhile;
        endif;
        wp_reset_query();

        /*- MAIN FUNCTIONS -*/
        wp_register_script('calculadora-functions', plugins_url('/js/functions.js', __FILE__), array('jquery'), array(), true);
        wp_enqueue_script('calculadora-functions');
 
        /* LOCALIZE MAIN SHORTCODE SCRIPT */
        wp_localize_script('calculadora-functions', 'custom_admin_url', array(
             'ajax_url' => admin_url('admin-ajax.php'),
             'medidas_ajax' => json_encode($arr_medidas)
        ));

        /*- MAIN STYLE -*/
        wp_register_style('calculadora-style', plugins_url('/css/calculadora-style.css', __FILE__), false, array(), 'all');
        wp_enqueue_style('calculadora-style');
    }

    // =========================================================================
    // ADD CUSTOM ADMIN COLUMN
    // =========================================================================
    public function custom_filter_posts_columns($columns)
    {
        unset($columns['date']);
        $columns['cantidad_helio'] = __('Cantidad de Helio', 'calculadora-helio');
        $columns['date'] = __('Fecha', 'wordpress');
        return $columns;
    }

    // =========================================================================
    // ADD SORTABLE BEHAVIOUR TO COLUMN
    // =========================================================================
    public function custom_medidas_sortable_columns($columns)
    {
        $columns['cantidad_helio'] = 'cantidad_helio';
        return $columns;
    }

    // =============================================================================
    // ADD PRE GET POST TO SORTABLE ADMIN COLUMN
    // =============================================================================
    public function custom_posts_orderby($query)
    {
        if (! is_admin() || ! $query->is_main_query()) {
            return;
        }
      
        if ('cantidad_helio' === $query->get('orderby')) {
            $query->set('orderby', 'meta_value');
            $query->set('meta_key', 'cantidad_helio');
            $query->set('meta_type', 'numeric');
        }
    }
      
    // =========================================================================
    // ADD VALUE TO CUSTOM ADMIN COLUMN
    // =========================================================================
    public function custom_medidas_column($column, $post_id)
    {
        // Image column
        if ('cantidad_helio' === $column) {
            $cantidad = get_post_meta($post_id, 'cantidad_helio', true);

            if ($cantidad != '') {
                echo number_format($cantidad, 3, ',', '.') . ' m3';
            } else {
                _e('Cantidad no especificada', 'calculadora-helio');
            }
        }
    }

    // =========================================================================
    // ADD METABOX FOR MEASURES POST TYPE
    // =========================================================================
    public function addMetaBox()
    {
        add_meta_box(
            'medidas_meta_box',
            esc_html__('Información Extra', 'calculadora-helio'),
            array($this, 'htmlMetaBox'),
            array('medidas')
        );
    }

    // =========================================================================
    // SAVE METABOX FOR MEASURES POST TYPE
    // =========================================================================
    public function saveMetaBox($post_id)
    {
        if (array_key_exists('cantidad_helio', $_POST)) {
            update_post_meta(
                $post_id,
                'cantidad_helio',
                $_POST['cantidad_helio']
            );
        }
    }

    // =========================================================================
    // HTML METABOX FOR MEASURES POST TYPE
    // =========================================================================
    public function htmlMetaBox($post)
    {
        $value = get_post_meta($post->ID, 'cantidad_helio', true); ?>
<label for="cantidad_helio"><?php _e('Cantidad de helio necesario: (Utilice sólo numeros)', 'calculadora-helio'); ?></label>
<input type="text" name="cantidad_helio" id="cantidad_helio" class="postbox" style="margin: 5px 0;" value="<?php echo $value; ?>" />
<?php
    }

    public function calculadora_shortcode_init()
    {
        add_shortcode('calculadora_shortcode', array($this, 'calculadora_shortcode'));
    }

    public function calculadora_shortcode($atts = [], $content = null)
    {
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with user attributes
        $wporg_atts = shortcode_atts(
            array(
            'title' => 'WordPress.org',
        ),
            $atts,
            $tag
        );

        ob_start(); ?>
<form id="calculadoraForm" class="calculadora-globos-container">
    <div id="calculadoraWrapper" class="calculadora-globos-input-content">
        <div id="calculadoraItem1" class="calculadora-globos-input-item-group">
            <div class="calculadora-input-item">
                <label for="cantidad_globos"><?php _e('Cantidad de Globos', 'calculadora-helio'); ?></label>
                <input type="number" value="1" min="1" name="cantidad_globos[]" placeholder="<?php _e('Ingrese la cantidad', 'calculadora-helio'); ?>" />
            </div>
            <div class="calculadora-input-item">
                <label for="modelo_globo"><?php _e('Modelo de Globo', 'calculadora-helio'); ?></label>
                <select name="modelo_globo[]" required>
                    <option value="" disabled selected><?php _e('Seleccione un tipo de globo', 'calculadora-helio'); ?></option>
                    <?php $arr_posts = new WP_Query(array('post_type' => 'medidas', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC')); ?>
                    <?php if ($arr_posts->have_posts()) : ?>
                    <?php while ($arr_posts->have_posts()) : $arr_posts->the_post(); ?>
                    <option value="<?php echo get_post_meta(get_the_ID(), 'cantidad_helio', true); ?>"><?php echo get_the_title(); ?></option>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <?php wp_reset_query(); ?>
                </select>
            </div>
            <div class="calculadora-input-item-button">
                <a id="addItems"><img src="<?php echo plugins_url('/svg/add.svg', __FILE__); ?>" width="15" height="15" alt="Agregar" /></a>
            </div>
        </div>
    </div>
    <div class="calculadora-globos-results-content">
        <button id="calculadoraBtn" class="calculadora-btn" type="submit" title="<?php _e('Haga click aqui para calcular la cantidad de Helio', 'calculadora-helio'); ?>"><?php _e('Calcular cantidad de Helio', 'calculadora-helio'); ?></button>
        <div class="calculadora-globos-results">
            <span><?php _e('Total:', 'calculadora-helio'); ?></span>
            <span id="calculadoraNumber"><?php _e('0', 'calculadora-helio'); ?></span>
            <span><?php _e('m3', 'calculadora-helio'); ?></span>
        </div>
        <small id="calculadoraError" class="calculadora-error no-display"><?php _e('Debe seleccionar el modelo del globo en las opciones', 'calculadora-helio'); ?></small>
    </div>
</form>
<?php
        $content = ob_get_clean();
        // return output
        return $content;
    }
}

new Calculadora;