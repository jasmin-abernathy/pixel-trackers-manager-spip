<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ptmspip_recenser_urls_publiques() {
	include_spip('inc/urls');
	include_spip('inc/filtres');

	$urls = [];
	$ajouter = static function ($url, $objet, $id_objet = 0) use (&$urls) {
		$url = trim((string) $url);
		if (!$url || !preg_match('~^https?://~i', $url)) {
			return;
		}
		$hash = sha1($url);
		if (!isset($urls[$hash])) {
			$urls[$hash] = [
				'url' => $url,
				'objet' => (string) $objet,
				'id_objet' => (int) $id_objet,
			];
		}
	};

	$adresse_site = rtrim((string) ($GLOBALS['meta']['adresse_site'] ?? ''), '/');
	if ($adresse_site) {
		$ajouter($adresse_site . '/', 'sommaire', 0);
	}

	foreach (sql_allfetsel('id_article', 'spip_articles', "statut='publie'", '', 'id_article ASC') as $row) {
		$id = (int) $row['id_article'];
		$ajouter(generer_objet_url_absolue($id, 'article'), 'article', $id);
	}

	foreach (sql_allfetsel('id_rubrique', 'spip_rubriques', "statut='publie'", '', 'id_rubrique ASC') as $row) {
		$id = (int) $row['id_rubrique'];
		$ajouter(generer_objet_url_absolue($id, 'rubrique'), 'rubrique', $id);
	}

	if (find_in_path('mentions.html', 'squelettes/')) {
		$ajouter(url_absolue(generer_url_public('mentions')), 'page', 0);
	}

	return array_values($urls);
}

function ptmspip_demarrer_scan() {
	$urls = ptmspip_recenser_urls_publiques();
	$date = date('Y-m-d H:i:s');

	sql_updateq('spip_ptmspip_scans', ['statut' => 'abandoned', 'date_fin' => $date], "statut='running'");

	$id_scan = (int) sql_insertq('spip_ptmspip_scans', [
		'date_debut' => $date,
		'date_fin' => '0000-00-00 00:00:00',
		'statut' => 'running',
		'total_urls' => count($urls),
	]);

	foreach ($urls as $item) {
		sql_insertq('spip_ptmspip_urls', [
			'id_scan' => $id_scan,
			'objet' => $item['objet'],
			'id_objet' => $item['id_objet'],
			'url' => $item['url'],
			'url_hash' => sha1($item['url']),
			'statut' => 'pending',
		]);
	}

	if (!$urls) {
		sql_updateq('spip_ptmspip_scans', ['statut' => 'finished', 'date_fin' => $date], 'id_scan=' . $id_scan);
	}

	return $id_scan;
}

function ptmspip_traiter_lot_scan($id_scan, $limite = 5) {
	$id_scan = (int) $id_scan;
	$limite = max(1, min(20, (int) $limite));
	if (!$id_scan) {
		return ['processed' => 0, 'finished' => true];
	}

	$scan = sql_fetsel('*', 'spip_ptmspip_scans', 'id_scan=' . $id_scan);
	if (!$scan || $scan['statut'] !== 'running') {
		return ['processed' => 0, 'finished' => true];
	}

	$rows = sql_allfetsel('*', 'spip_ptmspip_urls', 'id_scan=' . $id_scan . " AND statut='pending'", '', 'id_url ASC', '0,' . $limite);
	$processed = 0;

	foreach ($rows as $row) {
		ptmspip_scanner_une_url($row);
		$processed++;
	}

	ptmspip_recalculer_compteurs_scan($id_scan);
	$pending = (int) sql_countsel('spip_ptmspip_urls', 'id_scan=' . $id_scan . " AND statut='pending'");
	if ($pending === 0) {
		sql_updateq('spip_ptmspip_scans', [
			'statut' => 'finished',
			'date_fin' => date('Y-m-d H:i:s'),
		], 'id_scan=' . $id_scan);
	}

	return ['processed' => $processed, 'finished' => ($pending === 0)];
}

/**
 * Le crawl reste strictement sur le domaine du site, y compris après redirection.
 * www. et le domaine nu sont considérés équivalents.
 */
function ptmspip_valider_url_scan($url) {
	$site = (string) ($GLOBALS['meta']['adresse_site'] ?? '');
	$site_host = strtolower((string) parse_url($site, PHP_URL_HOST));
	$target_host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));
	$site_host = preg_replace('~^www\.~', '', $site_host);
	$target_host = preg_replace('~^www\.~', '', $target_host);

	if (!$site_host || !$target_host || $site_host !== $target_host) {
		return false;
	}

	return (string) $url;
}

