<?php

/**
* img.php?n=none.png&s=10,10&c=200,0,0
* n 目标文件，会被替换为 当前 php 文件目录下 img/none.png
* s 图片大小，默认为 10*10
* c 图片颜色，默认为 灰色
* t 图片类型，1 为圆型，默认为矩形
*/
error_reporting(E_ERROR);

$size = $_GET['s']; //  大小
$color = $_GET['c'];    //  颜色
$style = $_GET['t'];    //  风格
$name = str_replace(array('../','//'), '', trim($_GET['n']) ); //  原图

if( $size ){
    $size = explode(',', $_GET['s'] );
}else{
    $size = array(10,10);
}

if( $color ){
    $color = explode(',', $_GET['c'] );
}else{
    $color = array(200,200,200);
}
$ext = array_pop(explode('.', $name ));
if( !in_array($ext, array('png','jpg','jpeg','gif'))){
    die('err');
}
header("Content-type: image/png"); 
if( $path = realpath(dirname(__FILE__).'/img/'.$name) ){
    echo file_get_contents( $path );
    return false;
}else{
    

    $png = imagecreatetruecolor($size[0], $size[1]);
    imagesavealpha($png, true);

    $trans_colour = imagecolorallocatealpha($png, 0, 0, 0, 127);
    imagefill($png, 0, 0, $trans_colour);

    $red = imagecolorallocate($png, $color[0], $color[1], $color[2]);

    if(  $style == '1' ){
        #   圆形
        imagefilledarc($png, $size[0]/2, $size[1]/2, $size[0]-1, $size[1]-1, 0, 360 , $red, IMG_ARC_PIE);
    }else{
        imagefill($png, 0, 0, $red);
    }

    // if( $_GET['txt'] ){

    //     imagestring($png, 1, 5, 5,  $_GET['txt'] , $text_color);
    // }
    
    
    header("Content-type: image/png");
    imagepng($png);
}