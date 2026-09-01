<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Charge le catalogue neutre de signatures PTM.
 *
 * @return array
 */
function ptmspip_charger_regles() {
	$path = find_in_path('data/rules.json');
	if (!$path || !is_readable($path)) {
		return array('schema_version' => 1, 'services' => array());
	}

	$decoded = json_decode((string) file_get_contents($path), true);
	if (!is_array($decoded) || empty($decoded['services']) || !is_array($decoded['services'])) {
		return array('schema_version' => 1, 'services' => array());
	}

	return $decoded;
}

/**
 * Analyse un HTML déjà récupéré. Cette fonction ne crawle aucune URL et
 * n'effectue aucun appel externe.
 *
 * @param string $html
 * @return array<int,array<string,string>>
 */
function ptmspip_analyser_html($html) {
	$html = (string) $html;
	$regles = ptmspip_charger_regles();
	$resultats = array();

	foreach ($regles['services'] as $service) {
		if (empty($service['id']) || empty($service['patterns']) || !is_array($service['patterns'])) {
			continue;
		}

		foreach ($service['patterns'] as $pattern) {
			if ($pattern !== '' && stripos($html, (string) $pattern) !== false) {
				$resultats[] = array(
					'id' => (string) $service['id'],
					'label' => isset($service['label']) ? (string) $service['label'] : (string) $service['id'],
					'category' => isset($service['category']) ? (string) $service['category'] : 'unknown',
					'status' => 'active_evidence',
					'evidence' => (string) $pattern,
				);
				break;
			}
		}
	}

	return $resultats;
}
