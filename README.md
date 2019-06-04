这个扩展包用来上传图片、文件

## 上传本地
图片：

    $re = Uploads::image('image', 'image');
    
文件：    
        
    $re = Uploads::file('image', 'file');
    
## 上传服务器
 
图片：

    $re = Uploads::image('image', 'image', true, 'oss');
    
文件：    
        
    $re = Uploads::file('image', 'file', false, '', ['xls', 'xlsx']);

## 参数

     * 第一个参数    文件名(form 表单中的 name)
     * 第二个参数    路径（本地：相对于 /public 目录）
     * 第三个参数    是否支持上传服务器，默认不上传 false
     * 第四个参数    用哪种方式上传 oss, cos, qiniu, 又拍云
     * 第五个参数    允许上传的后缀
