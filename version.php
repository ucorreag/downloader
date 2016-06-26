<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * downloader block is a bridge for downloader-school platform and moodle
 *
 * @package    block_downloader
 * @package block_downloader
 * @copyright  2016 UHO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin->version = 2016062211;    // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2013111600;        // Requires this Moodle version
$plugin->cron      = 7200;               // Period for cron to check this module (secs)
$plugin->component = 'block_downloader'; // To check on upgrade, that module sits in correct place
$plugin->release    = '1.0.0.1';
$plugin->maturity   = MATURITY_STABLE;