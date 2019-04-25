<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/4/25
 * Time: 17:15
 */

namespace James\Uploads;

class Uploads
{
    /**
     * Notes: 上传图片
     * Date: 2019/4/25 17:16
     * @param $name         文件名
     * @param string $path  路径
     */
    public static function image($name, $path = 'uploads', $extension = [])
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

        return self::file($name, $path, $allowExtension);
    }


    /**
     * Notes:
     * Date: 2019/4/25 17:24
     * @param $name             文件名
     * @param string $path      路径
     * @param array $allowExtension  后缀
     * @return array
     */
    public static function file($name, $path = 'uploads', $allowExtension = [])
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

        $path = str_replace('.', '', $path);
        $path = trim($path, '/');
        $publicPath = public_path($path.'/');
        is_dir($publicPath) || mkdir($publicPath, 0755, true);

        $oldName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!empty($allowExtension) && !in_array($extension, $allowExtension))
        {
            return [
                'code'  => 500,
                'msg'   => $oldName . '的文件类型不被允许'
            ];
        }

        $newName = md5(time()).'.'.$extension;
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