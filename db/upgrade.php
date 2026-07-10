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

/**
 * Adds raw score and max score to xapi results table
 */
function hvp_upgrade_2018060100()
{
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
            "description" => "L'apprenant saisira les mots qui manquent dans un texte. Il saura si sa réponse est la bonne après chaque saisie ou après avoir saisi tous les mots, en fonction du paramétrage de l'exercice. Les auteurs saisissent le texte et marquent les mots à remplacer avec des astérisques. Les exercices créés peuvent être utilisés dans tous les domaines d'apprentissage : langues et grammaire, mathématiques, géographie, histoire, etc... "
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
            "description" => "Marquez les mots permet aux apprenants de sélectionner les mots d'un texte qui répondent à une question posée. L'enseignant entre le texte et marque les mots que l'apprenant devra sélectionner (les bonnes réponses) en les entourant d'astérisques : *MotAMarquer*",
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

function hvp_upgrade_2020080400() {
    global $DB;
    $dbman = $DB->get_manager();
    // Define field completionscorerequired to be added to hvp.
    $table = new xmldb_table('hvp');
    // Conditionally launch add field completionscorerequired.
    if (!$dbman->field_exists($table, 'completionpass')) {
        $dbman->add_field(
            $table,
            new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'timemodified')
        );
    }
}

function hvp_upgrade_2020080401() {
    global $DB;
    $dbman = $DB->get_manager();

    // Changing nullability of field completionpass on table hvp to not null.
    $table = new xmldb_table('hvp');
    $field = new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');

    // Launch change of nullability for field completionpass.
    $dbman->change_field_notnull($table, $field);
}

function hvp_upgrade_2020082800() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    if (!$dbman->field_exists($table, 'a11y_title')) {
        $dbman->add_field($table,
            new xmldb_field('a11y_title', XMLDB_TYPE_CHAR, '255', null, null, null, null)
        );
    }
}

/**
 * Drop old unused unique index, add nonunique index.
 */
function hvp_upgrade_2020091500() {
    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('hvp_xapi_results');
    $index = new xmldb_index('results', XMLDB_INDEX_NOTUNIQUE, ['content_id', 'user_id']);
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    $oldindex = new xmldb_index('result', XMLDB_INDEX_UNIQUE, ['id', 'content_id', 'user_id']);
    $dbman->drop_index($table, $oldindex);
}

