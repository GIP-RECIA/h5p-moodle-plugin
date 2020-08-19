INSERT INTO mdl_hvp_libraries_hub_cache_fr (machine_name,title,summary,description) SELECT machine_name,title,summary,description FROM mdl_hvp_libraries_hub_cache;

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Présentation de cours",
	summary = "Créez une présentation avec un diaporama interactif",
	description = "Les présentations de cours comprennent des diapositives qui incluent du multimédia, du texte et différents types d'interractions comme des résumés interactifs, des questions à choix multiple et des vidéos interactives. Les élèves peuvent découvrir de nouvelles méthodes d'apprentissage et tester leurs connaissances et leur mémoire. Comme toujours avec H5P, le contenu est éditable dans les navigateurs web et la présentation de cours inclue un outils de création WYSIWYG. Une utilisation typique de la présentation de cours consiste à présenter le sujet sur quelques diapositives et de les faire suivre par des diapositives qui permettront à l'usager de tester ses connaissances. La présentation de cours peut cependant être utilisée de plein de façons différentes, comme outil de présentation en classe ou comme un jeu en utilisant les boutons de navigation dans les diapositives pour permettre à l'utilisateur de faire des choix et d'en apprécier les conséquences" where machine_name = 'H5P.CoursePresentation';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET summary = "Télachargez un enregistrement audio",
	description = "Téléchargez un enregistrement audio en .mp3, .wav, .ogg ou fournissez le lien d'un enregistrement audio."
	WHERE machine_name = 'H5P.Audio';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Enregistrement audio",
	summary = "Créez un enregistrement audio",
	description = "Un enregistreur audio HTML5. Enregistrez-vous et écoutez-vous ou téléchargez un fichier .wav de votre enregistrement."
	WHERE machine_name = 'H5P.AudioRecorder';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Cartes de dialogue",
	summary = "Créez des cartes tournantes basées sur des textes",
	description = "Les cartes de dialogue peuvent aider les apprenants à mémoriser des mots, des expressions ou des souvenirs. Sur le recto de la carte, il y a un indice correspondant à un mot ou une expression. En tournant la carte, l'apprenant révèle ce mot ou cette expression. Les cartes de dialogue peuvent être utilisées pour les langues, les mathématiques, l'histoire, etc..."
	WHERE machine_name = 'H5P.Dialogcards';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Glisser-Déposer",
	summary = "Créez des des glisser-déposer sur des images",
	description = "Le glisser-déposer permet d'associer 2 éléments ou plus afin de réaliser visuellement des connexions logiques. Créez des exercices de glisser-déposer en utilisant du texte et/ou des images qui pourront être déplacés pour trouver la solution. Glisser-Déposer prend en charge les relations un à un, un à plusieurs, plusieurs à un et plusieurs à plusieurs entrer les questions et les réponses."
	WHERE machine_name = 'H5P.DragQuestion';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Déplacer des mots",
	summary = "Créez des exercices de glisser-déposer basés sur du texte"
	description = "Déplacer des mots permet de créer des textes auxquels il manque des morceaux. L'utilisateur devra glisser les morceaux de texte manquant pour reconstituer le texte complet. Cet outil permet de réfléchir au contenu d'un texte, de vérifier que l'utilisateur se souvient d'un texte qu'il a lu ou si il comprend ce qu'il lit... C'est super facile de créer un exercice, l'éditeur écrit simplement le texte et entoure les mots qui doivent être déplacés avec des astérisques, comme par exemple : *MotADéplacer*"
	WHERE machine_name = 'H5P.DragText';

