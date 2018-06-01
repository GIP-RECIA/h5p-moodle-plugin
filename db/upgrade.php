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
 * Upgrade definitions for the hvp module.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds data for tracking when content was created and last modified.
 */
function hvp_upgrade_2016011300() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Define field timecreated to be added to hvp.
    $timecreated = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'slug');

    // Conditionally launch add field timecreated.
    if (!$dbman->field_exists($table, $timecreated)) {
        $dbman->add_field($table, $timecreated);
    }

    // Define field timemodified to be added to hvp.
    $timemodified = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

    // Conditionally launch add field timemodified.
    if (!$dbman->field_exists($table, $timemodified)) {
        $dbman->add_field($table, $timemodified);
    }
}

/**
 * Adds table for keeping track of, and cleaning up temporary files
 */
function hvp_upgrade_2016042500() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define table hvp_tmpfiles to be created.
    $table = new xmldb_table('hvp_tmpfiles');

    // Adding fields to table hvp_tmpfiles.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_tmpfiles.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for hvp_tmpfiles.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds events table
 */
function hvp_upgrade_2016050600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define table hvp_events to be created.
    $table = new xmldb_table('hvp_events');

    // Adding fields to table hvp_events.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('sub_type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('content_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('content_title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_name', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_version', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_events.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for hvp_events.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define table hvp_counters to be created.
    $table = new xmldb_table('hvp_counters');

    // Adding fields to table hvp_counters.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_name', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_version', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);
    $table->add_field('num', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_counters.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes to table hvp_counters.
    $table->add_index('realkey', XMLDB_INDEX_NOTUNIQUE, [
        'type',
        'library_name',
        'library_version',
    ]);

    // Conditionally launch create table for hvp_counters.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds intro and introformat to hvp table
 */
function hvp_upgrade_2016051000() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Define field intro to be added to hvp.
    $intro = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

    // Add field intro if not defined already.
    if (!$dbman->field_exists($table, $intro)) {
        $dbman->add_field($table, $intro);
    }

    // Define field introformat to be added to hvp.
    $introformat = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'intro');

    // Add field introformat if not defined already.
    if (!$dbman->field_exists($table, $introformat)) {
        $dbman->add_field($table, $introformat);
    }
}

/**
 * Changes context of activity files to enable backup an restore.
 */
function hvp_upgrade_2016110100() {
    global $DB;

    // Change context of activity files from COURSE to MODULE.
    $filearea  = 'content';
    $component = 'mod_hvp';

    // Find activity ID and correct context ID.
    $hvpsresult = $DB->get_records_sql(
        "SELECT f.id AS fileid, f.itemid, c.id, f.filepath, f.filename, f.pathnamehash
                   FROM {files} f
                   JOIN {course_modules} cm ON f.itemid = cm.instance
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {context} c ON c.instanceid = cm.id
                  WHERE md.name = 'hvp'
                    AND f.filearea = 'content'
                    AND c.contextlevel = " . CONTEXT_MODULE
    );

    foreach ($hvpsresult as $hvp) {
        // Need to re-hash pathname after changing context.
        $pathnamehash = file_storage::get_pathname_hash($hvp->id,
            $component,
            $filearea,
            $hvp->itemid,
            $hvp->filepath,
            $hvp->filename
        );

        // Double check that hash doesn't exist (avoid duplicate entries).
        if (!$DB->get_field_sql("SELECT contextid FROM {files} WHERE pathnamehash = '{$pathnamehash}'")) {
            // Update context ID and pathname hash for files.
            $DB->execute("
                  UPDATE {files}
                  SET contextid = {$hvp->id},
                      pathnamehash = '{$pathnamehash}'
                  WHERE pathnamehash = '{$hvp->pathnamehash}'"
            );
        }
    }
}

/**
 * Notifies about breaking changes to H5P content type styling
 */
function hvp_upgrade_2016122800() {
    // @codingStandardsIgnoreLine
    \mod_hvp\framework::messages('info', '<span style="font-weight: bold;">Upgrade your H5P content types!</span> Old content types will still work, but the authoring tool will look and feel much better if you <a href="https://h5p.org/update-all-content-types">upgrade the content types</a>.');
    \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
}

