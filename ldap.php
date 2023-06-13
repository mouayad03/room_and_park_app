<?php
// LDAP variables
$ldap_host = 'ldap://ldap.forumsys.com';
$ldap_port = '389';
$ldap_dn = 'dc=example,dc=com';
$ldap_user = 'cn=admin,' . $ldap_dn;
$ldap_pass = 'password';

// Connect to LDAP server
$ldap_conn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Bind LDAP user
ldap_bind($ldap_conn, $ldap_user, $ldap_pass);

// Search for user
$search_filter = '(uid=mathew)';
$search_base = 'dc=example,dc=com';
$search_result = ldap_search($ldap_conn, $search_base, $search_filter);
if (!$search_result) {
  echo 'LDAP search failed.';
  exit;
}
$search_entries = ldap_get_entries($ldap_conn, $search_result);
if (!$search_entries) {
  echo 'No entries found.';
  exit;
}

// Display user attributes
foreach ($search_entries[0] as $attr => $value) {
  if (is_numeric($attr)) {
    continue;
  }
  echo $attr . ': ' . $value[0] . '<br>';
}

// Close LDAP connection
ldap_close($ldap_conn);



?>