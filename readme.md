## 项目说明
```
phar包没有集成在php命令下,写个脚本简单处理打包/解包.phar文件
```

## 使用示例
**打包:**
```
./phar.php build -s examples/project -d examples/test -m index.php

```

**解包**
```
./phar.php extract -s examples/test.phar -d examples/test
```

**使用包lib**
```
<?php
include("test.phar");
include("phar://test.phar/lib/a.php");
show();
```
