<?php

/**
 * User: LJG
 * Date: 2015/12/10
 * Time: 17:24
 */
class ExcelToArrary  {
	public function __construct() {
		/*导入phpExcel核心类    注意 ：你的路径跟我不一样就不能直接复制*/
		include_once(APPPATH . '/libraries/PHPExcel.php');
	}

	/**
	 * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
	 *以下基本都不要修改
	 */
	public function read($filename) {
        $suffix = substr(strrchr($filename, '.'), 1);
		$inputFileType = PHPExcel_IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		if($suffix=='csv'){
            $objReader->setInputEncoding('gb2312');
		} else {
            $objReader->setReadDataOnly(true);
        }
		$objPHPExcel = $objReader->load($filename);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$excelData = array();
		for ($row = 1; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			}
		}
		return $excelData;
	}
	/* 导出excel函数 暂时没使用*/
	public function push($data,$name='Excel'){

		$objPHPExcel = new PHPExcel();

		/*以下是一些设置 ，什么作者 标题啊之类的*/
		$objPHPExcel->getProperties()->setCreator("测试")
		->setLastModifiedBy("中钛新导入")
		->setTitle("数据EXCEL导出")
		->setSubject("数据EXCEL导出")
		->setDescription("错误信息")
		->setKeywords("excel")
		->setCategory("result file");
		/*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
		foreach($data as $k => $v)
		{
			 $num=$k+1;
			 $objPHPExcel->setActiveSheetIndex(0)
			// //Excel的第A列，uid是你查出数组的键值，下面以此类推
			->setCellValue('A'.$num, $v['手机号'])
			->setCellValue('B'.$num, $v['姓名'])
			->setCellValue('C'.$num, $v['生日'])
			->setCellValue('D'.$num, $v['性别'])
			->setCellValue('F'.$num, $v['错误原因']);
		}

		$objPHPExcel->getActiveSheet()->setTitle('错误导入');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save('php://output');
		$objWriter->save('./uploads/excel/error/'.$name.'.xls');
		return true;
	}
}