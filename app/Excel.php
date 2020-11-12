<?php


namespace App;


class Excel {
	public $styleArray = [
		'font' => [
			'bold' => true,
			'color' => [
				'rgb' => 'FFFFFF',
			],
		],
//	'borders' => [
//		'top' => [
//			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//		],
//	],
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//		'rotation' => 90,
			'startColor' => [
				'rgb' => 'FF0000',
			],
//		'endColor' => [
//			'argb' => 'FFFFFFFF',
//		],
		],
	];

	public $string;
	/**
	 * Document properties.
	 *
	 * @var Spreadsheet
	 */
	public $spreadsheet;

	public $cells_format;
	public $classes;

	public function __construct($string, $load) {

		$path = storage_path('app/temp.xls');
		copy('http://rasp.kolledgsvyazi.ru/'. $load, $path);         //загрузка на сервер файла второго корпуса
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');//объект читателя
		$spreadsheet = $reader->load($path);                           //чтение скаченного файла

		$this->string = mb_strtolower($string);
		$this->spreadsheet = $spreadsheet;

		$this->Search();
		$this->Highlight();
	}
	public function Search(){
		$cells_format = [];
		$classes = 0;
		$worksheet = $this->spreadsheet->getActiveSheet();
		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			foreach ($cellIterator as $cell) {
				$value =  $cell->getValue();
				$value = mb_strtolower($value);
				if(stripos($value, $this->string) !== false){
					$row = $cell->getRow();
					$rowUP = $row - 1;
					$cells_format[] = $cell->getColumn() . $rowUP;
					$cells_format[] = $cell->getColumn() . $row;
					$classes++;
				}
			}
		}
		$this->classes = $classes;
		$this->cells_format = $cells_format;
		return $classes;
	}
	public function Highlight(){
		foreach($this->cells_format as $cell){
			$this->spreadsheet->getActiveSheet()->getStyle($cell)
				->applyFromArray($this->styleArray);
		}
	}


	public function getSpreadsheet(){
		return $this->spreadsheet;
	}
	public function getClasses(){
		return $this->classes;
	}
}