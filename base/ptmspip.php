<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les tables de scan PTM SPIP.
 */
function ptmspip_declarer_tables_auxiliaires($tables) {
	$scans = [
		'id_scan' => 'bigint(21) NOT NULL',
		'date_debut' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		'date_fin' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		'statut' => "varchar(16) DEFAULT 'running' NOT NULL",
		'total_urls' => "int UNSIGNED DEFAULT '0' NOT NULL",
		'processed_urls' => "int UNSIGNED DEFAULT '0' NOT NULL",
		'error_urls' => "int UNSIGNED DEFAULT '0' NOT NULL",
		'findings_count' => "int UNSIGNED DEFAULT '0' NOT NULL",
	];
	$scans_keys = [
		'PRIMARY KEY' => 'id_scan',
		'KEY statut' => 'statut',
		'KEY date_debut' => 'date_debut',
	];

	$urls = [
		'id_url' => 'bigint(21) NOT NULL',
		'id_scan' => "bigint(21) DEFAULT '0' NOT NULL",
		'objet' => "varchar(32) DEFAULT '' NOT NULL",
		'id_objet' => "bigint(21) DEFAULT '0' NOT NULL",
		'url' => "text DEFAULT '' NOT NULL",
		'url_hash' => "char(40) DEFAULT '' NOT NULL",
		'statut' => "varchar(16) DEFAULT 'pending' NOT NULL",
		'http_status' => "int UNSIGNED DEFAULT '0' NOT NULL",
		'nb_findings' => "int UNSIGNED DEFAULT '0' NOT NULL",
		'erreur' => "text DEFAULT '' NOT NULL",
		'date_scan' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	];
	$urls_keys = [
		'PRIMARY KEY' => 'id_url',
		'UNIQUE KEY scan_url' => 'id_scan,url_hash',
		'KEY scan_statut' => 'id_scan,statut',
	];

	$findings = [
		'id_finding' => 'bigint(21) NOT NULL',
		'id_scan' => "bigint(21) DEFAULT '0' NOT NULL",
		'id_url' => "bigint(21) DEFAULT '0' NOT NULL",
		'service_id' => "varchar(120) DEFAULT '' NOT NULL",
		'label' => "text DEFAULT '' NOT NULL",
		'category' => "varchar(64) DEFAULT 'unknown' NOT NULL",
		'status' => "varchar(32) DEFAULT 'active_evidence' NOT NULL",
		'evidence' => "text DEFAULT '' NOT NULL",
		'evidence_hash' => "char(40) DEFAULT '' NOT NULL",
	];
	$findings_keys = [
		'PRIMARY KEY' => 'id_finding',
		'UNIQUE KEY url_service' => 'id_url,service_id,evidence_hash',
		'KEY scan' => 'id_scan',
		'KEY service_id' => 'service_id',
	];

	$tables['spip_ptmspip_scans'] = ['field' => &$scans, 'key' => &$scans_keys];
	$tables['spip_ptmspip_urls'] = ['field' => &$urls, 'key' => &$urls_keys];
	$tables['spip_ptmspip_findings'] = ['field' => &$findings, 'key' => &$findings_keys];

	return $tables;
}
