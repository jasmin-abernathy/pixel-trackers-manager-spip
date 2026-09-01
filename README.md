# Pixel Trackers Manager — SPIP

Portage SPIP de **Pixel Trackers Manager (PTM)**, développé par **Le Potager du Web**.

> État : `0.0.1-dev` — squelette de pré-développement. Ne pas installer en production avant l'audit d'un site SPIP représentatif.

## Objectif

Retrouver les principes de PTM WordPress dans l'écosystème SPIP : audit local-first, détection de traceurs et services tiers, documentation de confidentialité, recommandations et, à terme, gestion du consentement.

## Ce qui est déjà préparé

- `paquet.xml` compatible provisoirement SPIP 4.2 à 4.4 ;
- entrée PTM dans l'espace privé ;
- accès réservé aux administrateurs ;
- diagnostic d'environnement ;
- registre JSON de signatures indépendant du CMS ;
- analyseur HTML minimal réutilisable ;
- documentation d'architecture et matrice WordPress → SPIP ;
- checklist exacte des fichiers à analyser quand une copie de site SPIP sera disponible.

## Ce qui attend la copie du site SPIP

- stratégie de crawl des URLs réellement publiées ;
- inspection des squelettes, modèles, formulaires et plugins actifs ;
- détection des inclusions dynamiques et oEmbed ;
- stockage des scans et journal ;
- génération / mise à jour des pages légales ;
- blocage de scripts/iframes avant consentement ;
- interface de consentement ;
- tâches planifiées.

Aucune de ces fonctions ne sera simulée ou activée sans validation sur un vrai environnement SPIP.

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
- `docs/ROADMAP.md`

## Licence

GPL-2.0-or-later, cohérente avec PTM WordPress. Voir `LICENSE`.
