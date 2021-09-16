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
        add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_action('save_post', array($this, 'saveMetaBox'));
        add_filter('manage_medidas_posts_columns', array($this, 'custom_filter_posts_columns'));
        add_action('manage_medidas_posts_custom_column', array($this, 'custom_medidas_column'), 10, 2);
        add_filter('manage_edit-medidas_sortable_columns', array($this, 'custom_medidas_sortable_columns'));
        add_action('pre_get_posts', array($this, 'custom_posts_orderby' ));
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
                echo number_format($cantidad, 2, ',', '.') . ' m3';
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
}

new Calculadora;