function hvp_upgrade_2020112600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add Content Hub fields to main content table.
    $table = new xmldb_table('hvp');
    if (!$dbman->field_exists($table, 'shared')) {
        $dbman->add_field($table, new xmldb_field('shared', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'completionpass'));
    }
    if (!$dbman->field_exists($table, 'synced')) {
        $dbman->add_field($table, new xmldb_field('synced', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'shared'));
    }
    if (!$dbman->field_exists($table, 'hub_id')) {
        $dbman->add_field($table, new xmldb_field('hub_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'synced'));
    }

    // Create table for caching content hub metadata.
    $table = new xmldb_table('hvp_content_hub_cache');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('language', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);
    $table->add_field('json', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('last_checked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('language', XMLDB_INDEX_UNIQUE, ['language']);

    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

function hvp_upgrade_2026050600() {
  global $DB;
  $DB->execute("
    UPDATE {hvp}
    SET filtered = NULL
  ");
}

/**
 * Ajoute les traductions fr
 */
function add_fr_translations($translations) {
    global $DB;

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
 * Correction des anciennes traductions et ajout de nouvelles
 */
function hvp_upgrade_2021060400()
{
    global $DB;

    $translations = array(
        'H5P.Accordion' => array(
            "title" => "Accordéon",
            "summary" => "Créez des éléments extensibles empilés verticalement",
            "description" => "Réduisez la quantité de texte présentée aux lecteurs en utilisant cet accordéon réactif. Les lecteurs décident des titres à examiner de plus près en développant le titre. Excellent pour fournir une vue d'ensemble avec des explications approfondies en option.",
        ),
        'H5P.ArithmeticQuiz' => array(
            "title" => "Quiz d'arithmétique",
            "summary" => "Créez des quiz arithmétiques basés sur le temps",
            "description" => "Créez des quiz d'arithmétique composés de questions à choix multiples. En tant qu'auteur, il vous suffit de décider du type et de la longueur du quiz. Les utilisateurs gardent la trace de leur score et du temps passé à résoudre le quiz.",
        ),
        'H5P.Chart' => array(
            "title" => "Graphiques",
            "summary" => "Générez rapidement des diagrammes à barres et à secteurs",
            "description" => "Vous avez besoin de présenter des données statistiques simples sous forme de graphique sans avoir à créer les illustrations manuellement ? Graphiques est votre réponse.",
        ),
        'H5P.Collage' => array(
            "title" => "Collage",
            "summary" => "Créez un collage de plusieurs images",
            "description" => "L'outil Collage vous permet d'organiser des images en une composition apaisante.",
        ),
        'H5P.Column' => array(
            "title" => "Colonnes",
            "summary" => "Organisez le contenu du H5P dans une mise en page en colonnes",
            "description" => "Organisez votre type de contenu dans une mise en page en colonnes avec H5P Colonnes. Les types de contenu qui traitent de matières similaires ou qui partagent un thème commun peuvent désormais être regroupés pour créer une expérience d'apprentissage cohérente. En outre, les auteurs sont libres de faire preuve de créativité en combinant presque tous les types de contenu H5P existants.",
        ),
        'H5P.CoursePresentation' => array(
            "title" => "Présentation de cours",
            "summary" => "Créez une présentation avec un diaporama interactif",
            "description" => "Les présentations de cours comprennent des diapositives qui incluent du multimédia, du texte et différents types d'interractions comme des résumés interactifs, des questions à choix multiple et des vidéos interactives. Les élèves peuvent découvrir de nouvelles méthodes d'apprentissage et tester leurs connaissances et leur mémoire. Comme toujours avec H5P, le contenu est éditable dans les navigateurs web et la présentation de cours inclue un outils de création WYSIWYG. Une utilisation typique de la présentation de cours consiste à présenter le sujet sur quelques diapositives et de les faire suivre par des diapositives qui permettront à l'usager de tester ses connaissances. La présentation de cours peut cependant être utilisée de plein de façons différentes, comme outil de présentation en classe ou comme un jeu en utilisant les boutons de navigation dans les diapositives pour permettre à l'utilisateur de faire des choix et d'en apprécier les conséquences.",
        ),
        'H5P.Dialogcards' => array(
            "title" => "Cartes de dialogue",
            "summary" => "Créez des cartes tournantes basées sur des textes",
            "description" => "Les cartes de dialogue peuvent aider les apprenants à mémoriser des mots, des expressions ou des souvenirs. Sur le recto de la carte, il y a un indice correspondant à un mot ou une expression. En tournant la carte, l'apprenant révèle ce mot ou cette expression. Les cartes de dialogue peuvent être utilisées pour les langues, les mathématiques, l'histoire, etc.",
        ),
        'H5P.DocumentationTool' => array(
            "title" => "Outil de documentation",
            "summary" => "Créez un assistant de formulaire avec export de texte",
            "description" => "L'outil de documentation vise à faciliter la création d'assistants d'évaluation pour les activités axées sur les objectifs. Il peut également être utilisé comme un assistant de formulaire. Lors de l'édition, l'auteur peut ajouter plusieurs étapes à l'assistant. Dans chaque étape, l'auteur peut définir le contenu de cette étape. Le contenu peut être du texte brut, des champs de saisie, la définition des objectifs et l'évaluation des objectifs. Une fois publié, l'utilisateur final suivra les étapes de l'assistant. À la dernière étape de l'assistant, l'utilisateur peut générer un document contenant toutes les données qui ont été soumises. Ce document peut être téléchargé. L'outil de documentation est entièrement réactif et fonctionne parfaitement sur les petits écrans ainsi que sur votre bureau.",
        ),
        'H5P.DragQuestion' => array(
            "title" => "Glisser-Déposer",
            "summary" => "Créez des des glisser-déposer sur des images",
            "description" => "Le glisser-déposer permet d'associer 2 éléments ou plus afin de réaliser visuellement des connexions logiques. Créez des exercices de glisser-déposer en utilisant du texte et/ou des images qui pourront être déplacés pour trouver la solution. Glisser-Déposer prend en charge les relations un à un, un à plusieurs, plusieurs à un et plusieurs à plusieurs entrer les questions et les réponses.",
        ),
        'H5P.DragText' => array(
            "title" => "Déplacer des mots",
            "summary" => "Créez des exercices de glisser-déposer basés sur du texte",
            "description" => "Déplacer des mots permet de créer des textes auxquels il manque des morceaux. L'utilisateur devra glisser les morceaux de texte manquant pour reconstituer le texte complet. Cet outil permet de réfléchir au contenu d'un texte, de vérifier que l'utilisateur se souvient d'un texte qu'il a lu ou si il comprend ce qu'il lit... C'est super facile de créer un exercice, l'éditeur écrit simplement le texte et entoure les mots qui doivent être déplacés avec des astérisques, comme par exemple : *MotADéplacer*.",
        ),
        'H5P.Blanks' => array(
            "title" => "Remplir les vides",
            "summary" => "Créez des textes avec des mots manquant",
            "description" => "L'apprenant saisira les mots qui manquent dans un texte. Il saura si sa réponse est la bonne après chaque saisie ou après avoir saisi tous les mots, en fonction du paramétrage de l'exercice. Les auteurs saisissent le texte et marquent les mots à remplacer avec des astérisques. Les exercices créés peuvent être utilisés dans tous les domaines d'apprentissage : langues et grammaire, mathématiques, géographie, histoire, etc.",
        ),
        'H5P.ImageHotspotQuestion' => array(
            "title" => "Hotspot",
            "summary" => "Créez un point sur une image que les utilisateurs devront retrouver",
            "description" => "Hotspot permet aux utilisateurs de répondre à une question en cliquant sur un élément d'une image. L'enseignant télécharge une image et définit différents points correspondant à des détails ou des sections de l'image. Les points peuvent être définis comme corrects ou incorrects, avec un commentaire approprié qui s'affiche lorsque l'apprenant clique dessus.",
        ),
        'H5P.GuessTheAnswer' => array(
            "title" => "Devinez la réponse",
            "summary" => "Créez une question et une réponse associées à une image",
            "description" => "Ce type d'exercice permet aux enseignants de télécharger une image et d'y associer une question. Les apprenants peuvent deviner la réponse et appuyer sur un bouton pour vérifier que leur réponse est correcte. C'est un exercice qui permet d'effectuer des révisions.",
        ),
        'H5P.IFrameEmbed' => array(
            "title" => "Intégrateur d'Iframe",
            "summary" => "Embarquez du contenu à partir d'une url ou d'un ensemble de fichiers",
            "description" => "L'intégrateur d'iframe permet de réaliser facilement une activité H5P à partir d'une application JavaScript déjà existantes.",
        ),
        'H5P.InteractiveVideo' => array(
            "title" => "Vidéo interactive",
            "summary" => "Créez des vidéos interactives",
            "description" => "Ajoutez de l'interactivité à votre vidéo avec des explications, des images supplémentaires, des tableaux, des champs à remplir et des questions à choix multiple. Les questions peuvent permettre de passer à une autre partie de la vidéo en fonction de la réponse de l'utilisateur. Des résumés interactifs peuvent être ajoutés à la fin de la vidéo. Les vidéos interactives sont créées et modifiées depuis un navigateur standard.",
        ),
        'H5P.MarkTheWords' => array(
            "title" => "Marquez les mots",
            "summary" => "Créez un exercice où les utilisateurs mettent les mots en évidence",
            "description" => "Marquez les mots permet aux apprenants de sélectionner les mots d'un texte qui répondent à une question posée. L'enseignant entre le texte et marque les mots que l'apprenant devra sélectionner (les bonnes réponses) en les entourant d'astérisques : *MotAMarquer*.",
        ),
        'H5P.MemoryGame' => array(
            "title" => "Jeu de mémoire",
            "summary" => "Créez un jeu d'association d'images",
            "description" => "Créez vos propres jeux de mémoire et testez la mémoire de vos apprenants.",
        ),
        'H5P.MultiChoice' => array(
            "title" => "Choix multiple",
            "summary" => "Créez des questions à choix multiple flexibles",
            "description" => "Choix multiple est un outil d'évaluation. L'apprenant évalue immédiatement le résultat. Chaque question peut avoir une ou plusieurs réponses correctes.",
        ),
        'H5P.PersonalityQuiz' => array(
            "title" => "Test de personnalité",
            "summary" => "Créez des tests de personnalité",
            "description" => "Dans ce type de contenu, l'auteur définit une série de questions avec des alternatives, où chaque alternative est comparée à une ou plusieurs personnalités. À la fin du quiz, l'utilisateur final verra quelle personnalité correspond le mieux. Il existe plusieurs façons de rendre ce quiz visuellement attrayant, par exemple en représentant les questions, les alternatives et les personnalités à l'aide d'images.",
        ),
        'H5P.Questionnaire' => array(
            "title" => "Questionnaire",
            "summary" => "Créez un questionnaire pour avoir des retours",
            "description" => "Obtenez un retour d'information et posez des questions ouvertes dans des vidéos interactives et d'autres types de contenu avec Questionnaire. Questionnaire rend les réponses de l'utilisateur disponibles via une intégration xAPI. Cela signifie que les propriétaires de sites Web peuvent stocker les réponses de différentes manières. Les réponses peuvent être stockées dans un LRS, dans le stockage personnalisé du site ou un script peut récupérer l'adresse e-mail et l'utiliser pour envoyer un e-mail à l'utilisateur. Sur H5P.org, les réponses sont stockées dans Google Analytics.",
        ),
        'H5P.QuestionSet' => array(
            "title" => "Quiz (ensemble de questions)",
            "summary" => "Créez une série de différents types de questions",
            "description" => "Le quiz permet à l'apprenant de répondre à une série de questions présentées sous différentes formes tels que des questions  à choix multiple, des glisser-déposer, des remplissages de trous dans un texte. L'enseignant peut utiliser de nombreux paramètres pour régler le comportement du quiz. Il peut par exemple placer des images d'arrière plan, définir un pourcentage de réussite de l'apprenant, faire jouer une vidéo à la fin du quiz qui pourra être différente en fonction du résultat de l'apprenant.",
        ),
        'H5P.SingleChoiceSet' => array(
            "title" => "Ensemble de choix unique",
            "summary" => "Créez des questions avec une seule bonne réponse",
            "description" => "L'ensemble de choix unique permet aux concepteurs de contenu de créer des ensembles de questions avec une seule bonne réponse par question. L'utilisateur final reçoit un retour immédiat après avoir soumis chaque réponse.",
        ),
        'H5P.Summary' => array(
            "title" => "Résumé",
            "summary" => "Créez des tâches avec une liste de déclarations",
            "description" => "Les résumés aident l'apprenant à se souvenir des informations clés d'un texte, d'une vidéo ou d'une présentation, en élaborant activement un résumé du sujet traité. Lorsque l'apprenant a terminé un résumé, une liste complète d'affirmations clés sur le sujet s'affiche.",
        ),
        'H5P.Timeline' => array(
            "title" => "Frise chronologique (Timeline)",
            "summary" => "Créez une chronologie d'événements alimentée de contenus multimédia",
            "description" => "La Frise chronologique permet de placer une séquence d'événements dans un ordre chronologique. Pour chaque événements, l'enseignant peut ajouter des images. Il peut également inclure des objets provenant de Twitter, Youtube, Vimeo, Google Maps et SoundCloud. Cet outil est issu de Timeline.js, développé par Knight Lab.",
        ),
        'H5P.TrueFalse' => array(
            "title" => "Question vrai/faux",
            "summary" => "Créez des questions de type Vrai/Faux",
            "description" => "Question vrai/faux est un type de contenu simple et direct qui peut fonctionner seul ou être inséré dans d'autres types de contenu tels que la présentation de cours. Une question plus complexe peut être créée en ajoutant une image ou une vidéo.",
        ),
        'H5P.ImageHotspots' => array(
            "title" => "Image Hotspots",
            "summary" => "Créez une image avec plusieurs points d'information",
            "description" => "Image hotspots permet de créer une image avec des hotspots interactifs. Lorsque l'utilisateur appuie sur un hotspot, une popup contenant un en-tête et du texte ou une vidéo s'affiche. L'éditeur H5P vous permet d'ajouter autant de hotspots que vous le souhaitez.",
        ),
        'H5P.ImageMultipleHotspotQuestion' => array(
            "title" => "Hotspots Multiples",
            "summary" => "Créez plusieurs points que les utilisateurs devront trouver sur une image",
            "description" => "Hotspots Multiples permet aux enseignants de créer un exercice basé sur une image. Les apprenants devront trouver, de façon très ludique, les points qui correspondent à la question posée.",
        ),
        'H5P.ImageJuxtaposition' => array(
            "title" => "Juxtaposition d'images",
            "summary" => "Comparez deux images de manière interactive",
            "description" => "Juxtaposition d'images permet aux utilisateurs de comparer deux images de façon interactive, comme par exemple avant et après un événement.",
        ),
        'H5P.Audio' => array(
            "title" => "Audio",
            "summary" => "Téléchargez un enregistrement audio",
            "description" => "Téléchargez un enregistrement audio en .mp3, .wav, .ogg ou fournissez le lien d'un enregistrement audio.",
        ),
        'H5P.AudioRecorder' => array(
            "title" => "Enregistrement audio",
            "summary" => "Créez un enregistrement audio",
            "description" => "Un enregistreur audio HTML5. Enregistrez-vous et écoutez-vous ou téléchargez un fichier .wav de votre enregistrement.",
        ),
        'H5P.SpeakTheWords' => array(
            "title" => "Répondez à la voix",
            "summary" => "Répondez à une question en utilisant votre voix (Chrome uniquement)",
            "description" => "La fonction \"Répondez à la voix\" n'est prise en charge que par les navigateurs qui mettent en œuvre l'API Web Speech (navigateurs Chrome, sauf sur iOS). Vous devez disposer d'un microphone pour répondre à la question. Posez une question aux utilisateurs et faites-leur répondre en utilisant leur voix. Vous pouvez choisir plusieurs réponses correctes. L'utilisateur pourra voir comment ses mots ont été interprétés et dans quelle mesure il s'est rapproché des bonnes réponses.",
        ),
        'H5P.Agamotto' => array(
            "title" => "Agamotto (Mélangeur d'images)",
            "summary" => "Présentez une séquence d'images et d'explications",
            "description" => "Présentez une séquence d'images que les gens sont censés regarder l'une après l'autre, par exemple des photos d'un objet qui change au fil du temps, des schémas ou des cartes qui sont organisés en différentes couches ou des images qui révèlent de plus en plus de détails.",
        ),
        'H5P.ImageSequencing' => array(
            "title" => "Séquençage d'images",
            "summary" => "Placez les images dans le bon ordre",
            "description" => "Un type de contenu gratuit de séquencement d'images basé sur HTML5 qui permet aux auteurs d'ajouter une séquence de leurs propres images (et une description facultative de l'image) au jeu dans un ordre particulier. L'ordre des images sera aléatoire et les joueurs devront les réordonner en fonction de la description de la tâche.",
        ),
        'H5P.Flashcards' => array(
            "title" => "Cartes flash",
            "summary" => "Créez des cartes flash élégantes et modernes",
            "description" => "Ce type de contenu permet aux auteurs de créer une carte flash unique ou un ensemble de cartes flash, où chaque carte comporte des images associées à des questions et des réponses. Les apprenants doivent remplir le champ de texte, puis vérifier l'exactitude de leur solution.",
        ),
        'H5P.SpeakTheWordsSet' => array(
            "title" => "Répondez à la voix aux questions",
            "summary" => "Créez une série de questions auxquelles vous répondez par la parole (Chrome uniquement)",
            "description" => "\"Répondez à la voix aux questions\" n'est pris en charge que par les navigateurs qui mettent en œuvre l'API Web Speech (navigateurs Chrome, sauf sur ios). Vous devez disposer d'un microphone pour répondre à la question. Créez un ensemble de questions auxquelles les apprenants peuvent répondre en utilisant leur voix. Vous pouvez choisir plusieurs bonnes réponses. L'utilisateur pourra voir comment ses mots ont été interprétés, et à quel point il était proche des bonnes réponses.",
        ),
        'H5P.ImageSlider' => array(
            "title" => "Carrousel",
            "summary" => "Créez facilement un carrousel d'images",
            "description" => "Présentez vos images facilement sous forme de carrousel (diaporama). L'enseignant télécharge des images et fournie des commentaires pour ces images. Les 2 images qui suivent l'image affichée sont préchargées de façon à fluidifier l'affichage. Le diaporama peut être affiché en plein écran ou dans une page pour laquelle le dimensionnement des images sera géré par le système. Les enseignants peuvent décider de gérer les proportions différemment.",
        ),
        'H5P.Essay' => array(
            "title" => "Essais",
            "summary" => "Créer des essais avec un retour d'information instantané",
            "description" => "Dans ce type de contenu, l'auteur définit un ensemble de mots-clés qui représentent des aspects cruciaux d'un sujet. Ces mots-clés sont comparés à un texte que les élèves ont composé et peuvent être utilisés pour fournir immédiatement un retour d'information - soit en suggérant de réviser certains détails du sujet si un mot-clé est absent, soit en confirmant les idées de l'élève si le texte contient un mot-clé.",
        ),
        'H5P.ImagePair' => array(
            "title" => "Appariement d'images",
            "summary" => "Jeu de correspondance d'images par glisser-déposer",
            "description" => "L'appariement d'images est une activité simple et efficace qui demande aux apprenants de faire correspondre des paires d'images. Comme il n'est pas nécessaire que les deux images d'une paire soient identiques, les auteurs peuvent également tester la compréhension d'une relation entre deux images différentes.",
        ),
        'H5P.Dictation' => array(
            "title" => "Dictée",
            "summary" => "Créez une dictée avec un retour d'information instantané",
            "description" => "Vous pouvez ajouter des échantillons audio contenant une phrase à dicter et saisir la transcription correcte. Vos élèves peuvent écouter les échantillons et saisir ce qu'ils ont entendu dans un champ de texte. Leurs réponses seront évaluées automatiquement. Plusieurs options vous permettront de contrôler la difficulté de l'exercice. Vous pouvez éventuellement ajouter un deuxième échantillon audio pour une phrase qui pourrait contenir une version prononcée lentement. Vous pouvez également fixer une limite à la fréquence d'écoute d'un échantillon, définir si la ponctuation doit être prise en compte dans la notation et décider si les petites erreurs, comme les fautes de frappe, doivent être comptabilisées comme une absence d'erreur, une erreur complète ou une demi-erreur.",
        ),
        'H5P.BranchingScenario' => array(
            "title" => "Scénario de branchement (beta)",
            "summary" => "Créez des dilemmes et un apprentissage autodidacte",
            "description" => "Les scénarios de branchement permettent aux auteurs de présenter aux apprenants une variété de choix et de contenus interactifs riches. Les choix que les apprenants font détermineront le prochain contenu qu'ils verront. Peut être utilisé pour créer des dilemmes, des jeux sérieux et de l'apprentissage à son propre rythme.",
        ),
        'H5P.ThreeImage' => array(
            "title" => "Visite virtuelle (360)",
            "summary" => "Créez des environnements à 360° avec des interactions",
            "description" => "Les images 360 (équirectangulaires) et normales peuvent être enrichies d'interactivités telles que des explications, des vidéos, des sons et des questions interactives. Les images créent des scènes qui peuvent également être reliées entre elles pour donner à l'utilisateur l'impression de se déplacer entre des environnements ou entre différents points de vue au sein d'un même environnement.",
        ),
        'H5P.FindTheWords' => array(
            "title" => "Trouvez les mots",
            "summary" => "Jeu de mots mélangés",
            "description" => "Une activité de recherche de mots en HTML5 qui permet aux auteurs de créer une liste de mots qui seront dessinés dans une grille. La tâche de l'apprenant est de trouver et de sélectionner les mots dans la grille.",
        ),
        'H5P.InteractiveBook' => array(
            "title" => "Livre interactif",
            "summary" => "Créez de petits cours, livres et tests",
            "description" => "Créez de petits cours, livres ou tests. Le livre interactif permet aux auteurs de combiner de grandes quantités de contenu interactif, comme des vidéos interactives, des questions, des présentations de cours, etc. sur plusieurs pages. Un résumé à la fin récapitule les scores obtenus par l'apprenant tout au long du livre.",
        ),
        'H5P.KewArCode' => array(
            "title" => "KewAr Code",
            "summary" => "Créez des codes QR à des fins différentes",
            "description" => "KewAr Code permet aux concepteurs de contenu de créer des QR-codes. Ces QR-codes peuvent encoder des URL, mais aussi des informations de contact, des événements, des géolocalisations, etc. Les gens peuvent les scanner avec un lecteur de QR-codes afin de déclencher l'action choisie.",
        ),
        'H5P.AdventCalendar' => array(
            "title" => "Calendrier de l'Avent (beta)",
            "summary" => "Créez des surprises qui seront dévoilées chaque jour",
            "description" => "Construisez et personnalisez un magnifique calendrier de l'Avent. Vous pouvez ajouter une image d'arrière-plan à l'ensemble du calendrier, sur chaque porte, et comme arrière-plan du contenu à l'intérieur de chaque porte. Vous pouvez également ajouter un effet de neige et de la musique. À l'intérieur de chaque porte, vous pouvez ajouter un son, une vidéo, un texte, une image ou un lien.

Notez qu'il est facile pour les utilisateurs avertis de révéler immédiatement le contenu de toutes les portes. Si vous prévoyez de révéler de grands secrets les jours suivants, vous devez attendre ce jour-là avant d'ajouter vos grands secrets au calendrier.",
        ),
    );

    // On supprime toutes les traductions fr car on repart a zeros
    $DB->delete_records('hvp_libraries_hub_cache_fr');

    add_fr_translations($translations);
}

/**
 * Ajout de nouvelles traductions
 */
function hvp_upgrade_2023012500()
{
    global $DB;

    $translations = array(
        'H5P.Crossword' => array(
            "title" => "Mots croisés",
            "summary" => "Créez une grille de mots croisés",
            "description" => "Construisez et personnalisez de beaux mots croisés pour engager votre public. Les mots croisés sont hautement personnalisables, ce qui vous permet de configurer toutes les couleurs, de télécharger une image de fond, de décider de la façon dont les scores doivent être attribués et même de rendre les mots aléatoires afin que votre public obtienne une nouvelle grille à chaque fois si vous le souhaitez.",
        ),
        'H5P.SortParagraphs' => array(
            "title" => "Trier les paragraphes",
            "summary" => "Créez un ensemble de paragraphes à trier",
            "description" => "Tapez ou collez une liste de paragraphes qui seront randomisés. Vous pouvez par exemple faire en sorte que chaque paragraphe soit une partie d'une chanson, un bloc de code ou les étapes d'une recette. Les apprenants doivent classer les paragraphes dans l'ordre correct. Par défaut, les apprenants obtiendront un point pour chaque paragraphe qui suit le paragraphe qu'il est censé suivre, mais vous pouvez décider d'accorder un point pour chaque paragraphe qui se trouve à la bonne place.",
        ),
        'H5P.MultiMediaChoice' => array(
            "title" => "Choix de l'image",
            "summary" => "Créez une tâche où les choix sont des images",
            "description" => "Créez de superbes questions à choix multiple ou unique où les choix sont des images. Vous pouvez personnaliser la mise en page des choix et choisir entre des ratios d'images fixes ou simplement utiliser les ratios que les images ont déjà.",
        ),
        'H5P.Cornell' => array(
            "title" => "Notes de Cornell",
            "summary" => "Prendre des notes en utilisant le système Cornell",
            "description" => "Présentez aux apprenants une vidéo, un texte ou un document audio et encouragez-les à prendre des notes à l'aide du système de prise de notes Cornell.",
        ),
        'H5P.ARScavenger' => array(
            "title" => "AR Scavenger",
            "summary" => "Le plaisir de la réalité augmentée !",
            "description" => "Laissez les apprenants explorer la réalité augmentée avec des modèles 3D ou des exercices H5P. Vous pouvez définir des marqueurs semblables à des codes QR que vos élèves peuvent scanner avec l'appareil photo de leur appareil. Ces marqueurs peuvent déclencher le mélange d'un modèle 3D de votre choix avec la vue de la caméra, ou ils peuvent afficher une interaction H5P.",
        ),
        'H5P.StructureStrip' => array(
            "title" => "Bande de structure",
            "summary" => "Bande de structure interactive",
            "description" => "Une bande de structure est traditionnellement placée à côté d'une feuille de papier (ou même collée dessus). Elle fournit aux élèves un échafaudage pour un texte et les aide à maintenir les longueurs des différents segments de texte dans de bonnes proportions. Avec la bande de structure, vous pouvez maintenant utiliser la même approche dans H5P sans papier.",
        ),
    );

    add_fr_translations($translations);
}

/**
 * Ajout d'une nouvelle traduction
 */
function hvp_upgrade_2023013100()
{
    global $DB;

    $translations = array(
        'H5P.InfoWall' => array(
            "title" => "Mur d'information",
            "summary" => "Créez des panneaux d'informations que les utilisateurs peuvent filtrer par mots-clés pertinents.",
            "description" => "Permettez aux utilisateurs de parcourir facilement toutes les informations ou de les filtrer par mots-clés afin de rechercher des informations spécifiques. En tant qu'auteur, vous pouvez configurer un panneau maître et décider d'ajouter une image par panneau, etc.",
        ),
    );

    add_fr_translations($translations);
}

/**
 * Recrée la table hvp_libraries_hub_cache_fr et réinjecte toutes les traductions
 */
function hvp_upgrade_2026071000() {
    global $DB;
    $dbman = $DB->get_manager();

    // Supprimer la table si elle existe
    $table = new xmldb_table('hvp_libraries_hub_cache_fr');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Recréer la table
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('machine_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('summary', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    $dbman->create_table($table);

    // Regrouper toutes les traductions
    $translations = array(
        'H5P.Accordion' => array(
            "title" => "Accordéon",
            "summary" => "Créez des éléments extensibles empilés verticalement",
            "description" => "Réduisez la quantité de texte présentée aux lecteurs en utilisant cet accordéon réactif. Les lecteurs décident des titres à examiner de plus près en développant le titre. Excellent pour fournir une vue d'ensemble avec des explications approfondies en option.",
        ),
        'H5P.ArithmeticQuiz' => array(
            "title" => "Quiz d'arithmétique",
            "summary" => "Créez des quiz arithmétiques basés sur le temps",
            "description" => "Créez des quiz d'arithmétique composés de questions à choix multiples. En tant qu'auteur, il vous suffit de décider du type et de la longueur du quiz. Les utilisateurs gardent la trace de leur score et du temps passé à résoudre le quiz.",
        ),
        'H5P.Chart' => array(
            "title" => "Graphiques",
            "summary" => "Générez rapidement des diagrammes à barres et à secteurs",
            "description" => "Vous avez besoin de présenter des données statistiques simples sous forme de graphique sans avoir à créer les illustrations manuellement ? Graphiques est votre réponse.",
        ),
        'H5P.Collage' => array(
            "title" => "Collage",
            "summary" => "Créez un collage de plusieurs images",
            "description" => "L'outil Collage vous permet d'organiser des images en une composition apaisante.",
        ),
        'H5P.Column' => array(
            "title" => "Colonnes",
            "summary" => "Organisez le contenu du H5P dans une mise en page en colonnes",
            "description" => "Organisez votre type de contenu dans une mise en page en colonnes avec H5P Colonnes. Les types de contenu qui traitent de matières similaires ou qui partagent un thème commun peuvent désormais être regroupés pour créer une expérience d'apprentissage cohérente. En outre, les auteurs sont libres de faire preuve de créativité en combinant presque tous les types de contenu H5P existants.",
        ),
        'H5P.CoursePresentation' => array(
            "title" => "Présentation de cours",
            "summary" => "Créez une présentation avec un diaporama interactif",
            "description" => "Les présentations de cours comprennent des diapositives qui incluent du multimédia, du texte et différents types d'interractions comme des résumés interactifs, des questions à choix multiple et des vidéos interactives. Les élèves peuvent découvrir de nouvelles méthodes d'apprentissage et tester leurs connaissances et leur mémoire. Comme toujours avec H5P, le contenu est éditable dans les navigateurs web et la présentation de cours inclue un outils de création WYSIWYG. Une utilisation typique de la présentation de cours consiste à présenter le sujet sur quelques diapositives et de les faire suivre par des diapositives qui permettront à l'usager de tester ses connaissances. La présentation de cours peut cependant être utilisée de plein de façons différentes, comme outil de présentation en classe ou comme un jeu en utilisant les boutons de navigation dans les diapositives pour permettre à l'utilisateur de faire des choix et d'en apprécier les conséquences.",
        ),
        'H5P.Dialogcards' => array(
            "title" => "Cartes de dialogue",
            "summary" => "Créez des cartes tournantes basées sur des textes",
            "description" => "Les cartes de dialogue peuvent aider les apprenants à mémoriser des mots, des expressions ou des souvenirs. Sur le recto de la carte, il y a un indice correspondant à un mot ou une expression. En tournant la carte, l'apprenant révèle ce mot ou cette expression. Les cartes de dialogue peuvent être utilisées pour les langues, les mathématiques, l'histoire, etc.",
        ),
        'H5P.DocumentationTool' => array(
            "title" => "Outil de documentation",
            "summary" => "Créez un assistant de formulaire avec export de texte",
            "description" => "L'outil de documentation vise à faciliter la création d'assistants d'évaluation pour les activités axées sur les objectifs. Il peut également être utilisé comme un assistant de formulaire. Lors de l'édition, l'auteur peut ajouter plusieurs étapes à l'assistant. Dans chaque étape, l'auteur peut définir le contenu de cette étape. Le contenu peut être du texte brut, des champs de saisie, la définition des objectifs et l'évaluation des objectifs. Une fois publié, l'utilisateur final suivra les étapes de l'assistant. À la dernière étape de l'assistant, l'utilisateur peut générer un document contenant toutes les données qui ont été soumises. Ce document peut être téléchargé. L'outil de documentation est entièrement réactif et fonctionne parfaitement sur les petits écrans ainsi que sur votre bureau.",
        ),
        'H5P.DragQuestion' => array(
            "title" => "Glisser-Déposer",
            "summary" => "Créez des des glisser-déposer sur des images",
            "description" => "Le glisser-déposer permet d'associer 2 éléments ou plus afin de réaliser visuellement des connexions logiques. Créez des exercices de glisser-déposer en utilisant du texte et/ou des images qui pourront être déplacés pour trouver la solution. Glisser-Déposer prend en charge les relations un à un, un à plusieurs, plusieurs à un et plusieurs à plusieurs entrer les questions et les réponses.",
        ),
        'H5P.DragText' => array(
            "title" => "Déplacer des mots",
            "summary" => "Créez des exercices de glisser-déposer basés sur du texte",
            "description" => "Déplacer des mots permet de créer des textes auxquels il manque des morceaux. L'utilisateur devra glisser les morceaux de texte manquant pour reconstituer le texte complet. Cet outil permet de réfléchir au contenu d'un texte, de vérifier que l'utilisateur se souvient d'un texte qu'il a lu ou si il comprend ce qu'il lit... C'est super facile de créer un exercice, l'éditeur écrit simplement le texte et entoure les mots qui doivent être déplacés avec des astérisques, comme par exemple : *MotADéplacer*.",
        ),
        'H5P.Blanks' => array(
            "title" => "Remplir les vides",
            "summary" => "Créez des textes avec des mots manquant",
            "description" => "L'apprenant saisira les mots qui manquent dans un texte. Il saura si sa réponse est la bonne après chaque saisie ou après avoir saisi tous les mots, en fonction du paramétrage de l'exercice. Les auteurs saisissent le texte et marquent les mots à remplacer avec des astérisques. Les exercices créés peuvent être utilisés dans tous les domaines d'apprentissage : langues et grammaire, mathématiques, géographie, histoire, etc.",
        ),
        'H5P.ImageHotspotQuestion' => array(
            "title" => "Hotspot",
            "summary" => "Créez un point sur une image que les utilisateurs devront retrouver",
            "description" => "Hotspot permet aux utilisateurs de répondre à une question en cliquant sur un élément d'une image. L'enseignant télécharge une image et définit différents points correspondant à des détails ou des sections de l'image. Les points peuvent être définis comme corrects ou incorrects, avec un commentaire approprié qui s'affiche lorsque l'apprenant clique dessus.",
        ),
        'H5P.GuessTheAnswer' => array(
            "title" => "Devinez la réponse",
            "summary" => "Créez une question et une réponse associées à une image",
            "description" => "Ce type d'exercice permet aux enseignants de télécharger une image et d'y associer une question. Les apprenants peuvent deviner la réponse et appuyer sur un bouton pour vérifier que leur réponse est correcte. C'est un exercice qui permet d'effectuer des révisions.",
        ),
        'H5P.IFrameEmbed' => array(
            "title" => "Intégrateur d'Iframe",
            "summary" => "Embarquez du contenu à partir d'une url ou d'un ensemble de fichiers",
            "description" => "L'intégrateur d'iframe permet de réaliser facilement une activité H5P à partir d'une application JavaScript déjà existantes.",
        ),
        'H5P.InteractiveVideo' => array(
            "title" => "Vidéo interactive",
            "summary" => "Créez des vidéos interactives",
            "description" => "Ajoutez de l'interactivité à votre vidéo avec des explications, des images supplémentaires, des tableaux, des champs à remplir et des questions à choix multiple. Les questions peuvent permettre de passer à une autre partie de la vidéo en fonction de la réponse de l'utilisateur. Des résumés interactifs peuvent être ajoutés à la fin de la vidéo. Les vidéos interactives sont créées et modifiées depuis un navigateur standard.",
        ),
        'H5P.MarkTheWords' => array(
            "title" => "Marquez les mots",
            "summary" => "Créez un exercice où les utilisateurs mettent les mots en évidence",
            "description" => "Marquez les mots permet aux apprenants de sélectionner les mots d'un texte qui répondent à une question posée. L'enseignant entre le texte et marque les mots que l'apprenant devra sélectionner (les bonnes réponses) en les entourant d'astérisques : *MotAMarquer*.",
        ),
        'H5P.MemoryGame' => array(
            "title" => "Jeu de mémoire",
            "summary" => "Créez un jeu d'association d'images",
            "description" => "Créez vos propres jeux de mémoire et testez la mémoire de vos apprenants.",
        ),
        'H5P.MultiChoice' => array(
            "title" => "Choix multiple",
            "summary" => "Créez des questions à choix multiple flexibles",
            "description" => "Choix multiple est un outil d'évaluation. L'apprenant évalue immédiatement le résultat. Chaque question peut avoir une ou plusieurs réponses correctes.",
        ),
        'H5P.PersonalityQuiz' => array(
            "title" => "Test de personnalité",
            "summary" => "Créez des tests de personnalité",
            "description" => "Dans ce type de contenu, l'auteur définit une série de questions avec des alternatives, où chaque alternative est comparée à une ou plusieurs personnalités. À la fin du quiz, l'utilisateur final verra quelle personnalité correspond le mieux. Il existe plusieurs façons de rendre ce quiz visuellement attrayant, par exemple en représentant les questions, les alternatives et les personnalités à l'aide d'images.",
        ),
        'H5P.Questionnaire' => array(
            "title" => "Questionnaire",
            "summary" => "Créez un questionnaire pour avoir des retours",
            "description" => "Obtenez un retour d'information et posez des questions ouvertes dans des vidéos interactives et d'autres types de contenu avec Questionnaire. Questionnaire rend les réponses de l'utilisateur disponibles via une intégration xAPI. Cela signifie que les propriétaires de sites Web peuvent stocker les réponses de différentes manières. Les réponses peuvent être stockées dans un LRS, dans le stockage personnalisé du site ou un script peut récupérer l'adresse e-mail et l'utiliser pour envoyer un e-mail à l'utilisateur. Sur H5P.org, les réponses sont stockées dans Google Analytics.",
        ),
        'H5P.QuestionSet' => array(
            "title" => "Quiz (ensemble de questions)",
            "summary" => "Créez une série de différents types de questions",
            "description" => "Le quiz permet à l'apprenant de répondre à une série de questions présentées sous différentes formes tels que des questions  à choix multiple, des glisser-déposer, des remplissages de trous dans un texte. L'enseignant peut utiliser de nombreux paramètres pour régler le comportement du quiz. Il peut par exemple placer des images d'arrière plan, définir un pourcentage de réussite de l'apprenant, faire jouer une vidéo à la fin du quiz qui pourra être différente en fonction du résultat de l'apprenant.",
        ),
        'H5P.SingleChoiceSet' => array(
            "title" => "Ensemble de choix unique",
            "summary" => "Créez des questions avec une seule bonne réponse",
            "description" => "L'ensemble de choix unique permet aux concepteurs de contenu de créer des ensembles de questions avec une seule bonne réponse par question. L'utilisateur final reçoit un retour immédiat après avoir soumis chaque réponse.",
        ),
        'H5P.Summary' => array(
            "title" => "Résumé",
            "summary" => "Créez des tâches avec une liste de déclarations",
            "description" => "Les résumés aident l'apprenant à se souvenir des informations clés d'un texte, d'une vidéo ou d'une présentation, en élaborant activement un résumé du sujet traité. Lorsque l'apprenant a terminé un résumé, une liste complète d'affirmations clés sur le sujet s'affiche.",
        ),
        'H5P.Timeline' => array(
            "title" => "Frise chronologique (Timeline)",
            "summary" => "Créez une chronologie d'événements alimentée de contenus multimédia",
            "description" => "La Frise chronologique permet de placer une séquence d'événements dans un ordre chronologique. Pour chaque événements, l'enseignant peut ajouter des images. Il peut également inclure des objets provenant de Twitter, Youtube, Vimeo, Google Maps et SoundCloud. Cet outil est issu de Timeline.js, développé par Knight Lab.",
        ),
        'H5P.TrueFalse' => array(
            "title" => "Question vrai/faux",
            "summary" => "Créez des questions de type Vrai/Faux",
            "description" => "Question vrai/faux est un type de contenu simple et direct qui peut fonctionner seul ou être inséré dans d'autres types de contenu tels que la présentation de cours. Une question plus complexe peut être créée en ajoutant une image ou une vidéo.",
        ),
        'H5P.ImageHotspots' => array(
            "title" => "Image Hotspots",
            "summary" => "Créez une image avec plusieurs points d'information",
            "description" => "Image hotspots permet de créer une image avec des hotspots interactifs. Lorsque l'utilisateur appuie sur un hotspot, une popup contenant un en-tête et du texte ou une vidéo s'affiche. L'éditeur H5P vous permet d'ajouter autant de hotspots que vous le souhaitez.",
        ),
        'H5P.ImageMultipleHotspotQuestion' => array(
            "title" => "Hotspots Multiples",
            "summary" => "Créez plusieurs points que les utilisateurs devront trouver sur une image",
            "description" => "Hotspots Multiples permet aux enseignants de créer un exercice basé sur une image. Les apprenants devront trouver, de façon très ludique, les points qui correspondent à la question posée.",
        ),
        'H5P.ImageJuxtaposition' => array(
            "title" => "Juxtaposition d'images",
            "summary" => "Comparez deux images de manière interactive",
            "description" => "Juxtaposition d'images permet aux utilisateurs de comparer deux images de façon interactive, comme par exemple avant et après un événement.",
        ),
        'H5P.Audio' => array(
            "title" => "Audio",
            "summary" => "Téléchargez un enregistrement audio",
            "description" => "Téléchargez un enregistrement audio en .mp3, .wav, .ogg ou fournissez le lien d'un enregistrement audio.",
        ),
        'H5P.AudioRecorder' => array(
            "title" => "Enregistrement audio",
            "summary" => "Créez un enregistrement audio",
            "description" => "Un enregistreur audio HTML5. Enregistrez-vous et écoutez-vous ou téléchargez un fichier .wav de votre enregistrement.",
        ),
        'H5P.SpeakTheWords' => array(
            "title" => "Répondez à la voix",
            "summary" => "Répondez à une question en utilisant votre voix (Chrome uniquement)",
            "description" => "La fonction \"Répondez à la voix\" n'est prise en charge que par les navigateurs qui mettent en œuvre l'API Web Speech (navigateurs Chrome, sauf sur iOS). Vous devez disposer d'un microphone pour répondre à la question. Posez une question aux utilisateurs et faites-leur répondre en utilisant leur voix. Vous pouvez choisir plusieurs réponses correctes. L'utilisateur pourra voir comment ses mots ont été interprétés et dans quelle mesure il s'est rapproché des bonnes réponses.",
        ),
        'H5P.Agamotto' => array(
            "title" => "Agamotto (Mélangeur d'images)",
            "summary" => "Présentez une séquence d'images et d'explications",
            "description" => "Présentez une séquence d'images que les gens sont censés regarder l'une après l'autre, par exemple des photos d'un objet qui change au fil du temps, des schémas ou des cartes qui sont organisés en différentes couches ou des images qui révèlent de plus en plus de détails.",
        ),
        'H5P.ImageSequencing' => array(
            "title" => "Séquençage d'images",
            "summary" => "Placez les images dans le bon ordre",
            "description" => "Un type de contenu gratuit de séquencement d'images basé sur HTML5 qui permet aux auteurs d'ajouter une séquence de leurs propres images (et une description facultative de l'image) au jeu dans un ordre particulier. L'ordre des images sera aléatoire et les joueurs devront les réordonner en fonction de la description de la tâche.",
        ),
        'H5P.Flashcards' => array(
            "title" => "Cartes flash",
            "summary" => "Créez des cartes flash élégantes et modernes",
            "description" => "Ce type de contenu permet aux auteurs de créer une carte flash unique ou un ensemble de cartes flash, où chaque carte comporte des images associées à des questions et des réponses. Les apprenants doivent remplir le champ de texte, puis vérifier l'exactitude de leur solution.",
        ),
        'H5P.SpeakTheWordsSet' => array(
            "title" => "Répondez à la voix aux questions",
            "summary" => "Créez une série de questions auxquelles vous répondez par la parole (Chrome uniquement)",
            "description" => "\"Répondez à la voix aux questions\" n'est pris en charge que par les navigateurs qui mettent en œuvre l'API Web Speech (navigateurs Chrome, sauf sur ios). Vous devez disposer d'un microphone pour répondre à la question. Créez un ensemble de questions auxquelles les apprenants peuvent répondre en utilisant leur voix. Vous pouvez choisir plusieurs bonnes réponses. L'utilisateur pourra voir comment ses mots ont été interprétés, et à quel point il était proche des bonnes réponses.",
        ),
        'H5P.ImageSlider' => array(
            "title" => "Carrousel",
            "summary" => "Créez facilement un carrousel d'images",
            "description" => "Présentez vos images facilement sous forme de carrousel (diaporama). L'enseignant télécharge des images et fournie des commentaires pour ces images. Les 2 images qui suivent l'image affichée sont préchargées de façon à fluidifier l'affichage. Le diaporama peut être affiché en plein écran ou dans une page pour laquelle le dimensionnement des images sera géré par le système. Les enseignants peuvent décider de gérer les proportions différemment.",
        ),
        'H5P.Essay' => array(
            "title" => "Essais",
            "summary" => "Créer des essais avec un retour d'information instantané",
            "description" => "Dans ce type de contenu, l'auteur définit un ensemble de mots-clés qui représentent des aspects cruciaux d'un sujet. Ces mots-clés sont comparés à un texte que les élèves ont composé et peuvent être utilisés pour fournir immédiatement un retour d'information - soit en suggérant de réviser certains détails du sujet si un mot-clé est absent, soit en confirmant les idées de l'élève si le texte contient un mot-clé.",
        ),
        'H5P.ImagePair' => array(
            "title" => "Appariement d'images",
            "summary" => "Jeu de correspondance d'images par glisser-déposer",
            "description" => "L'appariement d'images est une activité simple et efficace qui demande aux apprenants de faire correspondre des paires d'images. Comme il n'est pas nécessaire que les deux images d'une paire soient identiques, les auteurs peuvent également tester la compréhension d'une relation entre deux images différentes.",
        ),
        'H5P.Dictation' => array(
            "title" => "Dictée",
            "summary" => "Créez une dictée avec un retour d'information instantané",
            "description" => "Vous pouvez ajouter des échantillons audio contenant une phrase à dicter et saisir la transcription correcte. Vos élèves peuvent écouter les échantillons et saisir ce qu'ils ont entendu dans un champ de texte. Leurs réponses seront évaluées automatiquement. Plusieurs options vous permettront de contrôler la difficulté de l'exercice. Vous pouvez éventuellement ajouter un deuxième échantillon audio pour une phrase qui pourrait contenir une version prononcée lentement. Vous pouvez également fixer une limite à la fréquence d'écoute d'un échantillon, définir si la ponctuation doit être prise en compte dans la notation et décider si les petites erreurs, comme les fautes de frappe, doivent être comptabilisées comme une absence d'erreur, une erreur complète ou une demi-erreur.",
        ),
        'H5P.BranchingScenario' => array(
            "title" => "Scénario de branchement (beta)",
            "summary" => "Créez des dilemmes et un apprentissage autodidacte",
            "description" => "Les scénarios de branchement permettent aux auteurs de présenter aux apprenants une variété de choix et de contenus interactifs riches. Les choix que les apprenants font détermineront le prochain contenu qu'ils verront. Peut être utilisé pour créer des dilemmes, des jeux sérieux et de l'apprentissage à son propre rythme.",
        ),
        'H5P.ThreeImage' => array(
            "title" => "Visite virtuelle (360)",
            "summary" => "Créez des environnements à 360° avec des interactions",
            "description" => "Les images 360 (équirectangulaires) et normales peuvent être enrichies d'interactivités telles que des explications, des vidéos, des sons et des questions interactives. Les images créent des scènes qui peuvent également être reliées entre elles pour donner à l'utilisateur l'impression de se déplacer entre des environnements ou entre différents points de vue au sein d'un même environnement.",
        ),
        'H5P.FindTheWords' => array(
            "title" => "Trouvez les mots",
            "summary" => "Jeu de mots mélangés",
            "description" => "Une activité de recherche de mots en HTML5 qui permet aux auteurs de créer une liste de mots qui seront dessinés dans une grille. La tâche de l'apprenant est de trouver et de sélectionner les mots dans la grille.",
        ),
        'H5P.InteractiveBook' => array(
            "title" => "Livre interactif",
            "summary" => "Créez de petits cours, livres et tests",
            "description" => "Créez de petits cours, livres ou tests. Le livre interactif permet aux auteurs de combiner de grandes quantités de contenu interactif, comme des vidéos interactives, des questions, des présentations de cours, etc. sur plusieurs pages. Un résumé à la fin récapitule les scores obtenus par l'apprenant tout au long du livre.",
        ),
        'H5P.KewArCode' => array(
            "title" => "KewAr Code",
            "summary" => "Créez des codes QR à des fins différentes",
            "description" => "KewAr Code permet aux concepteurs de contenu de créer des QR-codes. Ces QR-codes peuvent encoder des URL, mais aussi des informations de contact, des événements, des géolocalisations, etc. Les gens peuvent les scanner avec un lecteur de QR-codes afin de déclencher l'action choisie.",
        ),
        'H5P.AdventCalendar' => array(
            "title" => "Calendrier de l'Avent (beta)",
            "summary" => "Créez des surprises qui seront dévoilées chaque jour",
            "description" => "Construisez et personnalisez un magnifique calendrier de l'Avent. Vous pouvez ajouter une image d'arrière-plan à l'ensemble du calendrier, sur chaque porte, et comme arrière-plan du contenu à l'intérieur de chaque porte. Vous pouvez également ajouter un effet de neige et de la musique. À l'intérieur de chaque porte, vous pouvez ajouter un son, une vidéo, un texte, une image ou un lien.

Notez qu'il est facile pour les utilisateurs avertis de révéler immédiatement le contenu de toutes les portes. Si vous prévoyez de révéler de grands secrets les jours suivants, vous devez attendre ce jour-là avant d'ajouter vos grands secrets au calendrier.",
        ),
        'H5P.Crossword' => array(
            "title" => "Mots croisés",
            "summary" => "Créez une grille de mots croisés",
            "description" => "Construisez et personnalisez de beaux mots croisés pour engager votre public. Les mots croisés sont hautement personnalisables, ce qui vous permet de configurer toutes les couleurs, de télécharger une image de fond, de décider de la façon dont les scores doivent être attribués et même de rendre les mots aléatoires afin que votre public obtienne une nouvelle grille à chaque fois si vous le souhaitez.",
        ),
        'H5P.SortParagraphs' => array(
            "title" => "Trier les paragraphes",
            "summary" => "Créez un ensemble de paragraphes à trier",
            "description" => "Tapez ou collez une liste de paragraphes qui seront randomisés. Vous pouvez par exemple faire en sorte que chaque paragraphe soit une partie d'une chanson, un bloc de code ou les étapes d'une recette. Les apprenants doivent classer les paragraphes dans l'ordre correct. Par défaut, les apprenants obtiendront un point pour chaque paragraphe qui suit le paragraphe qu'il est censé suivre, mais vous pouvez décider d'accorder un point pour chaque paragraphe qui se trouve à la bonne place.",
        ),
        'H5P.MultiMediaChoice' => array(
            "title" => "Choix de l'image",
            "summary" => "Créez une tâche où les choix sont des images",
            "description" => "Créez de superbes questions à choix multiple ou unique où les choix sont des images. Vous pouvez personnaliser la mise en page des choix et choisir entre des ratios d'images fixes ou simplement utiliser les ratios que les images ont déjà.",
        ),
        'H5P.Cornell' => array(
            "title" => "Notes de Cornell",
            "summary" => "Prendre des notes en utilisant le système Cornell",
            "description" => "Présentez aux apprenants une vidéo, un texte ou un document audio et encouragez-les à prendre des notes à l'aide du système de prise de notes Cornell.",
        ),
        'H5P.ARScavenger' => array(
            "title" => "AR Scavenger",
            "summary" => "Le plaisir de la réalité augmentée !",
            "description" => "Laissez les apprenants explorer la réalité augmentée avec des modèles 3D ou des exercices H5P. Vous pouvez définir des marqueurs semblables à des codes QR que vos élèves peuvent scanner avec l'appareil photo de leur appareil. Ces marqueurs peuvent déclencher le mélange d'un modèle 3D de votre choix avec la vue de la caméra, ou ils peuvent afficher une interaction H5P.",
        ),
        'H5P.StructureStrip' => array(
            "title" => "Bande de structure",
            "summary" => "Bande de structure interactive",
            "description" => "Une bande de structure est traditionnellement placée à côté d'une feuille de papier (ou même collée dessus). Elle fournit aux élèves un échafaudage pour un texte et les aide à maintenir les longueurs des différents segments de texte dans de bonnes proportions. Avec la bande de structure, vous pouvez maintenant utiliser la même approche dans H5P sans papier.",
        ),
        'H5P.InfoWall' => array(
            "title" => "Mur d'information",
            "summary" => "Créez des panneaux d'informations que les utilisateurs peuvent filtrer par mots-clés pertinents.",
            "description" => "Permettez aux utilisateurs de parcourir facilement toutes les informations ou de les filtrer par mots-clés afin de rechercher des informations spécifiques. En tant qu'auteur, vous pouvez configurer un panneau maître et décider d'ajouter une image par panneau, etc.",
        ),
        'H5P.GameMap' => array(
            "title" => "Carte de jeu",
            "summary" => "Permettez à vos élèves de choisir leurs exercices sur une carte de jeu.",
            "description" => "Une carte de jeu est composée d'étapes que vous pouvez disposer sur une image d'arrière-plan. Chaque étape est reliée à une ou plusieurs autres étapes et peut contenir un type de contenu H5P que l'utilisateur peut consulter ou compléter. Vous pouvez définir des règles qui détermineront vers quelle étape l'utilisateur est autorisé à se déplacer, ce qui vous permet de créer une expérience de type jeu.",
        ),
    );

    add_fr_translations($translations);
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
        2019030700,
        2020080400,
        2020080401,
        2020082800,
        2020091500,
        2020112600,
        2021060400,
        2023012500,
        2023013100,
        2026050600,
        2026071000,
    ];

    foreach ($upgrades as $version) {
        if ($oldversion < $version) {
            call_user_func("hvp_upgrade_{$version}");
            upgrade_mod_savepoint(true, $version, 'hvp');
        }
    }

    return true;
}