UPDATE mdl_hvp_libraries_hub_cache_fr
 	SET title = "Remplir les vides ",
	summary = "Créez des textes avec des mots manquant ",
	 description = "L'apprenant saisira les mots qui manquent dans un texte. Il saura si sa réponse est la bonne après chaque saisie ou après avoir saisi tous les mots, en fonction du paramétrage de l'exercice. Les auteurs saisissent le texte et marquent les mots à remplacer avec des astérisques. Les exercices créés peuvent être utilisés dans tous les domaines d'apprentissage : langues et grammaire, mathématiques, géographie, histoire, etc... "
	WHERE machine_name = 'H5P.Blanks';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Hotspots Multiples",
	summary = "Créez plusieurs points que les utilisateurs devront trouver sur une image",
	description = "Hotspots Multiples permet aux enseignants de créer un exercice basé sur une image. Les apprenants devront trouver, de façon très ludique, les points qui correspondent à la question posée."
	WHERE machine_name = 'H5P.ImageMultipleHotspotQuestion';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Hotspot",
	summary = "Créer un point sur une image que les utilisateurs devront retrouver",
	description = "Hotspot  permet aux utilisateurs de répondre à une question en cliquant sur un élément d'une image. L'enseignant télécharge une image et définit différents points correspondant à des détails ou des sections de l'image. Les points peuvent être définis comme corrects ou incorrects, avec un commentaire approprié qui s'affiche lorsque l'apprenant clique dessus."
	WHERE machine_name = 'H5P.ImageHotspotQuestion';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Devinez la réponse",
	summary = "Créez une question et une réponse associées à une image",
	description = "Ce type d'exercice permet aux enseignants de télécharger une image et d'y associer une question. Les apprenants peuvent deviner la réponse et appuyer sur un bouton pour vérifier que leur réponse est correcte. C'est un exercice qui permet d'effectuer des révisions."
	WHERE machine_name = 'H5P.GuessTheAnswer';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Juxtaposition d'images",
	summary = "Comparez deux images de manière interactive",
	description = "Juxtaposition d'images permet aux utilisateurs de comparer deux images de façon interactive, comme par exemple avant et après un événement."
	WHERE machine_name = 'H5P.ImageJuxtaposition';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Carrousel",
	summary = "Créez facilement un carrousel d'images",
	description = "Présentez vos images facilement sous forme de carrousel (diaporama). L'enseignant télécharge des images et fournie des commentaires pour ces images. Les 2 images qui suivent l'image affichée sont préchargées de façon à fluidifier l'affichage. Le diaporama peut être affiché en plein écran ou dans une page pour laquelle le dimensionnement des images sera géré par le système. Les enseignants peuvent décider de gérer les proportions différemment."
	 WHERE machine_name = 'H5P.ImageSlider';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Vidéo interactive",
	summary = "Créez des vidéos interactives",
	description = "Ajoutez de l'interactivité à votre vidéo avec des explications, des images supplémentaires, des tableaux, des champs à remplir et des questions à choix multiple. Les questions peuvent permettre de passer à une autre partie de la vidéo en fonction de la réponse de l'utilisateur. Des résumés interactifs peuvent être ajoutés à la fin de la vidéo. Les vidéos interactives sont créées et modifiées depuis un navigateur standard."
	WHERE machine_name = 'H5P.InteractiveVideo';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Marquez les mots",
	summary = "Créez un exercice où les utilisateurs mettent les mots en évidence",
	description = "Marquez les mots permet aux apprenants de sélectionner les mots d'un texte qui répondent à une question posée. L'enseignant entre le texte et marque les mots que l'apprenant devra sélectionner (les bonnes réponses) en les entourant d'astérisques : *MotAMarquer*",
	WHERE machine_name = 'H5P.MarkTheWords';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Jeu de mémoire",
	summary = "Créez un jeu d'association d'images",
	description = "Créez vos propres jeux de mémoire et testez la mémoire de vos apprenants."
	WHERE machine_name = 'H5P.MemoryGame';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Choix multiple",
	summary = "Créez des questions à choix multiple flexibles",
	description = "Choix multiple est un outil d'évaluation. L'apprenant évalue immédiatement le résultat. Chaque question peut avoir une ou plusieurs réponses correctes."
	WHERE machine_name = 'H5P.MultiChoice';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Quiz (ensemble de questions)",
	summary = "Créez une série de différents types de questions",
	description = "Le quiz permet à l'apprenant de répondre à une série de questions présentées sous différentes formes tels que des questions  à choix multiple, des glisser-déposer, des remplissages de trous dans un texte. L'enseignant peut utiliser de nombreux paramètres pour régler le comportement du quiz. Il peut par exemple placer des images d'arrière plan, définir un pourcentage de réussite de l'apprenant, faire jouer une vidéo à la fin du quiz qui pourra être différente en fonction du résultat de l'apprenant."
	WHERE machine_name = 'H5P.QuestionSet';

UPDATE mdl_hvp_libraries_hub_cache_fr
	SET title = "Frise chronologique (Timeline)",
	summary = "Créez une chronologie d'événements alimentée de contenus multimédia",
	description = "La Frise chronologique permet de placer une séquence d'événements dans un ordre chronologique. Pour chaque événements, l'enseignant peut ajouter des images. Il peut également inclure des objets provenant de Twitter, Youtube, Vimeo, Google Maps et SoundCloud. Cet outil est issu de Timeline.js, développé par Knight Lab."
	WHERE machine_name = 'H5P.Timeline';