<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Point d'injection public pour les futurs assets de consentement.
 *
 * Vues Imprenables inclut #INSERT_HEAD sur ses pages principales, ce qui
 * permet d'utiliser ce pipeline sans modifier les squelettes du site.
 * Le socle reste volontairement sans effet tant que le consentement n'est
 * pas implémenté et testé sur une copie isolée.
 */
function ptmspip_insert_head($flux) {
	return $flux;
}

/**
 * Point de transformation du HTML final.
 *
 * Cible retenue pour le futur blocage pré-consentement : SPIP appelle ce
 * pipeline juste avant l'envoi au navigateur et ses modifications ne sont
 * pas stockées dans le cache. Pour rester frugal, toute future implémentation
 * devra sortir immédiatement si aucune signature pertinente n'est présente.
 */
function ptmspip_affichage_final($page) {
	return $page;
}

/**
 * Point de déclaration des scans périodiques.
 *
 * Aucun cron PTM n'est activé dans cette version de développement.
 */
function ptmspip_taches_generales_cron($taches) {
	return $taches;
}
