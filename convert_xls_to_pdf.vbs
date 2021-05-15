'create the excel object
	Set objExcel = CreateObject("Excel.Application")

	Set objWorkbook = objExcel.Workbooks.Open("C:\OpenServer\domains\bot\storage\app\tmp.xlsx")

'view the excel program and file, set to false to hide the whole process
	objExcel.Visible = True
' msgBox "objExcel.Cells(1,4).Value"

'add a new workbook
''	Set objWorkbook = objExcel.Workbooks.Add

'set a cell value at row 3 column 5
''	objExcel.Cells(3,5).Value = "new value"

'change a cell value
''	objExcel.Cells(3,5).Value = "something different"


'get a cell value and set it to a variable
''	r3c5 = objExcel.Cells(3,5).Value
objWorkbook.ActiveSheet.ExportAsFixedFormat 0, "C:\OpenServer\domains\bot\storage\app\tmp.pdf" ,0, 1, 0,,,0
'save the new excel file (make sure to change the location) 'xls for 2003 or earlier
''	objWorkbook.SaveAs "vbsTest.xlsx"

'close the workbook
	objWorkbook.Close

'exit the excel program
	objExcel.Quit