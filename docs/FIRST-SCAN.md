# Premier scan progressif — 0.0.2-dev

Cette étape implémente le premier crawl réel de PTM SPIP sans mécanisme de consentement.

## Périmètre

Le scanner recense :

- la page d'accueil ;
- tous les articles `publie` ;
- toutes les rubriques `publie` ;
- la page `mentions` si un squelette correspondant est présent.

Les URLs sont générées par SPIP lui-même. L'administrateur ne peut pas saisir une URL arbitraire à crawler.

## Progressivité

Un lot contient 5 URLs. Une erreur HTTP n'arrête pas le scan et est stockée avec l'URL concernée.

## Contexte anonyme

Les pages sont récupérées côté serveur avec `recuperer_url()` et aucune session navigateur/admin n'est transmise. Cela limite notamment les faux positifs provoqués par Crayons.

## Observations

Deux familles sont enregistrées :

1. `active_evidence` : signature PTM connue réellement trouvée dans le HTML ;
2. `human_review` : ressource HTTP(S) tierce chargée par `script`, `iframe`, `img`, `link`, `source`, `video`, `audio` ou `object`, mais pas encore connue du catalogue.

Les simples liens sortants `<a href>` ne sont pas signalés comme traceurs.

## Stockage

Trois tables sont créées :

- `spip_ptmspip_scans` ;
- `spip_ptmspip_urls` ;
- `spip_ptmspip_findings`.

Le scan conserve un historique et ne modifie aucun contenu SPIP.

## Étape suivante

Tester cette version sur une copie exécutable de Vues Imprenables. Après validation :

- enrichissement du recensement d'URLs si nécessaire ;
- amélioration du catalogue commun WordPress/SPIP ;
- automatisation progressive via le génie SPIP ;
- seulement ensuite prototype de consentement.
