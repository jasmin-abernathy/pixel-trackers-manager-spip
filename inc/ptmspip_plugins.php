<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ptmspip_charger_profils_plugins() {
	$path = find_in_path('data/spip-plugins.json');
	if (!$path || !is_readable($path)) {
		return ['schema_version' => 1, 'plugins' => []];
	}
	$decoded = json_decode((string) file_get_contents($path), true);
	if (!is_array($decoded) || empty($decoded['plugins']) || !is_array($decoded['plugins'])) {
		return ['schema_version' => 1, 'plugins' => []];
	}
	return $decoded;
}

function ptmspip_lister_plugins_actifs() {
	include_spip('plugins/installer');
	$raw = function_exists('liste_plugin_actifs') ? liste_plugin_actifs() : [];
	$resultats = [];

	foreach ((array) $raw as $key => $infos) {
		$infos = is_array($infos) ? $infos : [];
		$prefix = '';
		foreach (['prefixe', 'prefix'] as $field) {
			if (!empty($infos[$field])) {
				$prefix = strtolower((string) $infos[$field]);
				break;
			}
		}
		if (!$prefix && is_string($key) && !ctype_digit($key)) {
			$prefix = strtolower($key);
		}
		if (strpos($prefix, '/') !== false || strpos($prefix, '\\') !== false) {
			$prefix = strtolower(basename(str_replace('\\', '/', $prefix)));
		}
		if (!$prefix && !empty($infos['dir'])) {
			$prefix = strtolower(basename(rtrim(str_replace('\\', '/', (string) $infos['dir']), '/')));
		}
		$prefix = preg_replace('~[^a-z0-9_\-]~', '', (string) $prefix);
		if (!$prefix) {
			continue;
		}

		$label = $prefix;
		foreach (['nom', 'name', 'titre'] as $field) {
			if (!empty($infos[$field]) && is_scalar($infos[$field])) {
				$label = trim(strip_tags((string) $infos[$field]));
				break;
			}
		}
		$version = '';
		foreach (['version', 'version_base'] as $field) {
			if (!empty($infos[$field]) && is_scalar($infos[$field])) {
				$version = trim((string) $infos[$field]);
				break;
			}
		}

		$resultats[$prefix] = [
			'prefix' => $prefix,
			'label' => $label ?: $prefix,
			'version' => $version,
		];
	}

	ksort($resultats);
	return array_values($resultats);
}

function ptmspip_plugin_semble_pertinent($prefix, $label = '') {
	$haystack = strtolower((string) $prefix . ' ' . (string) $label);
	$needles = [
		'analytics', 'stat', 'matomo', 'umami', 'pixel', 'track', 'cookie', 'consent',
		'form', 'contact', 'mail', 'newsletter', 'subscriber', 'captcha', 'spam',
		'map', 'gis', 'geo', 'social', 'facebook', 'twitter', 'mastodon', 'oembed',
		'video', 'gravatar', 'avatar', 'pay', 'bank', 'stripe', 'paypal', 'inscri',
		'forum', 'comment', 'petition', 'oauth', 'openid', 'login', 'auth', 'sso',
	];
	foreach ($needles as $needle) {
		if (strpos($haystack, $needle) !== false) {
			return true;
		}
	}
	return false;
}

function ptmspip_analyser_plugins_actifs() {
	$catalogue = ptmspip_charger_profils_plugins();
	$profils = [];
	foreach ((array) ($catalogue['plugins'] ?? []) as $profil) {
		if (!empty($profil['prefix'])) {
			$profils[strtolower((string) $profil['prefix'])] = $profil;
		}
	}

	$resultats = [];
	foreach (ptmspip_lister_plugins_actifs() as $plugin) {
		$prefix = strtolower((string) $plugin['prefix']);
		$profil = $profils[$prefix] ?? null;
		if ($profil) {
			$resultats[] = array_merge($plugin, [
				'known' => true,
				'role' => (string) ($profil['role'] ?? 'unknown'),
				'privacy_kind' => (string) ($profil['privacy_kind'] ?? 'review'),
				'severity' => (string) ($profil['severity'] ?? 'review'),
				'services' => array_values((array) ($profil['services'] ?? [])),
				'note' => (string) ($profil['note'] ?? ''),
			]);
		} else {
			$relevant = ptmspip_plugin_semble_pertinent($prefix, $plugin['label']);
			$resultats[] = array_merge($plugin, [
				'known' => false,
				'role' => 'unclassified',
				'privacy_kind' => $relevant ? 'unclassified_relevant' : 'unclassified',
				'severity' => $relevant ? 'review' : 'info',
				'services' => [],
				'note' => $relevant
					? 'Plugin non encore catalogué dont le nom suggère une fonction liée aux données ou à un service externe : vérification humaine.'
					: 'Plugin actif non classé par PTM. Ce n’est pas une alerte en soi.',
			]);
		}
	}
	return $resultats;
}

