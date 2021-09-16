<?php
/**
 * Deactivation hooks
 *
 * @package           calculadora-helio
 * @author            Robert Ochoa
 * @copyright         2021 Robert Ochoa
 *
 * */

// FLUSH CUSTOM POST TYPE ======================================================
function calculadora_deactivate()
{
    unregister_post_type('medidas');
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'calculadora_deactivate');
