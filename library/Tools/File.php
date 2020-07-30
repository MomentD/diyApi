<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:55
 */

namespace Library\Tools;
/**
 * 文件助手类
 * Class File
 * 参考 vendor\easyswoole\utility\src\File.php
 * @author  : evalor <master@evalor.cn>
 * @package Library\Tools
 */
class File
{
    /**
     * 创建目录
     * @param string  $dirPath     需要创建的目录
     * @param integer $permissions 目录权限
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function createDirectory( $dirPath , $permissions = 0755 )
    {
        if (!is_dir( $dirPath ))
        {
            try
            {
                return mkdir( $dirPath , $permissions , true ) && chmod( $dirPath , $permissions );
            }
            catch (\Throwable $throwable)
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * 清空一个目录
     * @param string $dirPath       需要创建的目录
     * @param bool   $keepStructure 是否保持目录结构
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function cleanDirectory( $dirPath , $keepStructure = false )
    {
        $scanResult = static::scanDirectory( $dirPath );
        if (!$scanResult)
            return false;
        try
        {
            foreach ( $scanResult['files'] as $file )
                unlink( $file );
            if (!$keepStructure)
            {
                krsort( $scanResult['dirs'] );
                foreach ( $scanResult['dirs'] as $dir )
                    rmdir( $dir );
            }
            return true;
        }
        catch (\Throwable $throwable)
        {
            return false;
        }
    }

    /**
     * 删除一个目录
     * @param $dirPath
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function deleteDirectory( $dirPath )
    {
        $dirPath = realpath( $dirPath );
        if (!is_dir( $dirPath ))
            return false;
        if (!static::cleanDirectory( $dirPath ))
            return false;
        return rmdir( realpath( $dirPath ) );
    }

    /**
     * 复制目录
     * @param string $source    源位置
     * @param string $target    目标位置
     * @param bool   $overwrite 是否覆盖目标文件
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function copyDirectory( $source , $target , $overwrite = true )
    {
        $scanResult = static::scanDirectory( $source );
        if (!$scanResult)
            return false;
        if (!is_dir( $target ))
            self::createDirectory( $target );
        try
        {
            $sourceRealPath = realpath( $source );
            foreach ( $scanResult['files'] as $file )
            {
                $targetRealPath = realpath( $target ) . '/' . ltrim( substr( $file , strlen( $sourceRealPath ) ) , '/' );
                static::copyFile( $file , $targetRealPath , $overwrite );
            }
            return true;
        }
        catch (\Throwable $throwable)
        {
            return false;
        }
    }

    /**
     * 移动目录到另一位置
     * @param string $source    源位置
     * @param string $target    目标位置
     * @param bool   $overwrite 是否覆盖目标文件
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function moveDirectory( $source , $target , $overwrite = true )
    {
        $scanResult = static::scanDirectory( $source );
        if (!$scanResult)
            return false;
        if (!is_dir( $target ))
            self::createDirectory( $target );
        try
        {
            $sourceRealPath = realpath( $source );
            foreach ( $scanResult['files'] as $file )
            {
                $targetRealPath = realpath( $target ) . '/' . ltrim( substr( $file , strlen( $sourceRealPath ) ) , '/' );
                static::moveFile( $file , $targetRealPath , $overwrite );
            }
            static::deleteDirectory( $sourceRealPath );
            return true;
        }
        catch (\Throwable $throwable)
        {
            return false;
        }
    }

    /**
     * 复制文件
     * @param string $source    源位置
     * @param string $target    目标位置
     * @param bool   $overwrite 是否覆盖目标文件
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function copyFile( $source , $target , $overwrite = true )
    {
        return self::checkSourceCanOverWrite( $source , $target , $overwrite ) ? copy( $source , $target ) : false;
    }

    /**
     * 创建一个空文件
     * @param $filePath
     * @param $overwrite
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function touchFile( $filePath , $overwrite = true )
    {
        if (file_exists( $filePath ) && $overwrite == false)
        {
            return false;
        }
        elseif (file_exists( $filePath ) && $overwrite == true)
        {
            if (!unlink( $filePath ))
            {
                return false;
            }
        }
        $aimDir = dirname( $filePath );
        if (self::createDirectory( $aimDir ))
        {
            try
            {
                return touch( $filePath );
            }
            catch (\Throwable $throwable)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 创建一个有内容的文件
     * @param      $filePath
     * @param      $content
     * @param bool $overwrite
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function createFile( $filePath , $content , $overwrite = true )
    {
        if (static::touchFile( $filePath , $overwrite ))
        {
            return (bool)file_put_contents( $filePath , $content );
        }
        else
        {
            return false;
        }
    }

    /**
     * 移动文件到另一位置
     * @param string $source    源位置
     * @param string $target    目标位置
     * @param bool   $overwrite 是否覆盖目标文件
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function moveFile( $source , $target , $overwrite = true )
    {
        return self::checkSourceCanOverWrite( $source , $target , $overwrite ) ? rename( $source , $target ) : false;
    }

    /**
     * 校验文件资源是否可以进行操作（覆盖），重命名，复制等操作
     * @param string $source    源位置
     * @param string $target    目标位置
     * @param bool   $overwrite 是否覆盖目标文件
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    static function checkSourceCanOverWrite( $source , $target , $overwrite = true )
    {
        $result = true;
        if (!file_exists( $source ))
        {
            $result = false;
        }
        if (file_exists( $target ) && $overwrite == false)
        {
            $result = false;
        }
        elseif (file_exists( $target ) && $overwrite == true)
        {
            if (!unlink( $target ))
            {
                $result = false;
            }
        }
        $targetDir = dirname( $target );
        if (!self::createDirectory( $targetDir ))
        {
            $result = false;
        }
        return $result;
    }

    /**
     * 遍历目录
     * @param string $dirPath
     * @return array|bool
     * @author : evalor <master@evalor.cn>
     */
    static function scanDirectory( $dirPath )
    {
        if (!is_dir( $dirPath ))
            return false;
        $dirPath = realpath( $dirPath ) . '/';
        $dirs = array ( $dirPath );
        $fileContainer = array ();
        $dirContainer = array ();
        try
        {
            do
            {
                $workDir = array_pop( $dirs );
                $scanResult = scandir( $workDir );
                foreach ( $scanResult as $files )
                {
                    if ($files == '.' || $files == '..')
                        continue;
                    $realPath = $workDir . $files;
                    if (is_dir( $realPath ))
                    {
                        array_push( $dirs , $realPath . '/' );
                        $dirContainer[] = $realPath;
                    }
                    elseif (is_file( $realPath ))
                    {
                        $fileContainer[] = $realPath;
                    }
                }
            } while ($dirs);
        }
        catch (\Throwable $throwable)
        {
            return false;
        }
        return [ 'files' => $fileContainer , 'dirs' => $dirContainer ];
    }
}