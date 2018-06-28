#!/usr/bin/env php
<?php
/**
 * Phar打包&解包工具
 */
class Phar_Process
{
    private $argv = [];
    private $action_list = ["usage","build","extract"];
    public function __construct($argv)
    {
        if(!class_exists("Phar")) die("PHP版本不支持Phar\r\n");
        $this->argv = $argv;
        $action = isset($argv[1])? $argv[1] : "usage";
        $action = in_array($action,$this->action_list)? $action : "usage";
        call_user_func(array($this,$action));
    }

    private function _getArg($str,$default = null)
    {
        $count = count($this->argv);
        if($count <=2 ) return $default;
        for($i = 1; $i <= $count -1;$i ++)
        {
            if($this->argv[$i] == $str)
            {
                if(isset($this->argv[$i + 1])) return $this->argv[$i + 1];
            }
        }
        return $default;
    }

    private function extract()
    {
        $src = $this->_getArg("-s");
        if(empty($src)) die("-s 指定需要解开的.phar文件\r\n");
        if(!file_exists($src)) die("文件: $src 不存在\r\n");
        $dest = $this->_getArg("-d");
        if(empty($dest)) 
        {
            $dest = basename($src);
            $dest = explode(".",$dest);
            $dest = $dest[0];
        }
        if(is_dir($dest) || file_exists($dest)) die("目录: $dest 已存在\r\n");

        $phar = new Phar($src);  
        $phar->extractTo($dest);
    }

    private function build()
    {
        if(@ini_get("phar.readonly")) die("需要设置php.ini文件phar.readonly=Off;");
        $src = $this->_getArg("-s");
        if(empty($src)) die("-s 指定需要打包的目录\r\n");
        if(!is_dir($src)) die("目录: $src 不存在\r\n");
        $dest = $this->_getArg("-d");
        if(empty($dest)) die("-d 指定需要生成的文件名\r\n");
        $dest .= ".phar";
        if(file_exists($dest)) die("文件: $dest 已存在\r\n");
        $main = $this->_getArg("-m");
        if(empty($main)) $main = "main.php";
        if(!file_exists($src."/".$main)) die("指定的入口文件: $src/$main 不存在\r\n");

        $phar = new Phar($dest,0,$dest);
        $phar->buildFromDirectory($src);
        $phar->compressFiles(Phar::GZ);
        $phar->stopBuffering();
        // $phar->setStub($phar->createDefaultStub($main));
        //$phar->setDefaultStub($main,$main);
        $shebang = "#!/usr/bin/env php";
        $phar->setStub(($shebang ? $shebang . PHP_EOL : "") . $phar->createDefaultStub($main));
    }

    private function usage()
    {
        $s = isset($this->argv[0])? $this->argv[0] : "./phar.php";
        echo "Version 0.01\r\n";
        echo $s," help 帮助\r\n";
        echo $s," build -s <src-dir> -d <dest-name> [-m <src-dir/main.php>] 生成一个.phar包\r\n";
        echo $s," extract -s <src-file> [-d <dest-dir>] 解开一个.phar包\r\n";
    }
}
new Phar_Process($argv);