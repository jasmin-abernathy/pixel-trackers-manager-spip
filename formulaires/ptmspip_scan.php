<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_ptmspip_scan_charger_dist() {
	if (!autoriser('voir', 'ptmspip')) {
		return false;
	}

	include_spip('inc/ptmspip_scan');
	$scan = ptmspip_dernier_scan();

	return [
		'id_scan' => $scan ? (int) $scan['id_scan'] : 0,
		'can_continue' => ($scan && $scan['statut'] === 'running') ? 'oui' : '',
		'resume_html' => ptmspip_resume_scan_html($scan ? (int) $scan['id_scan'] : 0),
	];
}

function formulaires_ptmspip_scan_verifier_dist() {
	return [];
}

function formulaires_ptmspip_scan_traiter_dist() {
	if (!autoriser('voir', 'ptmspip')) {
		return ['message_erreur' => 'Accès refusé.'];
	}

	include_spip('inc/ptmspip_scan');
	$action = (string) _request('ptm_action');

	if ($action === 'start') {
		$id_scan = ptmspip_demarrer_scan();
		$result = ptmspip_traiter_lot_scan($id_scan, 5);
		return [
			'message_ok' => $result['finished'] ? 'Scan terminé.' : 'Scan démarré : premier lot de 5 pages traité.',
			'editable' => true,
		];
	}

	if ($action === 'next') {
		$id_scan = (int) _request('id_scan');
		$result = ptmspip_traiter_lot_scan($id_scan, 5);
		return [
			'message_ok' => $result['finished'] ? 'Scan terminé.' : 'Lot suivant traité. Vous pouvez continuer.',
			'editable' => true,
		];
	}

	return ['message_erreur' => 'Action de scan inconnue.', 'editable' => true];
}
