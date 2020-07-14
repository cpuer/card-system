<?php
namespace App\Http\Controllers\Merchant; use App\Library\Response; use App\System; use function GuzzleHttp\Psr7\mimetype_from_filename; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use Illuminate\Support\Facades\Auth; use Illuminate\Support\Facades\Storage; class File extends Controller { public static function uploadImg($spe26b52, $sp699450, $sp2759c6, $spb370bf = false) { try { $sp82245f = $spe26b52->extension(); } catch (\Throwable $sp7900a2) { return Response::fail($sp7900a2->getMessage()); } if (!$spe26b52 || !in_array(strtolower($sp82245f), array('jpg', 'jpeg', 'png', 'gif'))) { return Response::fail('图片错误, 系统支持jpg/png/gif格式'); } if ($spe26b52->getSize() > 5 * 1024 * 1024) { return Response::fail('图片不能大于5MB'); } try { $sp618c15 = $spe26b52->store($sp2759c6, array('disk' => System::_get('storage_driver'))); } catch (\Exception $sp7900a2) { \Log::error('File.uploadImg folder:' . $sp2759c6 . ', error:' . $sp7900a2->getMessage(), array('exception' => $sp7900a2)); if (config('app.debug')) { return Response::fail($sp7900a2->getMessage()); } else { return Response::fail('上传文件失败, 内部错误, 请联系客服'); } } if (!$sp618c15) { return Response::fail('系统保存文件出错, 请稍后再试'); } $spafe885 = System::_get('storage_driver'); $sp69c1d6 = Storage::disk($spafe885)->url($sp618c15); $sp77b581 = \App\File::insertGetId(array('user_id' => $sp699450, 'driver' => $spafe885, 'path' => $sp618c15, 'url' => $sp69c1d6)); if ($sp77b581 < 1) { Storage::disk($spafe885)->delete($sp618c15); return Response::fail('数据库繁忙，请稍后再试'); } $spa87332 = array('id' => $sp77b581, 'url' => $sp69c1d6, 'name' => pathinfo($sp618c15, PATHINFO_BASENAME)); if ($spb370bf) { return $spa87332; } return Response::success($spa87332); } function upload_merchant(Request $sp26e527) { $sp590011 = $this->getUser($sp26e527); if ($sp590011 === null) { return Response::forbidden('无效的用户'); } $spe26b52 = $sp26e527->file('file'); return $this->uploadImg($spe26b52, $sp590011->id, \App\File::getProductFolder()); } public function renderImage(Request $sp26e527, $sp24ebc1) { if (str_contains($sp24ebc1, '..') || str_contains($sp24ebc1, './') || str_contains($sp24ebc1, '.\\') || !starts_with($sp24ebc1, 'images/')) { $spe6f6ab = file_get_contents(public_path('images/illegal.jpg')); } else { $sp24ebc1 = str_replace('\\', '/', $sp24ebc1); $spe26b52 = \App\File::wherePath($sp24ebc1)->first(); if ($spe26b52) { $spafe885 = $spe26b52->driver; } else { $spafe885 = System::_get('storage_driver'); } if (!in_array($spafe885, array('local', 's3', 'oss', 'qiniu'))) { return response()->view('message', array('title' => '404', 'message' => '404 Driver NotFound'), 404); } try { $spe6f6ab = Storage::disk($spafe885)->get($sp24ebc1); } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $sp7900a2) { \Log::error('File.renderImage error: ' . $sp7900a2->getMessage(), array('exception' => $sp7900a2)); return response()->view('message', array('title' => '404', 'message' => '404 NotFound'), 404); } } ob_end_clean(); header('Content-Type: ' . mimetype_from_filename($sp24ebc1)); die($spe6f6ab); } }