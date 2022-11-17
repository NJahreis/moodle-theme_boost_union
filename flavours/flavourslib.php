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
 * Theme Boost Union - Flavours library
 *
 * @package    theme_boost_union
 * @copyright  2022 Moodle an Hochschulen e.V. <kontakt@moodle-an-hochschulen.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Helper function to check if any and which flavour should be applied to this page output.
 *
 * It iterates over the existing flavours from top to bottom, checks each flavour if it applies to the given page output
 * and stops as soon as a particular flavour applies.
 *
 * @return stdClass|null The flavour object if any flavour applies, otherwise null.
 */
function theme_boost_union_get_flavour_which_applies() {
    global $CFG, $DB, $PAGE, $USER;

    // Security net:
    // This function is called from every Moodle page.
    // BUT: If the plugin is not properly installed or updated yet, we must not access any database table
    // as this would trigger a "Read from database" error.
    if (get_config('theme_boost_union', 'version') < 2022080915) {
        return null;
    }

    // TODO Currently this function is a) called multiple times per page load and b) iterates over all existing flavours
    // TODO We have to implement a static cache to ease multiple calls of this function.
    // TODO And we have to implement a mechanism which lets us query the applied flavour by a given user ID and course category ID.

    // Require cohort library.
    require_once($CFG->dirroot.'/cohort/lib.php');

    // Get all flavours from the DB.
    $flavours = $DB->get_records('theme_boost_union_flavours', array(), 'sort ASC');

    // If we are on the preview page.
    $previewurl = new moodle_url('/theme/boost_union/flavours/preview.php');
    if ($previewurl->compare($PAGE->url, URL_MATCH_BASE) == true) {
        // Get the flavour from the URL.
        $previewflavourid = required_param('id', PARAM_INT);

        // Force the given flavour.
        return $flavours[$previewflavourid];
    }

    // Initialize static variable to hold the page category ID for category comparisons.
    static $pagecategoryid;

    // If the page has a category ID.
    if ($PAGE->category != null && $PAGE->category->id != null) {

        // If $pagecategoryid hasn't been filled yet.
        if ($pagecategoryid == null) {
            // Remember the page category ID.
            $pagecategoryid = $PAGE->category->id;
        }

        // Initialize static variable to hold the page's parent IDs for category comparisons.
        static $parentcategoryids;

        // If $parentcategoryids hasn't been filled yet.
        if ($parentcategoryids == null) {
            // Get the page category from the category manager and accept that it may not be found.
            $pagecategory = \core_course_category::get($pagecategoryid, IGNORE_MISSING);
            // If we got a valid category.
            if ($pagecategory != null) {
                // Pick the category path without the leading slash.
                $pagecategorypath = substr($pagecategory->path, 1);
                // Fill the parent ID array..
                $parentcategoryids = explode('/', $pagecategorypath);

                // Otherwise.
            } else {
                // Just remember an empty array to avoid breaking the following code.
                $parentcategoryids = array();
            }
        }
    }

    // Initialize static variables to hold the user's cohorts for cohort comparisons.
    static $usercohorts, $userhascohorts;

    // If $usercohorts hasn't been filled yet.
    if ($usercohorts == null) {
        // Get and remember the user's cohorts.
        $usercohorts = cohort_get_user_cohorts($USER->id);
        // Remember the user's cohorts.
        $userhascohorts = !empty($usercohorts);
    }

    // Iterate over the flavours.
    foreach ($flavours as $f) {
        // If the flavour is configured to apply to categories and this page has a category id.
        if ($f->applytocategories == true && $pagecategoryid != null) {
            // Decode the configured categories.
            $categoryids = json_decode($f->applytocategories_ids);

            // If at least one category is configured.
            if (!empty($categoryids)) {
                // Iterate over the configured categories.
                foreach ($categoryids as $c) {
                    // If the flavour is configured to apply to the page's category.
                    if ($c == $pagecategoryid) {
                        // Return the flavour object as we have found a match.
                        return $f;
                    }

                    // If the flavour is configured to include all subcategories and the category at hand is in the list of
                    // the page's parent categories.
                    if ($f->applytocategories_subcats == true && in_array($c, $parentcategoryids)) {
                        // Return the flavour object as we have found a match.
                        return $f;
                    }
                }
            }
        }

        // If the flavour is configured to apply to cohorts and this user has cohorts.
        if ($f->applytocohorts == true && $userhascohorts == true) {
            // Decode the configured cohorts.
            $cohortids = json_decode($f->applytocohorts_ids);

            // If at least one cohort is configured.
            if (!empty($cohortids)) {
                // If the user is a member of one of these cohorts.
                if (theme_boost_union_flavours_cohorts_is_member($USER->id, $cohortids) == true) {
                    // Return the flavour object as we have found a match.
                    return $f;
                }
            }
        }
    }

    // If we haven't found any flavour which applies to the given page output.
    return null;
}

/**
 * Helper function which gets the filename of the uploaded if to a given flavour filearea with the given itemid.
 * @param string $filearea The filearea (without the 'flavours_' prefix).
 * @param int $itemid The item id within the filearea.
 *
 * @return string|null The filename, if a file was uploaded, or null, if no file was uploaded to the filearea.
 */
function theme_boost_union_flavours_get_filename($filearea, $itemid) {
    // Get system context.
    $context = context_system::instance();

    // Get file storage.
    $fs = get_file_storage();

    // Get all files from the given filearea.
    $files = $fs->get_area_files($context->id, 'theme_boost_union', 'flavours_'.$filearea, $itemid,
            'sortorder,filepath,filename', false);
    if ($files) {
        // Just pick the first file - we are sure that there is just one file.
        $file = reset($files);
        // Get the file name.
        $filename = $file->get_filename();
    } else {
        $filename = null;
    }

    // Return the file name.
    return $filename;
}


/**
 * Helper function which checks if a user is a member of the given cohorts.
 * @param int $userid
 * @param array $cohorts
 *
 * @return bool
 */
function theme_boost_union_flavours_cohorts_is_member($userid, $cohorts) {
    global $DB;

    if (!empty($cohorts)) {
        // Create IN statement for cohorts.
        list($in, $params) = $DB->get_in_or_equal($cohorts);
        // Add param for userid.
        $params[] = $userid;
        // Return true if "userid = " . $userid . " AND cohortid IN " . $cohorts.
        return $DB->record_exists_select('cohort_members', "cohortid $in AND userid = ?", $params);
    } else {
        return false;
    }
}
