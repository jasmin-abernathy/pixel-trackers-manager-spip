<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ptmspip_charger_regles() {
	$path = find_in_path('data/rules.json');
	if (!$path || !is_readable($path)) {
		return ['schema_version' => 1, 'services' => []];
	}

	$decoded = json_decode((string) file_get_contents($path), true);
	if (!is_array($decoded) || empty($decoded['services']) || !is_array($decoded['services'])) {
		return ['schema_version' => 1, 'services' => []];
	}

	return $decoded;
}

/**
 * Analyse un HTML déjà récupéré. Aucun appel réseau n'est effectué ici.
 */
function ptmspip_analyser_html($html, $page_url = '') {
	$html = (string) $html;
	$regles = ptmspip_charger_regles();
	$resultats = [];
	$ids = [];

	foreach ($regles['services'] as $service) {
		if (empty($service['id']) || empty($service['patterns']) || !is_array($service['patterns'])) {
			continue;
		}

		foreach ($service['patterns'] as $pattern) {
			if ($pattern !== '' && stripos($html, (string) $pattern) !== false) {
				$id = (string) $service['id'];
				$resultats[] = [
					'id' => $id,
					'label' => isset($service['label']) ? (string) $service['label'] : $id,
					'category' => isset($service['category']) ? (string) $service['category'] : 'unknown',
					'status' => 'active_evidence',
					'evidence' => (string) $pattern,
				];
				$ids[$id] = true;
				break;
			}
		}
	}

	foreach (ptmspip_extraire_ressources_externes($html, $page_url, $regles) as $finding) {
		if (!isset($ids[$finding['id']])) {
			$resultats[] = $finding;
			$ids[$finding['id']] = true;
		}
	}

	return $resultats;
}

/**
 * Repère des ressources HTTP(S) tierces non encore connues du catalogue.
 * Les liens de navigation <a href> sont volontairement exclus : un lien sortant
 * n'est pas, à lui seul, un chargement tiers ou un traceur.
 */
function ptmspip_extraire_ressources_externes($html, $page_url = '', $regles = null) {
	if (!is_array($regles)) {
		$regles = ptmspip_charger_regles();
	}

	$site_url = $page_url ?: ($GLOBALS['meta']['adresse_site'] ?? '');
	$site_host = ptmspip_normaliser_host((string) parse_url($site_url, PHP_URL_HOST));
	$scheme = (string) parse_url($site_url, PHP_URL_SCHEME);
	if ($scheme !== 'https' && $scheme !== 'http') {
		$scheme = 'https';
	}

	$patterns_connus = [];
	foreach ($regles['services'] ?? [] as $service) {
		foreach ($service['patterns'] ?? [] as $pattern) {
			$patterns_connus[] = strtolower((string) $pattern);
		}
	}

	$findings = [];
	$regex = '~<(script|iframe|img|link|source|video|audio|object)\b[^>]*\b(src|href|poster|data)\s*=\s*(["\'])(.*?)\3~is';
	if (!preg_match_all($regex, (string) $html, $matches, PREG_SET_ORDER)) {
		return [];
	}

	foreach ($matches as $match) {
		$url = html_entity_decode(trim((string) $match[4]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		if (strpos($url, '//') === 0) {
			$url = $scheme . ':' . $url;
		}
		if (!preg_match('~^https?://~i', $url)) {
			continue;
		}

		$host = ptmspip_normaliser_host((string) parse_url($url, PHP_URL_HOST));
		if (!$host || ($site_host && $host === $site_host)) {
			continue;
		}

		$known = false;
		$lower_url = strtolower($url);
		foreach ($patterns_connus as $pattern) {
			if ($pattern !== '' && strpos($lower_url, $pattern) !== false) {
				$known = true;
				break;
			}
		}
		if ($known) {
			continue;
		}

		$id = 'external_domain:' . substr($host, 0, 100);
		if (isset($findings[$id])) {
			continue;
		}

		$findings[$id] = [
			'id' => $id,
			'label' => $host,
			'category' => 'external_resource',
			'status' => 'human_review',
			'evidence' => $url,
		];
	}

	return array_values($findings);
}

function ptmspip_normaliser_host($host) {
	$host = strtolower(trim((string) $host));
	return preg_replace('~^www\.~', '', $host);
}