function ptmspip_scanner_une_url($row) {
	$id_url = (int) ($row['id_url'] ?? 0);
	$id_scan = (int) ($row['id_scan'] ?? 0);
	$url = (string) ($row['url'] ?? '');
	if (!$id_url || !$id_scan || !$url) {
		return false;
	}

	include_spip('inc/distant');
	include_spip('inc/ptmspip_scanner');

	$res = recuperer_url($url, [
		'transcoder' => true,
		'taille_max' => 2 * 1024 * 1024,
		'follow_location' => 3,
		'headers' => ['User-Agent' => 'PTM-SPIP/0.0.2 local privacy scanner'],
		'callback_valider_url' => 'ptmspip_valider_url_scan',
	]);

	if (!$res || empty($res['page'])) {
		sql_updateq('spip_ptmspip_urls', [
			'statut' => 'error',
			'erreur' => 'Impossible de récupérer la page.',
			'date_scan' => date('Y-m-d H:i:s'),
		], 'id_url=' . $id_url);
		return false;
	}

	$status = isset($res['status']) ? (int) $res['status'] : 0;
	if ($status && ($status < 200 || $status >= 400)) {
		sql_updateq('spip_ptmspip_urls', [
			'statut' => 'error',
			'http_status' => $status,
			'erreur' => 'Réponse HTTP ' . $status,
			'date_scan' => date('Y-m-d H:i:s'),
		], 'id_url=' . $id_url);
		return false;
	}

	$findings = ptmspip_analyser_html((string) $res['page'], $url);
	sql_delete('spip_ptmspip_findings', 'id_url=' . $id_url);

	foreach ($findings as $finding) {
		$evidence = (string) ($finding['evidence'] ?? '');
		sql_insertq('spip_ptmspip_findings', [
			'id_scan' => $id_scan,
			'id_url' => $id_url,
			'service_id' => substr((string) ($finding['id'] ?? 'unknown'), 0, 120),
			'label' => (string) ($finding['label'] ?? 'Service inconnu'),
			'category' => substr((string) ($finding['category'] ?? 'unknown'), 0, 64),
			'status' => substr((string) ($finding['status'] ?? 'active_evidence'), 0, 32),
			'evidence' => $evidence,
			'evidence_hash' => sha1($evidence),
		]);
	}

	sql_updateq('spip_ptmspip_urls', [
		'statut' => 'done',
		'http_status' => $status,
		'nb_findings' => count($findings),
		'erreur' => '',
		'date_scan' => date('Y-m-d H:i:s'),
	], 'id_url=' . $id_url);

	return true;
}

function ptmspip_recalculer_compteurs_scan($id_scan) {
	$id_scan = (int) $id_scan;
	$processed = (int) sql_countsel('spip_ptmspip_urls', 'id_scan=' . $id_scan . " AND statut IN ('done','error')");
	$errors = (int) sql_countsel('spip_ptmspip_urls', 'id_scan=' . $id_scan . " AND statut='error'");
	$findings = (int) sql_countsel('spip_ptmspip_findings', 'id_scan=' . $id_scan);

	sql_updateq('spip_ptmspip_scans', [
		'processed_urls' => $processed,
		'error_urls' => $errors,
		'findings_count' => $findings,
	], 'id_scan=' . $id_scan);
}

function ptmspip_dernier_scan() {
	return sql_fetsel('*', 'spip_ptmspip_scans', '', '', 'id_scan DESC', '0,1');
}

function ptmspip_resume_scan_html($id_scan = 0) {
	$scan = $id_scan ? sql_fetsel('*', 'spip_ptmspip_scans', 'id_scan=' . (int) $id_scan) : ptmspip_dernier_scan();
	if (!$scan) {
		return '<p>Aucun scan n’a encore été lancé.</p>';
	}

	$total = max(0, (int) $scan['total_urls']);
	$processed = max(0, (int) $scan['processed_urls']);
	$percent = $total ? min(100, (int) round(($processed / $total) * 100)) : 100;
	$status = ptmspip_html((string) $scan['statut']);

	$html = '<div class="ptmspip-scan-summary">';
	$html .= '<p><strong>Scan #' . (int) $scan['id_scan'] . '</strong> — statut : <code>' . $status . '</code></p>';
	$html .= '<p><progress max="100" value="' . $percent . '">' . $percent . '%</progress> <strong>' . $percent . '%</strong> — ' . $processed . '/' . $total . ' URL(s)</p>';
	$html .= '<p>' . (int) $scan['findings_count'] . ' observation(s) · ' . (int) $scan['error_urls'] . ' erreur(s)</p>';

	$findings = sql_allfetsel('*', 'spip_ptmspip_findings', 'id_scan=' . (int) $scan['id_scan'], '', 'id_finding DESC', '0,30');
	if ($findings) {
		$html .= '<h3>Dernières observations</h3><div class="table-responsive"><table class="spip liste"><thead><tr><th>Service / domaine</th><th>Type</th><th>État</th><th>Page</th></tr></thead><tbody>';
		foreach ($findings as $finding) {
			$url = (string) sql_getfetsel('url', 'spip_ptmspip_urls', 'id_url=' . (int) $finding['id_url']);
			$html .= '<tr>';
			$html .= '<td><strong>' . ptmspip_html((string) $finding['label']) . '</strong><br><small>' . ptmspip_html((string) $finding['evidence']) . '</small></td>';
			$html .= '<td>' . ptmspip_html((string) $finding['category']) . '</td>';
			$html .= '<td>' . ptmspip_html((string) $finding['status']) . '</td>';
			$html .= '<td><a href="' . ptmspip_html($url) . '" rel="noreferrer" target="_blank">' . ptmspip_html($url) . '</a></td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table></div>';
	}

	$errors = sql_allfetsel('url,http_status,erreur', 'spip_ptmspip_urls', 'id_scan=' . (int) $scan['id_scan'] . " AND statut='error'", '', 'id_url DESC', '0,10');
	if ($errors) {
		$html .= '<h3>Erreurs récentes</h3><ul class="spip">';
		foreach ($errors as $error) {
			$html .= '<li><code>' . (int) $error['http_status'] . '</code> ' . ptmspip_html((string) $error['url']) . ' — ' . ptmspip_html((string) $error['erreur']) . '</li>';
		}
		$html .= '</ul>';
	}

	$html .= '</div>';
	return $html;
}

function ptmspip_html($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
