# Couverture de l'écosystème SPIP — 2026-09-02

## Objectif

PTM-SPIP ne doit pas fonctionner comme une simple liste de cookies. Il combine :

1. l'inventaire des plugins SPIP actifs ;
2. des profils de plugins connus ;
3. l'analyse du HTML réellement rendu ;
4. des signatures de services externes ;
5. la détection générique de domaines externes inconnus ;
6. la détection des formulaires susceptibles de collecter des données personnelles.

La présence d'un plugin n'est jamais une preuve de traceur.

## Familles SPIP couvertes

Le catalogue 0.2.1 contient **45 profils SPIP** et couvre en priorité les familles pertinentes de l'écosystème SPIP 4.4 :

- statistiques : Stats, Stats objets, Google Analytics, Matomo, Umami ;
- formulaires et contributions : Formidable, Saisies, Contact, Contact libre, CVT Upload, Forum, Comments, Pétitions, inscriptions Agenda ;
- newsletters et e-mail : MailSubscribers, MailShot, Newsletters, Ma lettre, Facteur, Rebonds ;
- anti-spam / CAPTCHA : NoSPAM, Captcha Addition, FB Antispam + détection reCAPTCHA, hCaptcha et Turnstile dans le rendu ;
- médias et embeds : oEmbed, oEmbed fake, YouTube, Vimeo, Dailymotion, SoundCloud, Spotify, Twitch, Instagram, Twitter ;
- cartes et géocodage : GIS, Google Maps, OpenStreetMap, Nominatim, Photon ;
- réseaux sociaux : Social Tags, Facebook Models, Sociaux, Twitter, Mastodon ;
- avatars : Gravatar ;
- consentement : Tarteaucitron, Cookiebar, CookieChoices, CIBC ;
- paiements : Banque&paiement, Formidable paiement, Reservation Bank + signatures Stripe/PayPal ;
- documentation et échanges de données : Pages, Import ICS, ciimport ;
- fonctionnalités locales connues : Crayons, Image Typo, Squelettes par rubrique.

Les trois derniers profils sont explicitement classés `local_functionality`. Ils sont connus de PTM afin de réduire le bruit, sans être transformés en alerte RGPD.

## Plugins inconnus

PTM utilise l'API native SPIP `liste_plugin_actifs()` pour inventorier tous les plugins actifs.

Un plugin absent du catalogue reste affiché. Si son préfixe ou son nom suggère une fonction liée aux statistiques, formulaires, e-mail, consentement, cartographie, réseaux sociaux, paiement, authentification ou données personnelles, PTM le marque pour vérification humaine.

« Non classé » ne signifie pas « dangereux ».

## Formulaires

Le scanner analyse aussi les formulaires du HTML rendu. Il signale les pages qui semblent collecter notamment :

- e-mail ;
- téléphone ;
- identité ;
- adresse ;
- message/commentaire ;
- mot de passe ;
- fichier.

Il signale également une action de formulaire HTTP(S) qui pointe directement vers un autre domaine.

Cette heuristique permet de repérer une collecte même si elle vient d'un squelette maison ou d'un plugin que PTM ne connaît pas.

Un cas réel a confirmé qu'un formulaire local de type contact peut collecter e-mail, sujet et message tout en utilisant un anti-spam local : PTM doit signaler la collecte sans inventer de CAPTCHA tiers ni de traceur.

## Services techniques

PTM Rules 0.2.1 embarqué par le plugin contient 32 signatures techniques. Les services reconnus dans le HTML sont des preuves actives ; les services seulement associés à un plugin restent des possibilités à confirmer.

## Garde-fous

- un simple lien sortant n'est pas un tracker ;
- une URL présente uniquement dans un commentaire ou une licence de fichier JavaScript ne doit pas devenir une preuve de service actif lors d'un futur scan statique ;
- Stats SPIP est classé comme traitement local, pas comme service tiers ;
- Saisies est une capacité de formulaire, pas une collecte prouvée ;
- NoSPAM et Captcha Addition ne sont pas confondus avec un CAPTCHA distant ;
- Crayons, Image Typo et Squelettes par rubrique sont des fonctionnalités locales connues ;
- un CMP n'est pas un tracker ;
- un plugin de cartographie ou oEmbed peut avoir plusieurs fournisseurs : le HTML rendu décide du fournisseur réellement utilisé.

## Limite assumée

Cette couverture n'est pas une promesse mathématique de connaître pour toujours tous les plugins SPIP existants. L'écosystème évolue et des plugins privés peuvent exister.

La robustesse vient donc du cumul : catalogue connu + inventaire de tous les plugins + heuristiques + observation du HTML + signalement des domaines tiers inconnus.

La prochaine couche de vérification sera un test navigateur réel, afin de détecter les requêtes créées dynamiquement par JavaScript et invisibles dans le HTML initial.