function ptmspip_statut_plugin($privacy_kind) {
	$map = [
		'tracker_integration' => 'integration_detected',
		'analytics_integration' => 'integration_detected',
		'data_collection' => 'data_processing',
		'data_collection_capability' => 'capability_detected',
		'data_processing' => 'data_processing',
		'local_processing' => 'local_processing',
		'local_functionality' => 'local_only',
		'external_processing' => 'external_processing',
		'external_processing_capability' => 'external_capability',
		'external_content_capability' => 'external_capability',
		'local_security' => 'local_only',
		'links_only' => 'links_only',
		'consent_manager' => 'consent_manager',
		'legal_documentation' => 'legal_helper',
		'unclassified_relevant' => 'human_review',
	];
	return $map[$privacy_kind] ?? 'human_review';
}

function ptmspip_enregistrer_plugins_scan($id_scan) {
	$id_scan = (int) $id_scan;
	if (!$id_scan) {
		return 0;
	}
	$nb = 0;
	foreach (ptmspip_analyser_plugins_actifs() as $plugin) {
		if (empty($plugin['known']) && $plugin['privacy_kind'] !== 'unclassified_relevant') {
			continue;
		}
		$evidence = 'Plugin SPIP actif : ' . $plugin['prefix'] . ($plugin['version'] ? ' ' . $plugin['version'] : '');
		$service_id = (empty($plugin['known']) ? 'spip_plugin_unknown:' : 'spip_plugin:') . $plugin['prefix'];
		sql_insertq('spip_ptmspip_findings', [
			'id_scan' => $id_scan,
			'id_url' => 0,
			'service_id' => substr($service_id, 0, 120),
			'label' => (string) $plugin['label'],
			'category' => substr((string) $plugin['role'], 0, 64),
			'status' => substr(ptmspip_statut_plugin((string) $plugin['privacy_kind']), 0, 32),
			'evidence' => $evidence . ($plugin['note'] ? ' — ' . $plugin['note'] : ''),
			'evidence_hash' => sha1($evidence),
		]);
		$nb++;
	}
	return $nb;
}

function ptmspip_resume_plugins_html() {
	$plugins = ptmspip_analyser_plugins_actifs();
	$known = array_filter($plugins, static function ($p) { return !empty($p['known']); });
	$relevant_unknown = array_filter($plugins, static function ($p) { return empty($p['known']) && $p['privacy_kind'] === 'unclassified_relevant'; });
	$unknown = array_filter($plugins, static function ($p) { return empty($p['known']) && $p['privacy_kind'] !== 'unclassified_relevant'; });

	$html = '<div class="ptmspip-plugin-inventory">';
	$html .= '<p><strong>' . count($plugins) . '</strong> plugin(s) actif(s) inventorié(s) · <strong>' . count($known) . '</strong> profil(s) PTM reconnu(s) · <strong>' . count($relevant_unknown) . '</strong> plugin(s) non classé(s) à vérifier.</p>';

	if ($known || $relevant_unknown) {
		$html .= '<div class="table-responsive"><table class="spip liste"><thead><tr><th>Plugin</th><th>Rôle</th><th>Lecture PTM</th><th>À savoir</th></tr></thead><tbody>';
		foreach (array_merge(array_values($known), array_values($relevant_unknown)) as $plugin) {
			$html .= '<tr>';
			$html .= '<td><strong>' . ptmspip_plugins_html($plugin['label']) . '</strong><br><code>' . ptmspip_plugins_html($plugin['prefix']) . '</code>' . ($plugin['version'] ? ' <small>' . ptmspip_plugins_html($plugin['version']) . '</small>' : '') . '</td>';
			$html .= '<td>' . ptmspip_plugins_html($plugin['role']) . '</td>';
			$html .= '<td><code>' . ptmspip_plugins_html(ptmspip_statut_plugin($plugin['privacy_kind'])) . '</code></td>';
			$html .= '<td>' . ptmspip_plugins_html($plugin['note']) . '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table></div>';
	}

	if ($unknown) {
		$prefixes = array_map(static function ($p) { return $p['prefix']; }, array_values($unknown));
		$html .= '<details><summary>' . count($unknown) . ' autre(s) plugin(s) actif(s) non classé(s)</summary><p><code>' . ptmspip_plugins_html(implode(', ', $prefixes)) . '</code></p><p>Ils restent couverts par le scan du HTML et des domaines externes. « Non classé » ne signifie pas « dangereux ».</p></details>';
	}
	$html .= '</div>';
	return $html;
}

function ptmspip_plugins_html($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
