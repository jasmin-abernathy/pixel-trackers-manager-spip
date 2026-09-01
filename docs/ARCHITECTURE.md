# Architecture PTM SPIP

## Principe

PTM doit séparer autant que possible le moteur de règles du CMS.

```text
catalogue PTM commun
        ↓
normalisation des observations
        ↓
┌──────────────────┬──────────────────┐
│ adaptateur WP     │ adaptateur SPIP  │
│ hooks/options     │ pipelines/meta   │
│ shortcodes        │ modèles/balises  │
│ WP-Cron           │ génie/jobs       │
└──────────────────┴──────────────────┘
```

## Couches prévues

1. `data/` : signatures et définitions indépendantes du CMS.
2. `inc/` : moteur d'analyse et normalisation des résultats.
3. adaptateur SPIP : découverte des contenus, squelettes, modèles, plugins et configuration.
4. espace privé : synthèse, scan, recommandations, documentation et journal.
5. consentement : couche séparée, inactive tant qu'elle n'a pas été validée sur les squelettes réels.

## Règle de sécurité

PTM ne doit jamais modifier automatiquement un squelette ou un contenu qu'il ne sait pas reconstruire de façon réversible.

## Stockage envisagé

À décider après audit : `spip_meta` peut suffire pour les petits réglages ; des tables propres seront préférables pour historique de scans, observations et journal si le volume le justifie.

## Planification

Le scan planifié devra s'appuyer sur les mécanismes de tâches SPIP plutôt que sur une implémentation WordPress transposée directement.
