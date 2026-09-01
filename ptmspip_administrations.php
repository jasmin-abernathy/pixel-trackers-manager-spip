<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ptmspip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = [];
	$maj['create'] = [
		['maj_tables', ['spip_ptmspip_scans', 'spip_ptmspip_urls', 'spip_ptmspip_findings']],
	];

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function ptmspip_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_ptmspip_findings');
	sql_drop_table('spip_ptmspip_urls');
	sql_drop_table('spip_ptmspip_scans');
	effacer_meta($nom_meta_base_version);
}
