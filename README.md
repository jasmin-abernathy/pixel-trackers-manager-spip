# Pixel Trackers Manager — SPIP

Portage SPIP de **Pixel Trackers Manager (PTM)**, développé par **Le Potager du Web**.

> État : `0.0.1` / `etat=dev` — pré-développement validé sur la structure de Vues Imprenables. Ne pas installer en production tant que le premier scan réel n'a pas été testé sur une copie isolée.

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
- scripts et polices spécifiques observés localement ;
- page `mentions` gérée par squelette, donc à traiter en lecture/recommandation et jamais par écrasement automatique ;
- `affichage_final` retenu comme point de travail futur pour un blocage léger et cache-compatible ;
- `taches_generales_cron` retenu pour les futurs scans planifiés.

Voir `docs/AUDIT-VUESIMPRENABLES-2026-09-01.md`.

## Ce qui est déjà préparé

- `paquet.xml` en état `dev`, plage provisoire SPIP 4.3 à 4.4 ;
- entrée PTM dans l'espace privé via `prive/squelettes/contenu/` ;
- accès réservé aux administrateurs ;
- registre JSON de signatures indépendant du CMS ;
- analyseur HTML minimal réutilisable ;
- pipelines `insert_head`, `affichage_final` et `taches_generales_cron` déclarés mais sans effet en production à ce stade ;
- documentation d'architecture, matrice WordPress → SPIP et audit réel.

## Prochaine étape technique

1. installer ce plugin sur une copie exécutable de Vues Imprenables ;
2. confirmer la version exacte du cœur SPIP ;
3. recenser les URLs publiques ;
4. réaliser un premier scan progressif en contexte anonyme ;
5. stocker scans et observations ;
6. seulement ensuite prototyper le blocage avant consentement.

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
- `docs/ROADMAP.md`

## Licence

GPL-2.0-or-later, cohérente avec PTM WordPress. Voir `LICENSE`.
