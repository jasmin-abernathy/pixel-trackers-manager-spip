<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ptmspip_autoriser() {
	// Point d'entrée du pipeline autoriser.
}

function autoriser_ptmspip_bouton_dist($faire, $type, $id, $qui, $opt) {
	return isset($qui['statut']) && $qui['statut'] === '0minirezo';
}

function autoriser_ptmspip_voir_dist($faire, $type, $id, $qui, $opt) {
	return isset($qui['statut']) && $qui['statut'] === '0minirezo';
}