/**
 * Adds content type cache to enable the content type hub
 */
function hvp_upgrade_2017040500() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add content type cache database.
    $table = new xmldb_table('hvp_libraries_hub_cache');

    // Adding fields to table hvp_libraries_hub_cache.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('machine_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('major_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('minor_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('patch_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('h5p_major_version', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
    $table->add_field('h5p_minor_version', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('summary', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('icon', XMLDB_TYPE_CHAR, '511', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('is_recommended', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('popularity', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('screenshots', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('license', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('example', XMLDB_TYPE_CHAR, '511', null, XMLDB_NOTNULL, null, null);
    $table->add_field('tutorial', XMLDB_TYPE_CHAR, '511', null, null, null, null);
    $table->add_field('keywords', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('categories', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('owner', XMLDB_TYPE_CHAR, '511', null, null, null, null);

    // Adding keys to table hvp_libraries_hub_cache.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally create table for hvp_libraries_hub_cache.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Update the content type cache.
    $core = \mod_hvp\framework::instance();
    $core->updateContentTypeCache();

    // Print messages.
    \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
    \mod_hvp\framework::printMessages('error', \mod_hvp\framework::messages('error'));

    // Add has_icon to libraries folder.
    $table = new xmldb_table('hvp_libraries');

    // Define field has_icon to be added to hvp_libraries.
    $hasicon = new xmldb_field('has_icon', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    // Add field has_icon if it does not exist.
    if (!$dbman->field_exists($table, $hasicon)) {
        $dbman->add_field($table, $hasicon);
    }

    // Display hub communication info.
    if (!get_config('mod_hvp', 'external_communication')) {
        // @codingStandardsIgnoreLine
        \mod_hvp\framework::messages('info', 'H5P now fetches content types directly from the H5P Hub. In order to do this, the H5P plugin will communicate with H5P.org once per day to fetch information about new and updated content types. It will send in anonymous data to the hub about H5P usage. You may disable the data contribution and/or the H5P Hub in the H5P settings.');
        \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
    }

    // Enable hub and delete old communication variable.
    set_config('hub_is_enabled', true, 'mod_hvp');
    unset_config('hub_is_enabled', 'mod_hvp');
}

/**
 * Adds xAPI results table to enable reporting
 */
function hvp_upgrade_2017050900() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add report rendering.
    $table = new xmldb_table('hvp_xapi_results');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('content_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('parent_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('interaction_type', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('correct_responses_pattern', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('response', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('additionals', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    // Add keys and index.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('result', XMLDB_INDEX_UNIQUE, [
        'id',
        'content_id',
        'user_id',
    ]);

    // Create table if it does not exist.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds raw score and max score to xapi results table
 */
function hvp_upgrade_2017060900() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add score to report rendering.
    $table = new xmldb_table('hvp_xapi_results');

    if ($dbman->table_exists($table)) {
        // Raw score field.
        $scorefield = new xmldb_field('raw_score', XMLDB_TYPE_INTEGER, '6');
        if (!$dbman->field_exists($table, $scorefield)) {
            $dbman->add_field($table, $scorefield);
        }

        // Max score field.
        $maxscorefield = new xmldb_field('max_score', XMLDB_TYPE_INTEGER, '6');
        if (!$dbman->field_exists($table, $maxscorefield)) {
            $dbman->add_field($table, $maxscorefield);
        }
    }
}

function hvp_upgrade_2018090300() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Remove old, unused metadata fields.
    if ($dbman->field_exists($table, 'author')) {
        $dbman->drop_field($table, new xmldb_field('author'));
    }

    if ($dbman->field_exists($table, 'license')) {
        $dbman->drop_field($table, new xmldb_field('license'));
    }

    if ($dbman->field_exists($table, 'meta_keywords')) {
        $dbman->drop_field($table, new xmldb_field('meta_keywords'));
    }

    if ($dbman->field_exists($table, 'meta_description')) {
        $dbman->drop_field($table, new xmldb_field('meta_description'));
    }

    // Create new metadata fields.
    if (!$dbman->field_exists($table, 'authors')) {
        $dbman->add_field($table,
            new xmldb_field('authors', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'source')) {
        $dbman->add_field($table,
            new xmldb_field('source', XMLDB_TYPE_CHAR, '255', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'year_from')) {
        $dbman->add_field($table,
            new xmldb_field('year_from', XMLDB_TYPE_INTEGER, '4', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'year_to')) {
        $dbman->add_field($table,
            new xmldb_field('year_to', XMLDB_TYPE_INTEGER, '4', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license')) {
        $dbman->add_field($table,
            new xmldb_field('license', XMLDB_TYPE_CHAR, '63', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license_version')) {
        $dbman->add_field($table,
            new xmldb_field('license_version', XMLDB_TYPE_CHAR, '15', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'changes')) {
        $dbman->add_field($table,
            new xmldb_field('changes', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license_extras')) {
        $dbman->add_field($table,
            new xmldb_field('license_extras', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'author_comments')) {
        $dbman->add_field($table,
            new xmldb_field('author_comments', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    // Add new libraries fields.
    $table = new xmldb_table('hvp_libraries');
    if (!$dbman->field_exists($table, 'add_to')) {
        $dbman->add_field($table,
            new xmldb_field('add_to', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'metadata_settings')) {
        $dbman->add_field($table,
            new xmldb_field('metadata_settings', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }
}


/**
 * Adds authentication table
 *
 * @throws ddl_exception
 */
function hvp_upgrade_2019022600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add auth table.
    $table = new xmldb_table('hvp_auth');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('secret', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);

    // Add keys and index.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('user_id', XMLDB_INDEX_UNIQUE, ['user_id']);

    // Create table if it does not exist.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Add default language to content
 *
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 */
function hvp_upgrade_2019030700() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    if (!$dbman->field_exists($table, 'default_language')) {
        $dbman->add_field($table,
            new xmldb_field('default_language', XMLDB_TYPE_CHAR, '32', null, null, null, null)
        );
    }
}

/**
 * Adds raw score and max score to xapi results table
 */
function hvp_upgrade_2018060100() {
    global $DB;

    $translations = array(
        'H5P.Audio' => array(
            "sumary" => "Télachargez un enregistrement audio",
            "description" => "Téléchargez un enregistrement audio en .mp3, .wav, .ogg ou fournissez le lien d'un enregistrement audio."
        ),
        'H5P.AudioRecorder' => array(
            "title" => "Enregistrement audio",
            "sumary" => "Créez un enregistrement audio",
            "description" => "Un enregistreur audio HTML5. Enregistrez-vous et écoutez-vous ou téléchargez un fichier .wav de votre enregistrement."
        ),
        'H5P.CoursePresentation' => array(
            "title" => "Présentation de cours",
            "sumary" => "Créez une présentation avec un diaporama interactif",
            "description" => "Les présentations de cours comprennent des diapositives qui incluent du multimédia, du texte et différents types d'interractions comme des résumés interactifs, des questions à choix multiple et des vidéos interactives. Les élèves peuvent découvrir de nouvelles méthodes d'apprentissage et tester leurs connaissances et leur mémoire. Comme toujours avec H5P, le contenu est éditable dans les navigateurs web et la présentation de cours inclue un outils de création WYSIWYG. Une utilisation typique de la présentation de cours consiste à présenter le sujet sur quelques diapositives et de les faire suivre par des diapositives qui permettront à l'usager de tester ses connaissances. La présentation de cours peut cependant être utilisée de plein de façons différentes, comme outil de présentation en classe ou comme un jeu en utilisant les boutons de navigation dans les diapositives pour permettre à l'utilisateur de faire des choix et d'en apprécier les conséquences",
        ),
        'H5P.Dialogcards' => array(
            "title" => "Cartes de dialogue",
            "sumary" => "Créez des cartes tournantes basées sur des textes",
            "description" => "Les cartes de dialogue peuvent aider les apprenants à mémoriser des mots, des expressions ou des souvenirs. Sur le recto de la carte, il y a un indice correspondant à un mot ou une expression. En tournant la carte, l'apprenant révèle ce mot ou cette expression. Les cartes de dialogue peuvent être utilisées pour les langues, les mathématiques, l'histoire, etc...",
        ),
        'H5P.DragQuestion' => array(
            "title" => "Glisser-Déposer",
            "sumary" => "Créez des des glisser-déposer sur des images",
            "description" => "Le glisser-déposer permet d'associer 2 éléments ou plus afin de réaliser visuellement des connexions logiques. Créez des exercices de glisser-déposer en utilisant du texte et/ou des images qui pourront être déplacés pour trouver la solution. Glisser-Déposer prend en charge les relations un à un, un à plusieurs, plusieurs à un et plusieurs à plusieurs entrer les questions et les réponses."
        ),
        'H5P.DragText' => array(
            "title" => "Remplir les vides ",
            "sumary" => "Créez des textes avec des mots manquant ",
            "description" => "L'apprenant saisira les mots qui manquent dans un texte. Il saura si sa réponse est la bonne après chaque saisie ou après avoir saisi tous les mots, en fonction du paramétrage de l'exercice. Les auteurs saisissent le texte et marquent les mots à remplacer avec des astérisques. Les exercices créés peuvent être utilisés dans tous les domaines d'apprentissage : langues et grammaire, mathématiques, géographie, histoire, etc... "
        ),
        'H5P.ImageMultipleHotspotQuestion' => array(
            "title" => "Hotspots Multiples",
            "sumary" => "Créez plusieurs points que les utilisateurs devront trouver sur une image",
            "description" => "Hotspots Multiples permet aux enseignants de créer un exercice basé sur une image. Les apprenants devront trouver, de façon très ludique, les points qui correspondent à la question posée."
        ),
        'H5P.ImageHotspotQuestion' => array(
            "title" => "Hotspot",
            "sumary" => "Créer un point sur une image que les utilisateurs devront retrouver",
            "description" => "Hotspot  permet aux utilisateurs de répondre à une question en cliquant sur un élément d'une image. L'enseignant télécharge une image et définit différents points correspondant à des détails ou des sections de l'image. Les points peuvent être définis comme corrects ou incorrects, avec un commentaire approprié qui s'affiche lorsque l'apprenant clique dessus."
        ),
        'H5P.GuessTheAnswer' => array(
            "title" => "Devinez la réponse",
            "sumary" => "Créez une question et une réponse associées à une image",
            "description" => "Ce type d'exercice permet aux enseignants de télécharger une image et d'y associer une question. Les apprenants peuvent deviner la réponse et appuyer sur un bouton pour vérifier que leur réponse est correcte. C'est un exercice qui permet d'effectuer des révisions."
        ),
        'H5P.ImageJuxtaposition' => array(
            "title" => "Juxtaposition d'images",
            "sumary" => "Comparez deux images de manière interactive",
            "description" => "Juxtaposition d'images permet aux utilisateurs de comparer deux images de façon interactive, comme par exemple avant et après un événement."
        ),
        'H5P.ImageSlider' => array(
            "title" => "Carrousel",
            "sumary" => "Créez facilement un carrousel d'images",
            "description" => "Présentez vos images facilement sous forme de carrousel (diaporama). L'enseignant télécharge des images et fournie des commentaires pour ces images. Les 2 images qui suivent l'image affichée sont préchargées de façon à fluidifier l'affichage. Le diaporama peut être affiché en plein écran ou dans une page pour laquelle le dimensionnement des images sera géré par le système. Les enseignants peuvent décider de gérer les proportions différemment."
        ),
        'H5P.InteractiveVideo' => array(
            "title" => "Vidéo interactive",
            "sumary" => "Créez des vidéos interactives",
            "description" => "Ajoutez de l'interactivité à votre vidéo avec des explications, des images supplémentaires, des tableaux, des champs à remplir et des questions à choix multiple. Les questions peuvent permettre de passer à une autre partie de la vidéo en fonction de la réponse de l'utilisateur. Des résumés interactifs peuvent être ajoutés à la fin de la vidéo. Les vidéos interactives sont créées et modifiées depuis un navigateur standard."
        ),
        'H5P.MarkTheWords' => array(
            "title" => "Marquez les mots",
            "sumary" => "Créez un exercice où les utilisateurs mettent les mots en évidence",
            "description" => "Marquez les mots permet aux apprenants de sélectionner les mots d'un texte qui répondent à une question posée. L'enseignant entre le texte et marque les mots que l'apprenant devra sélectionner (les bonnes réponses) en les entourant d'astérisques : *MotAMarquer*",
        ),
        'H5P.MemoryGame' => array(
            "title" => "Jeu de mémoire",
            "sumary" => "Créez un jeu d'association d'images",
            "description" => "Créez vos propres jeux de mémoire et testez la mémoire de vos apprenants."
        ),
        'H5P.MultiChoice' => array(
            "title" => "Choix multiple",
            "sumary" => "Créez des questions à choix multiple flexibles",
            "description" => "Choix multiple est un outil d'évaluation. L'apprenant évalue immédiatement le résultat. Chaque question peut avoir une ou plusieurs réponses correctes."
        ),
        'H5P.QuestionSet' => array(
            "title" => "Quiz (ensemble de questions)",
            "sumary" => "Créez une série de différents types de questions",
            "description" => "Le quiz permet à l'apprenant de répondre à une série de questions présentées sous différentes formes tels que des questions  à choix multiple, des glisser-déposer, des remplissages de trous dans un texte. L'enseignant peut utiliser de nombreux paramètres pour régler le comportement du quiz. Il peut par exemple placer des images d'arrière plan, définir un pourcentage de réussite de l'apprenant, faire jouer une vidéo à la fin du quiz qui pourra être différente en fonction du résultat de l'apprenant."
        ),
        'H5P.Timeline' => array(
            "title" => "Frise chronologique (Timeline)",
            "sumary" => "Créez une chronologie d'événements alimentée de contenus multimédia",
            "description" => "La Frise chronologique permet de placer une séquence d'événements dans un ordre chronologique. Pour chaque événements, l'enseignant peut ajouter des images. Il peut également inclure des objets provenant de Twitter, Youtube, Vimeo, Google Maps et SoundCloud. Cet outil est issu de Timeline.js, développé par Knight Lab."
        ),
    );

    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp_libraries_hub_cache_fr');

    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('machine_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('summary', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally create table for hvp_libraries_hub_cache.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    $caches = $DB->get_records("hvp_libraries_hub_cache");

    foreach ($caches as $cache) {
        if (!$DB->record_exists('hvp_libraries_hub_cache_fr', array('machine_name' => $cache->machine_name))) {
            $item = new stdClass();
            $item->machine_name = $cache->machine_name;
            $item->title = $cache->title;
            $item->summary = $cache->summary;
            $item->description = $cache->description;
            if (isset($translations[$item->machine_name])) {
                foreach ($translations[$item->machine_name] as $key => $value) {
                    $item->$key = $value;
                }
            }

            $DB->insert_record('hvp_libraries_hub_cache_fr', $item);
        }
    }
}

/**
 * Hvp module upgrade function.
 *
 * @param string $oldversion The version we are upgrading from
 *
 * @return bool Success
 */
function xmldb_hvp_upgrade($oldversion) {
    $upgrades = [
        2016011300,
        2016042500,
        2016050600,
        2016051000,
        2016110100,
        2016122800,
        2017040500,
        2017050900,
        2017060900,
        2018060100,
        2018090300,
        2019022600,
        2019030700
    ];

    foreach ($upgrades as $version) {
        if ($oldversion < $version) {
            call_user_func("hvp_upgrade_{$version}");
            upgrade_mod_savepoint(true, $version, 'hvp');
        }
    }

    return true;
}
