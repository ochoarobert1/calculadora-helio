<?php
/**
 * Activation hooks
 *
 * @package           calculadora-helio
 * @author            Robert Ochoa
 * @copyright         2021 Robert Ochoa
 *
 * */

// CREATE CUSTOM POST TYPE =====================================================
function calculadora_setup_post_type()
{
    // Register Custom Post Type
    $labels = array(
        'name'                  => _x('Medidas', 'Post Type General Name', 'calculadora-helio'),
        'singular_name'         => _x('Medida', 'Post Type Singular Name', 'calculadora-helio'),
        'menu_name'             => __('Medidas', 'calculadora-helio'),
        'name_admin_bar'        => __('Medidas', 'calculadora-helio'),
        'archives'              => __('Archivo de Medidas', 'calculadora-helio'),
        'attributes'            => __('Atributos de Medida', 'calculadora-helio'),
        'parent_item_colon'     => __('Medida Padre:', 'calculadora-helio'),
        'all_items'             => __('Todas las Medidas', 'calculadora-helio'),
        'add_new_item'          => __('Agregar Nueva Medida', 'calculadora-helio'),
        'add_new'               => __('Agregar Nueva', 'calculadora-helio'),
        'new_item'              => __('Nueva Medida', 'calculadora-helio'),
        'edit_item'             => __('Editar Medida', 'calculadora-helio'),
        'update_item'           => __('Actualizar Medida', 'calculadora-helio'),
        'view_item'             => __('Ver Medida', 'calculadora-helio'),
        'view_items'            => __('Ver Medidas', 'calculadora-helio'),
        'search_items'          => __('Buscar Medida', 'calculadora-helio'),
        'not_found'             => __('No hay resultados', 'calculadora-helio'),
        'not_found_in_trash'    => __('No hay resultados en Papelera', 'calculadora-helio'),
        'featured_image'        => __('Imagen Destacada', 'calculadora-helio'),
        'set_featured_image'    => __('Colocar Imagen Destacada', 'calculadora-helio'),
        'remove_featured_image' => __('Remover Imagen Destacada', 'calculadora-helio'),
        'use_featured_image'    => __('Usar como Imagen Destacada', 'calculadora-helio'),
        'insert_into_item'      => __('Insertar en Medida', 'calculadora-helio'),
        'uploaded_to_this_item' => __('Cargado a esta Medida', 'calculadora-helio'),
        'items_list'            => __('Listado de Medidas', 'calculadora-helio'),
        'items_list_navigation' => __('Navegación del Listado de Medidas', 'calculadora-helio'),
        'filter_items_list'     => __('Filtro del Listado de Medidas', 'calculadora-helio'),
    );
    $args = array(
        'label'                 => __('Medida', 'calculadora-helio'),
        'description'           => __('Medidas de Globos', 'calculadora-helio'),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-buddicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type('medidas', $args);
}

add_action('init', 'calculadora_setup_post_type');
 
// CREATE ACTIVATION HOOK ======================================================
function calculadora_activation_hook()
{
    calculadora_setup_post_type();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'calculadora_activation_hook');
