<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/4/25
 * Time: 17:15
 */

namespace James\Uploads;

use Illuminate\Support\Facades\Storage;

class Uploads
{
    /**
     * Notes: 上传图片
     * Date: 2019/4/26 9:36
     * @param $name             文件名
     * @param string $path      路径
     * @param bool $type        是否支持上传服务器，默认不上传
     * @param string $disk      用那种方式上传 oss, cos, qiniu, 又拍云
     * @param array $extension  允许上传的后缀
     * @return array
     */
    public static function image($name, $path = 'uploads', $type = false, $disk = '', $extension = [])
    {
        $allowExtension = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'bmp'
        ];

        if($extension)
            $allowExtension = array_merge($allowExtension, $extension);

        return self::file($name, $path, $type, $disk, $allowExtension);
    }

    /**
     * Notes: 上传文件
     * Date: 2019/4/26 9:48
     * @param $name                 文件名
     * @param string $path          路径
     * @param bool $type            是否支持上传服务器，默认不上传
     * @param string $disk          用那种方式上传 oss, cos, qiniu, 又拍云
     * @param array $allowExtension 允许上传的后缀
     * @return array
     */
    public static function file($name, $path = 'uploads', $type = false, $disk = '', $allowExtension = [])
    {
        if (!request()->hasFile($name))
        {
            return [
                'code'  => 401,
                'msg'   => '上传文件为空！'
            ];
        }

        $file = request()->file($name);
        if(!$file->isValid())
        {
            return [
                'code'  => 401,
                'msg'   => '上传失败，请重试！'
            ];
        }

        // 过滤所有的.符号
        $path = str_replace('.', '', $path);

        // 先去除两边空格
        $path = trim($path, '/');

        // 获取文件后缀
        $extension = strtolower($file->getClientOriginalExtension());

        // 组合新的文件名
        $newName = md5(time()).'.'.$extension;

        // 获取上传的文件名
        $oldName = $file->getClientOriginalName();

        if (!empty($allowExtension) && !in_array($extension, $allowExtension))
        {
            return [
                'code'  => 500,
                'msg'   => $oldName . '的文件类型不被允许'
            ];
        }

        if($type)
        {
            $filename = $path. '/' . $newName;
            if(Storage::disk($disk)->put($filename, file_get_contents($file)))
            {
                return [
                    'code'  => 200,
                    'msg'   => $filename
                ];
            }else{
                return [
                    'code'  => 500,
                    'msg'   => '上传失败'
                ];
            }
        }else{
            $publicPath = public_path($path.'/');
            is_dir($publicPath) || mkdir($publicPath, 0755, true);
            if($file->move($publicPath, $newName))
            {
                return [
                    'code'  => 200,
                    'msg'   => $path.'/'.$newName
                ];
            }else{
                return [
                    'code'  => 500,
                    'msg'   => '保存文件失败'
                ];
            }
        }
    }

    /**
     * Notes: 删除文件
     * Date: 2019/4/28 13:35
     * @param null $path    路径
     * @param bool $type    是否是服务器
     * @param null $disk    储存方式
     * @return array
     */
    public static function delete($path = null, $type = false, $disk = null)
    {
        if(!$path || (!is_file($path) && !$type))
        {
            return [
                'code'  => 401,
                'msg'   => '文件不存在',
            ];
        }

        if(!$type)
        {
            @unlink(public_path($path));
            return [
                'code'  => 200,
                'msg'   => '删除成功',
            ];
        }else{
            $storage = Storage::disk($disk);
            if(!$storage->exists($path)){
                return [
                    'code'  => 401,
                    'msg'   => '文件不存在',
                ];
            }

            if($storage->delete($path))
            {
                return [
                    'code'  => 200,
                    'msg'   => '删除成功',
                ];
            }else{
                return [
                    'code'  => 500,
                    'msg'   => '删除失败',
                ];
            }
        }
    }
}