# Politique de sécurité

PTM SPIP est en pré-développement.

Ne jamais publier dans une issue : mots de passe, clés API, dumps de base, fichiers `config/connect.php`, données personnelles ou exports clients.

Pour une vulnérabilité pouvant exposer des données, contourner les droits SPIP ou affaiblir le consentement, utiliser un canal privé de signalement plutôt qu'une issue publique.

Avant toute version testable en production, le projet devra vérifier au minimum : autorisations SPIP, actions sécurisées, échappement des sorties, appels externes explicites, stockage des scans et comportement du cache.
