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

// CREATE HOOKS ON ACTIVATE / DEACTIVATE =======================================SA
require_once('inc/activation_hook.php');
require_once('inc/deactivation_hook.php');

// MAIN CLASS CONTAINER ========================================================