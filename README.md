# Pixel Trackers Manager — SPIP

Portage SPIP de **Pixel Trackers Manager (PTM)**, développé par **Le Potager du Web**.

> État : `0.0.3` / `etat=dev` — scan progressif + inventaire de l'écosystème SPIP. À tester sur une copie exécutable avant toute utilisation en production.

## Objectif

Retrouver les principes de PTM WordPress dans l'écosystème SPIP : audit local-first, détection de traceurs et services tiers, cartographie des traitements, documentation de confidentialité, recommandations et, à terme, gestion du consentement.

## Couverture SPIP — 0.0.3

PTM ne dépend plus uniquement d'une liste de domaines. Il combine désormais :

- inventaire de **tous les plugins SPIP actifs** via l'API native ;
- **42 profils RGPD** de plugins SPIP couvrant les principales familles pertinentes de SPIP 4.4 ;
- **32 signatures techniques** de services/traceurs dans PTM Rules 0.2.0 ;
- analyse du HTML réellement rendu ;
- détection générique de ressources tierces inconnues ;
- détection des formulaires susceptibles de collecter des données personnelles ;
- signalement des formulaires envoyant directement vers un autre domaine.

PTM distingue explicitement : traceur observé, intégration potentielle, traitement local, collecte de données, service externe, gestionnaire de consentement et vérification humaine.

La présence de Formidable, GIS, oEmbed ou d'un plugin analytics n'est donc jamais transformée automatiquement en « tracker détecté » : le HTML rendu sert de preuve lorsque c'est possible.

Voir `docs/SPIP-ECOSYSTEM-COVERAGE.md`.

## Audit réel déjà effectué

Une copie du site SPIP Vues Imprenables a été analysée le 1er septembre 2026. Cet audit a confirmé notamment la nécessité d'analyser le HTML public rendu, car les contenus SPIP via `#TEXTE`, les modèles et les plugins peuvent introduire des services absents des fichiers de squelette.

Voir `docs/AUDIT-VUESIMPRENABLES-2026-09-01.md`.

## Scan progressif

Le scanner sait :

- créer ses tables de scans, URLs et observations ;
- recenser l'accueil, les articles publiés, les rubriques publiées et la page `mentions` ;
- récupérer les pages côté serveur sans transmettre la session administrateur ;
- traiter 5 URLs maximum par requête et continuer en cas d'erreur ;
- détecter les signatures PTM Rules ;
- signaler les ressources tierces inconnues ;
- ignorer les simples liens sortants lors de la détection générique de ressources ;
- repérer dans les formulaires les champs de type e-mail, téléphone, identité, adresse, message, mot de passe ou fichier ;
- afficher progression, résultats, erreurs et inventaire des plugins dans l'espace privé.

Le scan ne modifie aucun contenu, aucun squelette et n'active aucun mécanisme de consentement.

## Architecture

- `data/rules.json` : snapshot local de PTM Rules ;
- `data/spip-plugins.json` : profils SPIP locaux ;
- `inc/ptmspip_plugins.php` : inventaire et classification des plugins actifs ;
- `inc/ptmspip_scanner.php` : analyse du HTML ;
- `inc/ptmspip_forms.php` : heuristiques de collecte via formulaires ;
- `inc/ptmspip_scan.php` : recensement, file d'attente et traitement progressif ;
- `base/ptmspip.php` : tables de stockage ;
- `formulaires/ptmspip_scan.*` : interface CVT du scanner ;
- `prive/squelettes/contenu/ptmspip.html` : page de l'espace privé ;
- `ptmspip_pipelines.php` : points d'accroche préparés pour la suite.

## Prochaine validation

1. installer `0.0.3` sur une copie exécutable ;
2. vérifier l'inventaire réel des plugins actifs ;
3. lancer un scan complet par lots ;
4. comparer les résultats au HTML et au réseau réellement servis ;
5. corriger les faux positifs/faux négatifs ;
6. ajouter ensuite le test navigateur pour les requêtes créées dynamiquement par JavaScript.

## Principes

- observer avant de demander ;
- local par défaut ;
- aucune publication juridique silencieuse ;
- un plugin installé n'est pas une preuve de tracking ;
- un lien sortant n'est pas un tracker ;
- distinguer preuve active, capacité, traitement et vérification humaine ;
- ne jamais réécrire un squelette que PTM ne comprend pas.

## Documentation

- `docs/ARCHITECTURE.md`
- `docs/PORTAGE-MATRIX.md`
- `docs/AUDIT-SITE-SPIP.md`
- `docs/AUDIT-VUESIMPRENABLES-2026-09-01.md`
- `docs/FIRST-SCAN.md`
- `docs/SPIP-ECOSYSTEM-COVERAGE.md`
- `docs/ROADMAP.md`

## Licence

GPL-2.0-or-later, cohérente avec PTM WordPress. Voir `LICENSE`.
