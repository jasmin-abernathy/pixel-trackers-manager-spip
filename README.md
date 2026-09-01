# Pixel Trackers Manager — SPIP

Portage SPIP de **Pixel Trackers Manager (PTM)**, développé par **Le Potager du Web**.

> État : `0.0.2` / `etat=dev` — premier scan progressif codé. À tester sur une copie exécutable avant toute utilisation en production.

## Objectif

Retrouver les principes de PTM WordPress dans l'écosystème SPIP : audit local-first, détection de traceurs et services tiers, documentation de confidentialité, recommandations et, à terme, gestion du consentement.

## Audit réel déjà effectué

Une copie du site SPIP Vues Imprenables a été analysée le 1er septembre 2026.

L'audit a confirmé notamment :

- squelettes maison classiques avec variantes par rubrique ;
- `#INSERT_HEAD` disponible sur les pages principales ;
- contenus éditoriaux injectés via `#TEXTE`, donc nécessité d'analyser le HTML public rendu ;
- présence de Crayons, Image Typo et Squelettes par Rubrique dans `plugins/auto` ;
- absence de tracker tiers évident codé en dur dans les squelettes fournis ;
- page `mentions` gérée par squelette, donc jamais réécrite automatiquement ;
- `affichage_final` retenu comme point de travail futur pour un blocage léger et cache-compatible ;
- `taches_generales_cron` retenu pour les futurs scans planifiés.

Voir `docs/AUDIT-VUESIMPRENABLES-2026-09-01.md`.

## Premier scan progressif — 0.0.2

La première implémentation réelle sait maintenant :

- créer ses tables de scans, URLs et observations ;
- recenser l'accueil, les articles publiés, les rubriques publiées et la page `mentions` ;
- générer les URLs avec les fonctions natives SPIP ;
- récupérer les pages côté serveur sans transmettre la session administrateur ;
- traiter 5 URLs maximum par requête ;
- poursuivre le scan si une URL échoue ;
- détecter les signatures du catalogue PTM ;
- signaler les ressources tierces inconnues pour vérification humaine ;
- ignorer les simples liens sortants ;
- afficher progression, résultats et erreurs dans l'espace privé.

Le scan ne modifie aucun contenu, aucun squelette et n'active aucun mécanisme de consentement.

Voir `docs/FIRST-SCAN.md`.

## Architecture

- `data/rules.json` : catalogue de signatures indépendant du CMS ;
- `inc/ptmspip_scanner.php` : analyse du HTML ;
- `inc/ptmspip_scan.php` : recensement, file d'attente et traitement progressif ;
- `base/ptmspip.php` : tables de stockage ;
- `formulaires/ptmspip_scan.*` : interface CVT du scanner ;
- `prive/squelettes/contenu/ptmspip.html` : page de l'espace privé ;
- `ptmspip_pipelines.php` : points d'accroche préparés pour la suite.

## Prochaine étape technique

1. installer `0.0.2` sur une copie exécutable de Vues Imprenables ;
2. confirmer la version exacte du cœur SPIP ;
3. vérifier la création/mise à jour des tables ;
4. lancer un scan complet par lots ;
5. comparer les résultats au HTML réellement servi ;
6. corriger les faux positifs/faux négatifs ;
7. seulement ensuite automatiser le scan via le génie SPIP et prototyper le consentement.

## Principes

- observer avant de demander ;
- local par défaut ;
- aucune publication juridique silencieuse ;
- refus du consentement aussi simple que l'acceptation ;
- fermeture d'une bannière ≠ consentement ;
- écriture prudente : ne jamais réécrire un squelette que PTM ne comprend pas ;
- distinguer preuve active, intégration potentielle et vérification humaine.

## Documentation

- `docs/ARCHITECTURE.md`
- `docs/PORTAGE-MATRIX.md`
- `docs/AUDIT-SITE-SPIP.md`
- `docs/AUDIT-VUESIMPRENABLES-2026-09-01.md`
- `docs/FIRST-SCAN.md`
- `docs/ROADMAP.md`

## Licence

GPL-2.0-or-later, cohérente avec PTM WordPress. Voir `LICENSE`.
