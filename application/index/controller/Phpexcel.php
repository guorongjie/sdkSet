<?php
namespace app\index\controller;

use think\Controller;
use Wechat\WechatMenu;
use Wechat\WechatOauth;
use Wechat\WechatUser;

class Phpexcel extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function out()
    {
//        vendor("phpexcel.PHPExcel");//当composer失效时可以使用此方法引入
        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet ->setTitle("demo");//可以给sheet设置名称为"demo"
        $objSheet->setCellValue("A1","姓名")->setCellValue("B1","分数");
        $objSheet->setCellValue("A2","张三")->setCellValue("B2","100");
        $array = array(
            array(),
            array('姓名','分数'),
            array('张三','60'),
            array('李四','61'),
            array('王五','62'),
        );
        $objSheet -> fromArray($array);//数据较大时，不建议使用此方法，建议使用setCellValue()
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');//生成一个Excel2007文件
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
        header('Content-Disposition: attachment;filename="'.'test2.xlsx'.'"');//告诉浏览器将输出文件的名称(文件下载)
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter->save("php://output");
    }
}
