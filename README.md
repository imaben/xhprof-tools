# xhprof-tools

## 部署方法

### 注入xhprof采样脚本

对于Apache服务器，添加以下代码

```
php_admin_value auto_prepend_file "/path/to/xhprof-tools/prepend.php"
```

对于Nginx服务器，添加以下代码

```
fastcgi_param PHP_VALUE "auto_prepend_file=/path/to/xhprof-tools/prepend.php";
```

也可以直接在php.ini配置文件中增加以下代码

```
auto_prepend_file=/path/to/xhprof-tools/prepend.php
```

以上三种方法选其一即可！

### 采样添加、管理界面部署

将目录直接放在web目录下，访问index.php即可，要`保证根目录或根目录下data.bin文件有可写权限`

### 依赖说明

- `index.php`依赖xhprof_html/js下相关资源文件
- `prepend.php`依赖xhprof_lib下相关文件
