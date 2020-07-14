<?php
namespace App\Library; use Hashids\Hashids; class Helper { public static function getMysqlDate($sp874c58 = 0) { return date('Y-m-d', time() + $sp874c58 * 24 * 3600); } public static function getIP() { if (isset($_SERVER)) { if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { $spc3acf5 = $_SERVER['HTTP_X_FORWARDED_FOR']; } else { if (isset($_SERVER['HTTP_CLIENT_IP'])) { $spc3acf5 = $_SERVER['HTTP_CLIENT_IP']; } else { $spc3acf5 = @$_SERVER['REMOTE_ADDR']; } } } else { if (getenv('HTTP_X_FORWARDED_FOR')) { $spc3acf5 = getenv('HTTP_X_FORWARDED_FOR'); } else { if (getenv('HTTP_CLIENT_IP')) { $spc3acf5 = getenv('HTTP_CLIENT_IP'); } else { $spc3acf5 = getenv('REMOTE_ADDR'); } } } if (strpos($spc3acf5, ',') !== FALSE) { $spce0999 = explode(',', $spc3acf5); return $spce0999[0]; } return $spc3acf5; } public static function getClientIP() { if (isset($_SERVER)) { $spc3acf5 = $_SERVER['REMOTE_ADDR']; } else { $spc3acf5 = getenv('REMOTE_ADDR'); } if (strpos($spc3acf5, ',') !== FALSE) { $spce0999 = explode(',', $spc3acf5); return $spce0999[0]; } return $spc3acf5; } public static function filterWords($spec9993, $sp3135c5) { if (!$spec9993) { return false; } if (!is_array($sp3135c5)) { $sp3135c5 = explode('|', $sp3135c5); } foreach ($sp3135c5 as $spc4c50b) { if ($spc4c50b && strpos($spec9993, $spc4c50b) !== FALSE) { return $spc4c50b; } } return false; } public static function is_idcard($sp82c95f) { if (strlen($sp82c95f) == 18) { return self::idcard_checksum18($sp82c95f); } elseif (strlen($sp82c95f) == 15) { $sp82c95f = self::idcard_15to18($sp82c95f); return self::idcard_checksum18($sp82c95f); } else { return false; } } private static function idcard_verify_number($sp750112) { if (strlen($sp750112) != 17) { return false; } $spc346e3 = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); $sp18fae3 = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); $sp46d2e9 = 0; for ($sp8f2283 = 0; $sp8f2283 < strlen($sp750112); $sp8f2283++) { $sp46d2e9 += substr($sp750112, $sp8f2283, 1) * $spc346e3[$sp8f2283]; } $sp57a36a = $sp46d2e9 % 11; $sp59d982 = $sp18fae3[$sp57a36a]; return $sp59d982; } private static function idcard_15to18($sp628f6e) { if (strlen($sp628f6e) != 15) { return false; } else { if (array_search(substr($sp628f6e, 12, 3), array('996', '997', '998', '999')) !== false) { $sp628f6e = substr($sp628f6e, 0, 6) . '18' . substr($sp628f6e, 6, 9); } else { $sp628f6e = substr($sp628f6e, 0, 6) . '19' . substr($sp628f6e, 6, 9); } } $sp628f6e = $sp628f6e . self::idcard_verify_number($sp628f6e); return $sp628f6e; } private static function idcard_checksum18($sp628f6e) { if (strlen($sp628f6e) != 18) { return false; } $sp750112 = substr($sp628f6e, 0, 17); if (self::idcard_verify_number($sp750112) != strtoupper(substr($sp628f6e, 17, 1))) { return false; } else { return true; } } public static function str_between($spec9993, $sp75920f, $sp34cb7f) { $sp6b541a = strpos($spec9993, $sp75920f); if ($sp6b541a === false) { return ''; } $spe72bbc = strpos($spec9993, $sp34cb7f, $sp6b541a + strlen($sp75920f)); if ($spe72bbc === false || $sp6b541a >= $spe72bbc) { return ''; } $sp460191 = strlen($sp75920f); $spa87332 = substr($spec9993, $sp6b541a + $sp460191, $spe72bbc - $sp6b541a - $sp460191); return $spa87332; } public static function str_between_longest($spec9993, $sp75920f, $sp34cb7f) { $sp6b541a = strpos($spec9993, $sp75920f); if ($sp6b541a === false) { return ''; } $spe72bbc = strrpos($spec9993, $sp34cb7f, $sp6b541a + strlen($sp75920f)); if ($spe72bbc === false || $sp6b541a >= $spe72bbc) { return ''; } $sp460191 = strlen($sp75920f); $spa87332 = substr($spec9993, $sp6b541a + $sp460191, $spe72bbc - $sp6b541a - $sp460191); return $spa87332; } public static function format_url($sp69c1d6) { if (!strlen($sp69c1d6)) { return $sp69c1d6; } if (!starts_with($sp69c1d6, 'http://') && !starts_with($sp69c1d6, 'https://')) { $sp69c1d6 = 'http://' . $sp69c1d6; } while (ends_with($sp69c1d6, '/')) { $sp69c1d6 = substr($sp69c1d6, 0, -1); } return $sp69c1d6; } public static function lite_hash($spec9993) { $sp630572 = crc32((string) $spec9993); if ($sp630572 < 0) { $sp630572 &= 1 << 7; } return $sp630572; } const ID_TYPE_USER = 0; const ID_TYPE_CATEGORY = 1; const ID_TYPE_PRODUCT = 2; const ID_TYPE_AFFILIATE = 3; public static function id_encode($sp138835, $spa0789d, ...$sp142d66) { $spc6db57 = new Hashids(config('app.key'), 8, 'abcdefghijklmnopqrstuvwxyz1234567890'); return @$spc6db57->encode(self::lite_hash($sp138835), $sp138835, self::lite_hash($spa0789d), $spa0789d, ...$sp142d66); } public static function id_decode($spb3ed8c, $spa0789d, &$spc88927 = false) { if (strlen($spb3ed8c) < 8) { $spc6db57 = new Hashids(config('app.key')); if ($spa0789d === self::ID_TYPE_USER) { return intval(@$spc6db57->decodeHex($spb3ed8c)); } else { return intval(@$spc6db57->decode($spb3ed8c)[0]); } } $spc6db57 = new Hashids(config('app.key'), 8, 'abcdefghijklmnopqrstuvwxyz1234567890'); $spc88927 = @$spc6db57->decode($spb3ed8c) ?? array(); return intval($spc88927[1]); } public static function is_mobile() { if (isset($_SERVER['HTTP_USER_AGENT'])) { if (preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT'])) { return true; } } return false; } public static function b1_rand_background() { if (self::is_mobile()) { $spe81858 = array('//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpgyq8n5j20u01hcne2.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpfyjbd0j20u01hcte2.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpw3b5mkj20u01hcnfh.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoybkicbj20u01hc7de.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpes8rmmj20u01hctn7.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp8ond6gj20u01hctji.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp4ljhhvj20u01hck0r.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpstrwnsj20u01hc7he.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxq2a1vthj20u01hc4gs.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpiebjztj20u01hcaom.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxow4b14kj20u01hc43x.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxohtyvgfj20u01hc7gk.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp6vexa3j20u01hcdj3.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxqa0zhc6j20u01hc14e.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxomnbr0gj20u01hc79r.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpx57f0sj20u01hcqmd.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoozjilyj20u01hcgt9.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxprigfw1j20u01hcam9.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxod70fcpj20u01hcajj.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpzb5p1tj20u01hcnca.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxozvry57j20u01hcgwo.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpv092lfj20u01hcx1o.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpdz6s0bj20u01hcaqj.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoso79ayj20u01hcq9c.jpg', '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpqjrtjhj20u01hcapi.jpg'); } else { $spe81858 = array('//ww1.sinaimg.cn/large/ac1a0c4agy1ftz78cfrj2j21hc0u0kio.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7qj6l3xj21hc0u0b29.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9tqa2fvpj21hc0u017a.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz71m76skj21hc0u0nnq.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz709py6fj21hc0u0wx2.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgqv33lj21hc0u04qp.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9s9soh4sj21hc0u01kx.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9s9r2vkzj21hc0u0x4e.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7etbcs8j21hc0u07p3.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgn1bluj21hc0u0kiy.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7r6tmv1j21hc0u0anj.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7c4h0xzj21hc0u01kx.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9tq7uypvj21hc0u01be.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4pjgbncj21hc0u0kjl.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7i6u1gxj21hc0u0tyk.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4s0fb2tj21hc0u01ky.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz72wkr9dj21hc0u0h1r.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7tj5ohrj21hc0u0qnp.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgp23zbj21hc0u0txl.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7l9dcokj21hc0u0k9k.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4lvumu1j21hc0u0x6p.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7alxyhnj21hc0u0nkh.jpg', '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz799gvb3j21hc0u0qdt.jpg'); } return $spe81858[rand(0, count($spe81858) - 1)]; } }