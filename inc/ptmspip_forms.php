<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Repère les formulaires susceptibles de collecter des données personnelles.
 * Un simple formulaire de recherche n'est pas signalé.
 */
function ptmspip_extraire_formulaires_donnees($html, $page_url = '') {
	$html = (string) $html;
	if ($html === '' || !preg_match_all('~<form\b([^>]*)>(.*?)</form>~is', $html, $forms, PREG_SET_ORDER)) {
		return [];
	}

	$page_host = strtolower((string) parse_url((string) $page_url, PHP_URL_HOST));
	$page_host = preg_replace('~^www\.~', '', $page_host);
	$resultats = [];
	$field_hints = [
		'email' => '~(?:type\s*=\s*["\']email["\']|name\s*=\s*["\'][^"\']*(?:email|mail)[^"\']*["\'])~i',
		'telephone' => '~(?:type\s*=\s*["\']tel["\']|name\s*=\s*["\'][^"\']*(?:tel|phone|mobile)[^"\']*["\'])~i',
		'mot_de_passe' => '~type\s*=\s*["\']password["\']~i',
		'fichier' => '~type\s*=\s*["\']file["\']~i',
		'identite' => '~name\s*=\s*["\'][^"\']*(?:nom|name|prenom|firstname|lastname|societe|company)[^"\']*["\']~i',
		'adresse' => '~name\s*=\s*["\'][^"\']*(?:adresse|address|postal|zip|ville|city)[^"\']*["\']~i',
		'message' => '~<(?:textarea|input)\b[^>]*name\s*=\s*["\'][^"\']*(?:message|comment|texte|content)[^"\']*["\']~i',
	];

	foreach ($forms as $form) {
		$attrs = (string) $form[1];
		$body = (string) $form[2];
		$full = $attrs . ' ' . $body;
		$fields = [];
		foreach ($field_hints as $label => $regex) {
			if (preg_match($regex, $full)) {
				$fields[] = $label;
			}
		}

		$action = '';
		if (preg_match('~\baction\s*=\s*(["\'])(.*?)\1~is', $attrs, $m)) {
			$action = html_entity_decode(trim((string) $m[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		if ($action && preg_match('~^https?://~i', $action)) {
			$host = strtolower((string) parse_url($action, PHP_URL_HOST));
			$host = preg_replace('~^www\.~', '', $host);
			if ($host && (!$page_host || $host !== $page_host)) {
				$id = 'external_form:' . substr($host, 0, 100);
				$resultats[$id] = [
					'id' => $id,
					'label' => 'Formulaire vers ' . $host,
					'category' => 'external_form',
					'status' => 'human_review',
					'evidence' => $action,
				];
			}
		}

		if ($fields) {
			sort($fields);
			$id = 'form_data_collection';
			$existing = isset($resultats[$id]['fields']) ? $resultats[$id]['fields'] : [];
			$merged = array_values(array_unique(array_merge($existing, $fields)));
			sort($merged);
			$resultats[$id] = [
				'id' => $id,
				'label' => 'Formulaire collectant potentiellement des données personnelles',
				'category' => 'data_collection',
				'status' => 'human_review',
				'evidence' => 'Champs repérés : ' . implode(', ', $merged),
				'fields' => $merged,
			];
		}
	}

	foreach ($resultats as &$finding) {
		unset($finding['fields']);
	}
	unset($finding);
	return array_values($resultats);
}
